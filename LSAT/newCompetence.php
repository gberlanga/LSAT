<?php

require 'core/init.php';

$user = new User();
$user->checkIsValidUser('teacher');
$teacherId = $user->data()->id;
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
  <title>LSAT | Nueva competencia</title>
  <?php include 'includes/templates/headTags.php' ?>
</head>

<body>

  <?php include 'includes/templates/header.php' ?>

  <section class="scroll-container" role="main">

    <div class="row">
      <?php include 'includes/templates/teacherSidebar.php' ?>
      <div class="large-9 medium-8 columns">
        <br/>
        <h3>Nueva competencia</h3>
        <h4 class="subheader">Crear una nueva competencia reuniendo varias redes de aprendizaje</h4>
        <hr>

        <form id="newCompetence">

          <div class="row">
            <label>Nombre de la competencia<input type="text" name="name" id="name"/></label>

            <h5>A continuacion, escribe los ids de las redes que formaran la competencia.</h5>
			<ol>
			<li> <input id="web1" type="text"/> </li>
			<li> <input id="web2" type="text"/> </li>
			<li> <input id="web3" type="text"/> </li>
			<li> <input id="web4" type="text"/> </li>
			<li> <input id="web5" type="text"/> </li>
			</ol>
          </div>

        </form>

        <a onclick="createCompetence()" class="button round small right">Crear</a>

      </div>
    </div>
  </section>



  <?php include 'includes/templates/footer.php' ?>

  <script src="js/vendor/jquery.js"></script>
  <script src="js/foundation.min.js"></script>

  <script>
    $(document).foundation();



    function createCompetence(){
      var name = $("input#name").val();
      var ids = [];
      ids[0] = $("input#web1").val();
      ids[1] = $("input#web2").val();
      ids[2] = $("input#web3").val();
      ids[3] = $("input#web4").val();
      ids[4] = $("input#web5").val();

      $.post( "controls/doAction.php", {  action: "createCompetence", name: name, webIds:ids})
      .done(function( data ) {

        data = JSON.parse(data);
        console.log(data);
        if(data.message == 'success'){
          //Llevar al explorador de la red para mostrar detalle de la red creada
          window.location.replace('./competenceDetail.php?competence='+data.response);
        }else{
          alert("Error: \n\n" + data.message);
      }
    });
  }

  </script>
</body>
</html>
