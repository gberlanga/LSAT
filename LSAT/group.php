<?php

require 'core/init.php';

$user = new User();
$user->checkIsValidUser('teacher');

//validar que el id del grupo que se quiere ver sea del profesor que esta logueado
$teacherId = $user->data()->id;
$groupId = Input::get('id');
$groups = new Groups();
$isOwner = $groups->verifyGroupOwnership($groupId, $teacherId);

if(!$isOwner){
  Redirect::to('groups.php');
}

$group = $groups->getGroupById($groupId);
//Las competencias asignadas a este grupo
$competences = $groups->getCompetencesForGroup($groupId);

?>

<!doctype html>
<html class="no-js" lang="en">
<head>
  <title>LSAT | Groups</title>
  <?php include 'includes/templates/headTags.php' ?>
</head>

<body>

  <?php include 'includes/templates/header.php' ?>

  <section class="scroll-container" role="main">

    <div class="row">
      <?php include 'includes/templates/teacherSidebar.php' ?>  
      <div class="large-9 medium-8 columns">
        <h3>Detalle de grupo <?php echo $group->name ?></h3>
        <h4 class="subheader">Competencias asignadas</h4>
        <hr>  
        <ol>
          <?php
          if($competences != null){

            foreach ($competences as $competence) {
              echo "<li>$competence->name </li>";
            }

          }else{
            echo "<tr> <td> Este grupo aun no tiene competencias. </td> </tr>";
          }
          
          ?>
        </ol>
        
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
