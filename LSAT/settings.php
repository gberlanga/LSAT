<?php
require 'core/init.php';

$user = new User();
$user->checkIsValidUser();

?>

<!doctype html>
<html class="no-js" lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>LSAT | Mi información </title>
  <link rel="stylesheet" href="css/foundation.css" />
  <link rel="stylesheet" href="css/lsat.css" />
  <script src="js/vendor/modernizr.js"></script>
</head>

<body>

  <?php include 'includes/templates/header.php' ?>

  <section class="scroll-container" role="main">

    <div class="row">

      <?php 

      if($user->data()->role == 'admin'){
        include 'includes/templates/adminSidebar.php';
      }else if($user->data()->role == 'teacher'){
        include 'includes/templates/teacherSidebar.php';  
      }else  if($user->data()->role == 'student'){
        include 'includes/templates/studentSidebar.php';
      }
      
      ?>  

      <div class="large-9 medium-8 columns">
       <br/>
       <h3>Ver y editar mi información</h3>
       <hr/>

       <h5>Username</h5>
       <input id="username" type="text" value="<?php echo $user->data()->username; ?>"> <br/>
       <h5>Mail</h5>
       <input id="mail" type="text" value="<?php echo $user->data()->mail;  ?>">
       <h5>Nueva contraseña</h5>
       <input id="password" pattern=".{6,}" type="password">
       <p class="grey">Por seguridad no mostramos tu antigua contraseña, si la quieres cambiar escribe una nueva. Si dejas el campo vacio tu contraseña no sera cambiada.</p>
       <a href="#" onclick="updateSettings()" class="button small right">Guardar cambios</a>

     </div>
   </div>
 </section>

 <script src="js/vendor/jquery.js"></script>
 <script src="js/foundation.min.js"></script>
 <script>
  $(document).foundation();
</script>

<script>

 function updateSettings(){
  var username  = $("#username").val();
  var mail      = $("#mail").val();
  var password  = $("#password").val();

  $.post( "controls/doAction.php", { action:"updateSettings", username: username, mail: mail, password:password })
  .done(function( data ) {

    data = JSON.parse(data);
    if(data.message == 'success'){
      window.location.reload();
    }else{
      alert("There was an error: " + data.message);
    }

  });
}

</script>
</body>
</html>
