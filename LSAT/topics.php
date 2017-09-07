<?php

require 'core/init.php';

$user = new User();
$user->checkIsValidUser('admin');

$t = new Topics();
$topics = $t->getAllTopics();

?>

<!doctype html>
<html class="no-js" lang="en">
<head>
  <title>LSAT | Temas</title>
  <?php include 'includes/templates/headTags.php' ?>
</head>

<body>

  <?php include 'includes/templates/header.php' ?>

  <section class="scroll-container" role="main">

    <div class="row">
      <?php include 'includes/templates/adminSidebar.php' ?>  
      <div class="large-9 medium-8 columns">
        <br/>
        <h3>Administracion de temas</h3>
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
           foreach ($topics as $topic) {
            echo "<tr id='$topic->id'> 
            <td> $topic->id </td>
            <td> $topic->name </td>
          </tr>";
        }

        ?>

      </tbody>
    </table>

    <br/>
    <h4>Dar de alta un nuevo tema</h4>
    <hr>
    <div id="">
     Nombre del tema:
     <input id="topicName" type="text">
     <a href="#" onclick="registerTopic()" class="button">Agregar</a>
   </div>

 </div>
</div>
</section>


<?php include 'includes/templates/footer.php' ?>


<script src="js/vendor/jquery.js"></script>
<script src="js/foundation.min.js"></script>
<script>
  $(document).foundation();

  function registerTopic(){
    var name  = $("#topicName").val();
    if(name.trim() == ""){
      alert("Debe escribir el nombre del tema.");
    }else{
      $.post( "controls/doAction.php", { action:"registerTopic", name: name })
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
