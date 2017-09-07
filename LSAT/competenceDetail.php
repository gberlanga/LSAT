<?php

require 'core/init.php';

$user = new User();
$user->checkIsValidUser('teacher');

$c = new Competence();
$competenceId =  Input::get("competence");

if ($competenceId != ''){
  $competence = $c->getCompetence($competenceId);

  if ($competence == null) {
    Redirect::to('competences.php');
  }
}else{
  Redirect::to('competences.php');
}

$websInCompetence = $c->getWebsInCompetence($competenceId);
//var_dump($websInCompetence);
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
  <title>LSAT | Detalle de Competencia</title>
  <?php include 'includes/templates/headTags.php' ?>
</head>

<body>

  <?php include 'includes/templates/header.php' ?>

  <section class="scroll-container" role="main">

    <div class="row">

      <?php include 'includes/templates/teacherSidebar.php' ?>
      <div class="large-9 medium-8 columns">

        <h3>
          <?php echo $competence->name; ?>
        </h3>

        <table>
         <thead>
           <tr>
             <th width="300">Red</th>
             <th width="300">Ponderar</th>
           </tr>
         </thead>

         <tbody>
           <?php
           foreach ($websInCompetence as $web) {

            echo "<tr id='$web->id'>
            <td> $web->name </td>";

            if($web->isGraded){
              echo "<td> La red ya fue ponderada </td>";
            }else{
              echo "<td> <a href=\"gradingWeb.php?web=$web->webId&c=$competenceId\" class='tiny button secondary'>Ponderar</a> </td>";
            }

            echo  "</tr>";
          }

          ?>

        </tbody>
      </table>
      <a href="#" onclick="publishCompetence()" class="button round small right alerta">Publicar</a>
    </div>
  </div>
</section>

<?php include 'includes/templates/footer.php' ?>


<script src="js/vendor/jquery.js"></script>
<script src="js/foundation.min.js"></script>
<script>
  $(document).foundation();

  var cId = <?php
    if (isset($competenceId)) {
      echo "$competenceId";
    }else{
      echo "0";
    }
    ?>;

  function publishCompetence(){

    $.post( "controls/doAction.php", { action:"webIsGraded", cId: cId})
    .done(function( data ) {

      data = JSON.parse(data);
      if(data.message == 'success'){
        console.log(data);
        if(data.isGraded){


          $.post( "controls/doAction.php", { action:"publishCompetence", cId: cId})
          .done(function( data ) {
            data = JSON.parse(data);
            if(data.message == 'success'){
              window.location.replace('./competences.php');
            }else{
              alert("Error" + data.message);
            }
          });


        }else{
          alert("No puedes publicar una competencia \nhasta que todas las redes hayan sido ponderadas.");
        }

      }else{
        alert("Error: \n\n" + data.message);
      }
    });
  }

</script>
</body>
</html>
