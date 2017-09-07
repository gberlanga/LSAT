<?php

require 'core/init.php';

$user = new User();
$user->checkIsValidUser('teacher');
$teacherId = $user->data()->id;
$difficulty = new Difficulty();
$difficulties = $difficulty->getDifficulties();
$topic = new Topic();
$topics = $topic->getTopics();
$webId = trim(Input::get("web"));

if ($webId != ''){
  $w = new Web();
  $web = $w->getWebIfValidAndEditable($webId);

  if ($web == false) {
    Redirect::to('webs.php');
  }
}

?>

<!doctype html>
<html class="no-js" lang="en">
<head>
  <title>LSAT | Nueva red</title>
  <?php include 'includes/templates/headTags.php' ?>
</head>

<body>

  <?php include 'includes/templates/header.php' ?>

  <section class="scroll-container" role="main">

    <div class="row">
      <?php include 'includes/templates/teacherSidebar.php' ?>
      <div class="large-9 medium-8 columns">
        <br/>
        <?php
        if (isset($web)) {
          echo "<h3>Editar red</h3>";
        }else{
          echo "<h3>Nueva red</h3><h4 class='subheader'>Crear una nueva red de aprendizaje</h4>";
        }
        ?>

        <hr>

        <form id="newWeb">

          <div class="row">
            <label>Nombre de la red <input type="text" name="name" id="name" value="<?php
              if (isset($web)) {
                echo "$web->name";
              }else{
                echo "";
              }
              ?>"/></label>

              <div id="weblevels" class="weblevels">
                <ul class="">
                  <li class="level1" onclick="changeLevel(1)"> <h5>Nivel 1</h5> </li>
                  <li class="level2" onclick="changeLevel(2)"> <h5>Nivel 2</h5> </li>
                  <li class="level3" onclick="changeLevel(3)"> <h5>Nivel 3</h5> </li>
                  <li class="addLevel level10" onclick="addLevel()"> <h5> + </h5> </li>
                </ul>
              </div>

              <div id="webStructure" class="webStructure level1">

                <div id="questionFilter" class="questionFilter">

                  <div id="filter">
                    <div class="component">
                      Tema
                      <select id="topic" name="topic">
                        <?php
                        foreach ($topics as $item) {
                          echo "<option value='$item->id'>$item->name</option>";
                        }
                        ?>
                      </select>
                    </div>

                    <div class="component">
                      Dificultad
                      <select id="difficulty" name="difficulty">
                        <?php
                        foreach ($difficulties as $item) {
                          echo "<option value='$item->id'>$item->name</option>";
                        }
                        ?>
                      </select>
                    </div>

                    <a href="#" onclick="filterQuestions()" class="button tiny btn">Get</a>
                  </div>

                  <div id="questionsForLevel">
                    <div>
                      <h6>Preguntas seleccionadas</h6>
                      <ul>
                      </ul>
                    </div>
                  </div>

                </div>

                <div id="searchResults" class="searchResults">

                  <div id="noQuestions" class="noQuestions">
                    No has buscado ninguna pregunta
                  </div>

                  <table class="results">
                    <thead>
                      <tr>
                        <th width="700">Texto Pregunta</th>
                        <th width="80">Agregar</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>

                  <div id="questionModal" class="reveal-modal medium" data-reveal>

                    <p id="qText"></p>
                    <img id="qImage" src=""/>

                    <div class="ans">
                      <table>
                        <thead>
                          <tr>
                            <th width="400px">Respuesta</th>
                            <th width="400px">Feedback</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>

                    <a class="close-reveal-modal">&#215;</a>
                  </div>

                </div>

                <div style="clear: both;"></div>
              </div>
            </div>

          </form>

          <a href="#" onclick="saveWeb()" class="button round small right">Guardar</a>
          <a href="#" onclick="publishWeb()" class="button round small right alerta">Publicar</a>

        </div>
      </div>
    </section>

    <?php include 'includes/templates/footer.php' ?>
    <script src="js/vendor/jquery.js"></script>
    <script src="js/foundation.min.js"></script>

    <script>
      $(document).foundation();

      var webId = <?php
      if (isset($web)) {
        echo "$webId";
      }else{
        echo "0";
      }
      ?>;

      var currentLevel = 1;
      var maxLevels = 10;
      var nextLevel = 4;
      var questionsForLevel = [[],[],[],[],[],[],[],[],[],[]]; /*Maximo de 10 niveles*/
      var usedQuestions = [];
      var webStructure = $("#webStructure");
      var weblevels = $("#weblevels ul");
      var addNewLi = $(".addLevel");
      var noQustions = $(".noQuestions");
      var resultsTable = $("table.results");
      var questionsForLevelUl = $("#questionsForLevel ul");
      var questionLiTemplate = "<li> <a class='delete' onclick='deleteQuestion($id)'> X </a> <a class='number' onclick='showQuestion($id)'>$number</a></li>";

      var questionModal = $("#questionModal");
      var qText = $("#questionModal #qText");
      var qImage = $("#questionModal #qImage");

      if (webId != 0) {
      //Vamos a editar la red
      $.post( "controls/doAction.php", {  action: "getWebElementsForEdition", webId: webId})
      .done(function( data ) {
        data = JSON.parse(data);

        console.log(data);
        for (var questionId in data) {
          if (data.hasOwnProperty(questionId)) {
            var level = parseInt(data[questionId]);
            questionsForLevel[level-1].push(parseInt(questionId));
          }
        }
        refreshLis();

      });
    }

    function addLevel(){
      if(nextLevel > maxLevels) return;
      var li = "<li class='level"+nextLevel
      +"' onclick='changeLevel("+nextLevel+")'> <h5>Nivel "+nextLevel+"</h5></li>";
      addNewLi.before(li);
      nextLevel++;
    }

    function changeLevel(level){
      var i;
      for(var i=1; i<=maxLevels; i++){
        var l = "level"+i;
        webStructure.removeClass(l);
      }

      webStructure.addClass("level"+level);
      currentLevel = level;
      refreshLis();
    }

    function addQuestion(id){
      if($.inArray(id, usedQuestions) == -1){
        //console.log(id);
        questionsForLevel[currentLevel-1].push(id);
        usedQuestions.push(id);
        addQuestionLi(id);
      }
      else{
        alert("La pregunta ya fue usada en otro nivel");
      }
    }

    function addQuestionLi(id){
      var t = questionLiTemplate;
      var li = t.replace('$id', id);
      li = li.replace('$id', id);
      li = li.replace("$number", questionsForLevel[currentLevel-1].length);
      questionsForLevelUl.append(li);
    }

    function refreshLis(){
      var t = questionLiTemplate;
      var len = questionsForLevel[currentLevel-1].length;
      console.log("len" + len);
      questionsForLevelUl.empty();

      for(var i=0; i<len; i++){
        console.log(i);
        var id = questionsForLevel[currentLevel-1][i];
        var li = t.replace('$id', id);
        li = li.replace('$id', id);
        li = li.replace("$number", i+1);
        questionsForLevelUl.append(li);

      }
    }

    function showQuestion(id){
      var template =  "<tr> <td> <p> $text </p> <img style='display:$dA' src='$imgA' /> </td> <td> <p> $feedback </p>  <img style='display:$dF' src='$imgF' /> </td> </tr>";

      $.post( "controls/doAction.php", {  action: "getQuestion", id: id})
      .done(function( data ) {

        data = JSON.parse(data);
        if(data.message == 'error'){
          alert("Error: \n\n" + data.message);
        }else{
          /*Llenar el contenedor con los datos de la pregunta*/
          console.log(data);
          qImage.show();
          qText.html(data['text']);
          qImage.attr("src", data['urlImage']);
          if(data['urlImage'] == ""){
            qImage.hide();
          }

          var tbody = $("#questionModal .ans tbody");
          tbody.empty();

          for(i=0; i<4; i++){

            var t = template;
            t = t.replace("$text", data[i].text);
            t = t.replace("$imgA", data[i].urlImage);
            t = t.replace("$feedback", data[i].textFeedback);
            t = t.replace("$imgF", data[i].imageFeedback);

            if(data[i].urlImage == ""){
              t = t.replace("$dA", 'none');
            }else{
              t = t.replace("$dA", 'block');
            }

            if(data[i].imageFeedback == ""){
              t = t.replace("$dF", 'none');
            }else{
              t = t.replace("$dF", 'block');
            }

            tbody.append(t);
          }

          $('#questionModal').foundation('reveal', 'open');

        }
      });
}

