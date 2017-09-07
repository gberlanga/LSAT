<?php

require 'core/init.php';

$user = new User();
$user->checkIsValidUser('admin');

?>

<!doctype html>
<html class="no-js" lang="en">
<head>
  <title>LSAT | Registrar maestro</title>
  <?php include 'includes/templates/headTags.php' ?>
</head>

<body>

  <?php include 'includes/templates/header.php' ?>

  <section class="scroll-container" role="main">

    <div class="row">
      <?php include 'includes/templates/adminSidebar.php' ?>  
      <div class="large-9 medium-8 columns">
        <br/>
        <h3>Registrar nuevo maestro</h3>
        <h4 class="subheader"> Ten cuidado de no registrar profesores duplicados.</h4>
        <hr>  
        <div id="">
        Nombre:
         <input id="username" type="text">
         Mail:
         <input id="mail" type="text">
         Nómina / Matrícula:
         <input id="idnumber" type="text">
         <a href="#" onclick="registerTeacher()" class="button tiny right">Registrar</a>
       </div>

     </div>
   </div>
 </section>


 <?php include 'includes/templates/footer.php' ?>


 <script src="js/vendor/jquery.js"></script>
 <script src="js/foundation.min.js"></script>
 <script>
  $(document).foundation();

  function registerTeacher(){
    var username  = $("#username").val().trim();
    var mail      = $("#mail").val().trim();
    var idnumber  = $("#idnumber").val().trim();

    if(username == "" || mail == "" || idnumber == ""){
      alert("No puedes dejar campos vacíos.");
      return;
    }

    $.post( "controls/doAction.php", { action:"registerTeacher", username: username, mail: mail, idnumber: idnumber })
    .done(function( data ) {
      data = JSON.parse(data);
      if(data.message == 'success'){
        window.location.replace('./manageTeachers.php');
      }else{
        alert("Error: " + data.message);
      }

    });
  }


</script>
</body>
</html>
