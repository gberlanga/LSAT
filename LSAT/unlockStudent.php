<?php

require 'core/init.php';

$user = new User();
$user->checkIsValidUser('teacher');

$teacherId = $user->data()->id;

$groups = new Groups();
$teacherGroups = $groups->getGroupsForTeacher($teacherId);

$groupsIds = array();
$groupsNames = array();
foreach($teacherGroups as $group){
  array_push($groupsIds,$group->id);
  $groupsNames[$group->id] = $group->name;
}


$blockedStudentsByGroup = $user->getBlockedStudents($groupsIds);

?>

<!doctype html>
<html class="no-js" lang="en">
<head>
	<title>LSAT | Grupos</title>
	<?php include 'includes/templates/headTags.php' ?>
</head>

<body>

	<?php include 'includes/templates/header.php' ?>

  <section class="scroll-container" role="main">

		<div class="row">
			<?php include 'includes/templates/teacherSidebar.php' ?>
			<div class="large-9 medium-8 columns">
				<h3>Desbloquear alumno</h3>
				<h4 class="subheader">Lista de alumnos bloqueados</h4>
				<hr>

        <?php
          if($blockedStudentsByGroup != null){
              foreach ($blockedStudentsByGroup as $key=>$studentsInGroup) {
                echo "<div class='blockedStudent'><h6 class='panel-title'>Grupo $groupsNames[$key]</h6> <div class='body'></div>";
                echo "<table> <thead> <tr> <th width='500'>Alumno</th> <th width='300'>Competencia</th> <th width='200'>Desbloquear</th></tr> </thead> <tbody>";

                foreach($studentsInGroup as $student) {
                  echo "<tr id='$student->studentId'>
  								<td> $student->username </td>
  								<td> $student->competenceName </td>";
                  echo "<td> <a href=# onClick='unlockStudent($student->studentId, $student->competenceId, $key)' class='tiny button secondary'>Desbloquear</a> </td></tr>";
                }
                echo "</tbody></table>";
							}
						}else{
							echo "No hay alumnos bloqueados";
						}
				?>

      </div>
    </div>
  </section>


  <?php include 'includes/templates/footer.php' ?>


  <script src="js/vendor/jquery.js"></script>
  <script src="js/foundation.min.js"></script>
  <script>
    $(document).foundation();

    function unlockStudent(sId, cId, gId){
      $.post( "controls/doAction.php", { action:"unlockStudent", sId: sId, cId: cId, gId: gId})
      .done(function( data ) {
        data = JSON.parse(data);
        if(data.message == 'error'){
          alert("Error: \n\n" + data.message);
        }else{
          window.location.reload();
        }
      });

    }
  </script>
</body>
</html>
