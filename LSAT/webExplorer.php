<?php

require 'core/init.php';

$user = new User();
$user->checkIsValidUser('teacher');
$web = new Web();
$allWebs = $web->getAllPublishedWebs();
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
	<title>LSAT | Webs</title>
	<?php include 'includes/templates/headTags.php' ?>
</head>

<body>

	<?php include 'includes/templates/header.php' ?>

	<section class="scroll-container" role="main">

		<div class="row">
			<?php include 'includes/templates/teacherSidebar.php' ?>
			<div class="large-9 medium-8 columns">
				<h3>Redes de aprendizaje</h3>
				<h4 class="subheader">Que hayan sido publicadas por cualquier profesor</h4>
				<hr>

				<table>
					<thead>
						<tr>
							<th width="50">ID</th>
							<th width="300">Nombre de la red</th>
							<th width="200">Profesor</th>
							<th width="200">Fecha de creacion</th>
							<th width="300">Detalle</th>
						</tr>
					</thead>

					<tbody>
						<?php

						if($allWebs != null){

							foreach ($allWebs as $web) {
								echo "<tr id='$web->id'>
								<td> $web->id </td>
								<td> $web->name </td>
								<td> $web->professor </td>
								<td> $web->createdDate </td>";
								echo "<td> <a href=\"webDetail.php?web=$web->id\" class='tiny button secondary'>Ver detalle</a> </td></tr>";
							}
						}else{
							echo "<tr> <td> No hay redes publicadas </td> </tr>";
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
