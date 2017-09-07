<?php

require 'core/init.php';

$user = new User();
$user->checkIsValidUser('teacher');

?>

<!doctype html>
<html class="no-js" lang="en">
<head>
  <title>LSAT | New Group</title>
  <?php include 'includes/templates/headTags.php' ?>
</head>

<body>

  <?php include 'includes/templates/header.php' ?>

  <section class="scroll-container" role="main">

    <div class="row">
      <?php include 'includes/templates/teacherSidebar.php' ?>  
      <div class="large-9 medium-8 columns">
        <h3>Grupo</h3>
        <h4 class="subheader">Crear nuevo grupo</h4>
        <hr>  

        <form> 
          <div class="row"> 
            <div class="large-4 columns"> 
            <label>Nombre del grupo <input id="groupname" type="text" placeholder="TC-0001" /> </label> 
            </div>
          </div>
          <div class="row"> 
            <div class="large-12 columns"> 
              <label>Alumnos<input id="students" type="text" placeholder="Matrículas de alumnos separadas por comas... A012345, A02389"/> </label>
            </div> 
          </div>  
          <a href="#" onclick="createGroup()" class="button round small right">Crear</a>
        </form>

      </div>
    </div>
  </section>


  <?php include 'includes/templates/footer.php' ?>

  <script src="js/vendor/jquery.js"></script>
  <script src="js/foundation.min.js"></script>

  <script>
    $(document).foundation();

    function createGroup(){
      var groupname  = $("#groupname").val();
      var students   = $("#students").val();

      $.post( "controls/doAction.php", { action:"createGroup", groupname: groupname, students: students})
      .done(function( data ) {

        data = JSON.parse(data);
        if(data.message == 'success'){
          window.location.replace('./groups.php');
        }else{
          alert("Error: \n\n" + data.message);
        }

      });
    }

  </script>
</body>
</html>
