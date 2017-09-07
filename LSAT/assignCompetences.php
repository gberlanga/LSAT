<?php

require 'core/init.php';

$user = new User();
$user->checkIsValidUser('teacher');

$teacherId = $user->data()->id;
$groups = new Groups();
$teacherGroups = $groups->getGroupsForTeacher($teacherId);
$competence = new Competence();
$teacherCompetences = $competence->getCompetencesForTeacher($teacherId);
$groupCompetences = $competence->getCompetencesByGroupOfTeacher($teacherId);

?>

<!doctype html>
<html class="no-js" lang="en">
<head>
  <title>LSAT | Assign competences</title>
  <?php include 'includes/templates/headTags.php' ?>
</head>

<body>

  <?php include 'includes/templates/header.php' ?>

  <section class="scroll-container" role="main">

    <div class="row">
      <?php include 'includes/templates/teacherSidebar.php' ?>
      <div class="large-9 medium-8 columns">
        <br/>
        <h3>Asignar competencias</h3>
        <hr>


        <?php
        foreach ($teacherGroups as $group) {
          echo "<div class='assignCompetence' id='g-$group->id'>
                  <h6 class='panel-title'>Grupo $group->name</h6> 
                  <div class='body'>
                    <ol>";
          foreach ($groupCompetences as $competence) {
            if ($competence->groupId == $group->id){
              echo "<li name='$competence->competenceId'>$competence->name </li>";
            }

          }
          echo "</ol><a onclick='showAvailableCompetences($group->id)'>Asignar competencia</a></div></div>";
        }
        ?>


        <div  class="availableCompetences reveal-modal small" id="acModal" style="display: none" data-reveal>
          <h3>Competencias disponibles</h3>
          <ul>
            <?php
            foreach ($teacherCompetences as $competence) {
              echo "<li name='$competence->id'>
                           <a onclick='addCompetence($competence->id)'>+</a> 
                           $competence->name 
                    </li>";
            }
            ?>
          </ul>
        </div>

      </div>
    </div>
  </section>

  <?php include 'includes/templates/footer.php' ?>

  <script src="js/vendor/jquery.js"></script>
  <script src="js/foundation.min.js"></script>
  <script>
    $(document).foundation();

    var currentGroup = 0;

    function showAvailableCompetences(groupId) {
      currentGroup = groupId;
      var id = "#g-"+groupId;
      var groupCompetences = $(id).find('li');

      var availableLis = $(".availableCompetences li");
      var usedCompetencesIds = [];

      //Iteerar cada competencia del grupo actual, guardar su id
      $(groupCompetences).each(function() {

        var name = $(this).attr('name');
        usedCompetencesIds.push(name);

      });

      //Iteerar cada competencia disponible y ocultarla si ya existe en el grupo
      $(availableLis).each(function() {
        $(this).show();
        var name = $(this).attr('name');
        var exists = usedCompetencesIds.indexOf(name); 
        if(exists != -1){
          $(this).hide();
        }        
      });

    $('#acModal').foundation('reveal', 'open');
  }

  function addCompetence(competenceId){
    $.post( "controls/doAction.php", {  action: "addCompetenceToGroup", competenceId: competenceId, groupId: currentGroup})
    .done(function( data ) {
      data = JSON.parse(data);
      if(data.message != 'success'){
        alert("Error: \n\n" + data.message);
      }else{
        window.location.reload();
      }

    });
  }

</script>
</body>
</html>
