<?php

require 'core/init.php';

$user = new User();
$user->checkIsValidUser('teacher');
$difficulty = new Difficulty();
$difficulties = $difficulty->getDifficulties();
$topic = new Topic();
$topics = $topic->getTopics();
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
  <title>LSAT | Crear pregunta</title>
  <?php include 'includes/templates/headTags.php' ?>
  <link rel="stylesheet" href="css/jquery.wysiwyg.css" type="text/css"/>
</head>

<body>

  <?php include 'includes/templates/header.php' ?>

  <section class="scroll-container" role="main">

    <div class="row">
      <?php include 'includes/templates/teacherSidebar.php' ?>
      <div class="large-9 medium-8 columns">
        <br/>
        <h3>Crear nueva pregunta</h3>
        <hr>

        <form id="newQuestion">

          <div class="row">
            <div class="large-12 columns">
              <label>Texto de la pregunta
                <textarea id="qtext" name="text" style="width:100%; height: 200px;"></textarea>
              </label>
            </div>
          </div>

          <div class="row">
            <div class="large-12 columns">
              <label>Url media
                <input type="text" id="qurl" name="url" placeholder="URL de una imagen o video que ayude a explicar la pregunta" />
              </label>
            </div>
          </div>

          <div class="row">
            <div class="large-6 columns">
              <label>Dificultad
                <select id="qgrade" name="grade">
                  <?php
                  foreach ($difficulties as $item) {
                    echo "<option value='$item->id'>$item->name</option>";
                  }
                  ?>
                </select>
              </label>
            </div>

            <div class="large-6 columns">
              <label>Tema
                <select id="qtopic" name="topic">
                  <?php
                  foreach ($topics as $item) {
                    echo "<option value='$item->id'>$item->name</option>";
                  }
                  ?>
                </select>
              </label>
            </div>
          </div>

          <hr>

          <h4>Respuestas</h4>

          <div class="row correctAns">
            <div class="large-6 columns">
              <label>Respuesta 1 - CORRECTA <textarea  name="ans1"></textarea> </label>
            </div>

            <div class="large-6 columns">
              <label>Feedback <textarea  name="feed1"></textarea> </label>
            </div>

            <div class="large-6 columns">
              <label>URL <input type="text" name="urla1" placeholder="URL de una imagen o video que complemente la respuesta" />  </label>
            </div>

            <div class="large-6 columns">
              <label>URL feedback <input type="text" name="urlf1" placeholder="URL de una imagen o video que complemente el feedback" />  </label>
            </div>

          </div>

          <div class="row grey1">
            <div class="large-6 columns">
              <label>Respuesta 2 <textarea  name="ans2"></textarea> </label>
            </div>

            <div class="large-6 columns">
              <label>Feedback <textarea  name="feed2"></textarea> </label>
            </div>

            <div class="large-6 columns">
              <label>URL <input type="text" name="urla2"/>  </label>
            </div>

            <div class="large-6 columns">
              <label>URL feedback <input type="text" name="urlf2" />  </label>
            </div>

          </div>


          <div class="row grey2">
            <div class="large-6 columns">
              <label>Respuesta 3 <textarea  name="ans3"></textarea> </label>
            </div>

            <div class="large-6 columns">
              <label>Feedback <textarea  name="feed3"></textarea> </label>
            </div>

            <div class="large-6 columns">
              <label>URL <input type="text" name="urla3" />  </label>
            </div>

            <div class="large-6 columns">
              <label>URL feedback <input type="text" name="urlf3" />  </label>
            </div>

          </div>

          <div class="row grey1">
            <div class="large-6 columns">
              <label>Respuesta 4 <textarea  name="ans4"></textarea> </label>
            </div>

            <div class="large-6 columns">
              <label>Feedback <textarea  name="feed4"></textarea> </label>
            </div>

            <div class="large-6 columns">
              <label>URL <input type="text" name="urla4" />  </label>
            </div>

            <div class="large-6 columns">
              <label>URL feedback <input type="text" name="urlf4" />  </label>
            </div>

          </div>

          <br/>

          <a href="#" onclick="createQuestion()" class="button round small right">Crear</a>

        </form>

      </div>
    </div>
  </section>


  <?php include 'includes/templates/footer.php' ?>


  <script src="js/vendor/jquery.js"></script>
  <script src="js/foundation.min.js"></script>

  <script type="text/javascript" src="js/jquery.wysiwyg.js"></script>
  <script type="text/javascript" src="js/controls/wysiwyg.image.js"></script>
  <script type="text/javascript" src="js/controls/wysiwyg.link.js"></script>
  <script type="text/javascript" src="js/controls/wysiwyg.table.js"></script>

  <script type="text/javascript">

    (function ($) {
      $(document).ready(function () {
        $('#qtext').wysiwyg({autoGrow: true, maxHeight: 400, autoSave:true, initialContent: "" });
      });
    })(jQuery);


  </script>
  <script>
    $(document).foundation();

    function createQuestion(){

      var fields = $("#newQuestion").serializeArray();
//      console.log(fields);

      var topic  = $("#qtopic").val();
      var grade  = $("#qgrade").val();
      var url    = $("#qurl").val();
      var text   = $("#qtext").val();
      console.log(text);

      var len = fields.length,
      dataObj = {};

      for (i=0; i<len; i++) {
        dataObj[fields[i].name] = fields[i].value;
      }

      var data = JSON.stringify(dataObj);
      console.log(data);


      $.post( "controls/doAction.php", {  action: "createQuestion", data: data})
      .done(function( data ) {

        data = JSON.parse(data);
        if(data.message == 'success'){
          alert("La pregunta fue creada");
          window.location.reload();
        }else{
          alert("Error: \n\n" + data.message);
        }

      });

}

</script>
</body>
</html>
