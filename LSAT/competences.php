<?php

require 'core/init.php';

$user = new User();
$user->checkIsValidUser('teacher');
$teacherId = $user->data()->id;
$competence = new Competence();
$teacherCompetences = $competence->getCompetencesForTeacher($teacherId);
//var_dump($teacherCompetences);

?>

<!doctype html>
<html class="no-js" lang="en">
<head>
  <title>LSAT | Competencias</title>
  <?php include 'includes/templates/headTags.php' ?>
</head>

<body>

  <?php include 'includes/templates/header.php' ?>

  <section class="scroll-container" role="main">

    <div class="row">
      <?php include 'includes/templates/teacherSidebar.php' ?>
      <div class="large-9 medium-8 columns">
        <h3>Mis competencias</h3>
        <h4 class="subheader">Administracion de competencias</h4>
        <hr>

        <table>
         <thead>
           <tr>
             <th width="300">Nombre</th>
             <th width="300">Editar</th>
           </tr>
         </thead>

         <tbody>
           <?php

           if($teacherCompetences != null){

            foreach ($teacherCompetences as $competence) {

              echo "<tr id='$competence->id'>
              <td> $competence->name </td>";


              if($competence->isPublished){
                echo "<td> Competencia publicada </td>";
              }else{
                echo "<td> <a href=\"competenceDetail.php?competence=$competence->id\" class='tiny button secondary'>Editar</a> </td>";
              }

              echo  "</tr>";
          }
        }else{
          echo "<tr> <td> No hay competencias </td> </tr>";
        }




        ?>

      </tbody>
    </table>

  </div>
</div>
</section>


<?php include 'includes/templates/footer.php' ?>


<script src="js/vendor/jquery.js"></script>
<script src="js/foundation.min.js"></script>
<script>
  $(document).foundation();

</script>
</body>
</html>
