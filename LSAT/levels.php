<?php

require 'core/init.php';

$user = new User();
$user->checkIsValidUser('admin');

$l = new Levels();
$levels = $l->getAllLevels();

?>

<!doctype html>
<html class="no-js" lang="en">
<head>
  <title>LSAT | Niveles</title>
  <?php include 'includes/templates/headTags.php' ?>
</head>

<body>

  <?php include 'includes/templates/header.php' ?>

  <section class="scroll-container" role="main">

    <div class="row">
      <?php include 'includes/templates/adminSidebar.php' ?>  
      <div class="large-9 medium-8 columns">
        <br/>
        <h3>Administracion de niveles de dificultad</h3>
        <hr>

        <table> 
         <thead> 
           <tr> 
             <th width="300">Id</th> 
             <th width="200">Nombre</th> 
           </tr> 
         </thead>

         <tbody> 
           <?php
           foreach ($levels as $level) {
            echo "<tr id='$level->id'> 
            <td> $level->id </td>
            <td> $level->name </td>
          </tr>";
        }

        ?>

      </tbody>
    </table>

    <br/>
    <h4>Dar de alta un nuevo nivel</h4>
    <hr>
    <div id="">
     Nombre del nivel:
     <input id="levelName" type="text">
     <a href="#" onclick="registerLevel()" class="button small">Agregar</a>
   </div>

 </div>
</div>
</section>


<?php include 'includes/templates/footer.php' ?>


<script src="js/vendor/jquery.js"></script>
<script src="js/foundation.min.js"></script>
<script>
  $(document).foundation();

  function registerLevel(){
    var name  = $("#levelName").val();
    if(name.trim() == ""){
      alert("Debe escribir el nombre del nivel.");
    }else{
      $.post( "controls/doAction.php", { action:"registerLevel", name: name })
      .done(function( data ) {
        data = JSON.parse(data);
        if(data.message == 'success'){
          window.location.reload();
        }else{
          alert("There was an error: " + data.message);
        }

      });
    }

    
  }


</script>
</body>
</html>
