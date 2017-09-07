<?php

require 'core/init.php';

$user = new User();
$user->checkIsValidUser('student');
$studentId = $user->data()->id;
$information = $user->getInformationForStudent($studentId);
//var_dump($information);
$c = new Competence();
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
	<title>LSAT | Dashboard</title>
	<?php include 'includes/templates/headTags.php' ?>
</head>

<body>

	<?php include 'includes/templates/header.php' ?>

	<section class="scroll-container" role="main">

		<div class="row">
			<?php include 'includes/templates/studentSidebar.php' ?>
			<div class="large-9 medium-8 columns">
				<br/>
				<h3>Bienvenido <?php echo $user->data()->username ?> </h3>
				<h4 class="subheader">Aquí puedes encontrar una lista de tus grupos y las actividades que debes contestar.</h4>
				<hr>

				<div id="studentCompetences">
					<div id="groups">
						<ul>
							<?php
							if (isset($information)) {
								foreach ($information as $key => $group) {
									//Desplegar descripcion del curso
									echo "<li><a href='#' onclick='showCompetence($group->groupId)'><span><b>$group->groupName</b></span><span>$group->professorName</span></a></li>";
								}
							} else {
								echo "<span>Aun no tienes competencias asignadas</span>";
							}
							?>
						</ul>
					</div>
					<?php

					if (isset($information)){
						//La informacion viene por grupos
						//Cada grupo tiene un campo llamado "competences" donde vienen todas sus competencias separadas por comas
						//Las guardaremos en un arreglo para iterarlas facilmente

						foreach ($information as $key => $group) {
							$groupId = $group->groupId;
							$competencesString = $group->competences;
							$competencesIdsString = $group->competencesIds;

							$competencesArray = explode(',', $competencesString);
							$competencesIdsArray = explode(',', $competencesIdsString);

						    //Desplegar descripcion del curso
							echo "<div id=$group->groupId class='competences' style='display: none'>
							<h3>$group->groupName / <small>$group->professorName</small></h3>
							<ul>";
							foreach ($competencesIdsArray as $key => $competenceId) {
								$competenceName = $competencesArray[$key];

								$studentProgress = $user->getStudentProgress($studentId, $groupId, $competenceId);

								//Checar el status de esta competencia
								if(count($studentProgress) == 0) {
									$status = "Empezar";
									$statusClass = "notStarted";
								} else {
									$isBlocked = $c->isCompetenceBlocked($studentId, $groupId, $competenceId);
									if($isBlocked == true) {
										$status = "Bloqueado";
										$statusClass = "blocked";
									} else {
										$isCompleted = $c->isCompetenceCompleted($studentProgress);
										if($isCompleted == true) {
											$status = "Completado";
											$statusClass = "finished";
										} else {
											$status = "Continuar";
											$statusClass = "started";
										}
									}
								}
								if($statusClass == "finished" || $statusClass == "blocked"){
									echo "<li><a class='$statusClass'>$status</a> <span> $competenceName </span> </li>";
								}else{
									echo "<li><a href='answer.php?c=$competenceId&g=$groupId' class='$statusClass'>$status</a> <span> $competenceName </span> </li>";
								}

							}

							echo "</ul></div>";
						}
					}
					?>
				</div>

			</div>
		</div>
	</section>


	<?php include 'includes/templates/footer.php' ?>

	<script src="js/vendor/jquery.js"></script>
	<script src="js/foundation.min.js"></script>
	<script>
		$(document).foundation();

		function showCompetence(groupId) {
			$("div.competences").hide();
			var group = "#"+groupId;
			$(group).show();
		}

		$("div.competences").first().show();

	</script>
</body>
</html>