function createWeb(isPublished){
  var name = $("input#name").val().trim();

  if(name == ""){
    alert("Por favor asigne un nombre a la red.");
    return;
  }

  $.post( "controls/doAction.php", {  action: "createWeb", webId:webId, name: name, questionsForLevel:questionsForLevel, isPublished: isPublished})
  .done(function( data ) {

    data = JSON.parse(data);
    if(data.message == 'error'){
      alert("Error: \n\n" + data.message);
    }else{
          //Llevar al explorador de la red para mostrar detalle de la red creada
          window.location.replace('./webs.php');
        }
      });
}

function saveWeb(){
  createWeb(0);
}

function publishWeb(){
  var r = confirm("Estas seguro que deseas publicar la red?");
  if (r == true) {
    createWeb(1);
  }
}

function deleteQuestion(id){
  console.log("deleteQuestion"+id);
  var arr = questionsForLevel[currentLevel-1];
      //Le quita al arreglo el id de la pregunta que queremos eliminar
      arr = $.grep(arr, function(value) {
        return value != id;
      });

      questionsForLevel[currentLevel-1] = arr;

      //Eliminarla tambien de la lista de preguntas usadas
      usedQuestions = $.grep(usedQuestions, function(value){
        return value != id;
      });

      console.log(arr);
      console.log(questionsForLevel[currentLevel-1]);

      refreshLis();
    }

    function filterQuestions(){

      var topic  = $("#topic").val();
      var difficulty  = $("#difficulty").val();
      var template =  "<tr id='id'> <td> $text </td><td> <a onclick='addQuestion($id);' class='tiny button secondary'>Agregar</a> </td> </tr>";

      $.post( "controls/doAction.php", {  action: "filterQuestions",
        topic: topic,
        difficulty: difficulty})

      .done(function( data ) {
      //console.log(data);

      data = JSON.parse(data);
      if(data.message == 'error'){
        alert("Error: \n\n" + data.message);
      }else{
        //Llenar el contenedor con las preguntas
        //console.log(data);

        noQustions.hide();
        resultsTable.show();

        var i;
        var tbody = $("table.results tbody");
        tbody.empty();
        for(i=0; i<data.length; i++){
          var t = template;
          //console.log(t);
          t = t.replace("$text", data[i].text);
          t = t.replace("$id", data[i].id);
          tbody.append(t);

        }
      }

    });
    }

  </script>
</body>
</html>
