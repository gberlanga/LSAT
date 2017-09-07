<?php

require 'core/init.php';

$user = new User();
$user->checkIsValidUser('teacher');

$web = new Web();
$webId =  Input::get("web");
$webName = $web->getWeb($webId);

$competenceId = Input::get("c");

$webInCompetence = $web->getWebsInCompetenceId($webId, $competenceId);
$isGraded = $webInCompetence->isGraded;
if($isGraded) {
	Redirect::to('competenceDetail.php?competence='.$competenceId);
}

$c = new Competence();
$competence = $c->getCompetence($competenceId);

$levels = $web->getLevelsInWeb($webId);
$questionsByLevel = $web->getQuestionsInWeb($webId);

$questionsIds = $web->getQuestionsIds($webId);
$question = new Question();
$questions = $question->getQuestions($questionsIds);

$answer = new Answer();
$answers = $answer->getAnswersForQuestionList($questions);

?>

<!doctype html>
<html class="no-js" lang="en">
<head>
	<title>LSAT | Redes</title>
	<?php include 'includes/templates/headTags.php' ?>
</head>

<body>

	<?php include 'includes/templates/header.php' ?>

	<section class="scroll-container" role="main">

		<div class="row">

			<?php include 'includes/templates/teacherSidebar.php' ?>
			<div class="large-9 medium-8 columns">

				<h3>
					<?php echo $webName->name; ?> para competencia:  <?php echo $competence->name; ?>
				</h3>
				<h4 class="subheader"> Asignar ponderacion a cada respuesta </h4>

				<!-- WAAA-->
				<div class="">
					<?php
						foreach($levels as $level) {
							echo "<div class='webExplorerLevel'>
											<h6 class='panel-title'>Nivel $level</h6>
											<div class='body'>
												<ol>";
							foreach($questions as $question) {
								if ($level == $questionsByLevel[$question->id]){
                  $urlImage = $question->urlImage;
                  echo "  <li class='questionForLevel'>
                          <div class='question'>
                            <p>$question->text</p>";
                  if (!empty($urlImage)) {
                    echo "  <img src='$urlImage'>";
                  }
                  echo "  </div>
                          <div class='answers'>
                            <table width='100%'>
                              <thead>
                                <tr>
																	<th width='10%'>Ponderaci&oacuten</th>
                                  <th width='45%'>Respuesta</th>
                                  <th width='45%'>Feedback</th>
                                </tr>
                              </thead>
                              <tbody>";
                  $answersForQuestion = $answers[$question->id];
									$maxLevel = count($levels);
								  $options = "";
								  for ($i=1-$level; $i <= 0; $i++) {
								    $options .= "<option value='$i'>$i</option>";
								  }
									foreach($answersForQuestion as $a){
										$answerId = $a[0]->id;
                    $answerText = $a[0]->text;
                    $answerImage = $a[0]->urlImage;
                    $feedbackText = $a[0]->textFeedback;
                    $feedbackImage = $a[0]->imageFeedback;
                    echo "<tr>";
                    if ($a[0]->correct == 1){
                      echo "<td><label class='label answer' name='$question->id-$answerId'> Correcta </label></td>";
                    } else{
											echo "<td><select class='answer' name='$question->id-$answerId'> $options </select></td>";
										}
										echo "<td><p> $answerText </p>";
										if (!empty($answerImage)) {
											echo "    <img src='$answerImage' >";
										}
										echo "    </td>
                                <td>
                                  <p> $feedbackText </p>";
                      if (!empty($feedbackImage)) {
                        echo "    <img src='$feedbackImage' >";
                      }
                      echo "    </td>
                              </tr>";
                  }
                  echo "      </tbody>
                            </table>
                          </div><hr>";
                }
							}
							echo "    </li></ol>
                      </div>
                    </div>";
						}

					?>
				</div>

				<a href="#" onclick="gradeWeb()" class="button round small right">Guardar</a>
			</div>
		</div>
	</section>

	<?php include 'includes/templates/footer.php' ?>


	<script src="js/vendor/jquery.js"></script>
	<script src="js/foundation.min.js"></script>
	<script>
		$(document).foundation();

		var webId = <?php
		if (isset($web)) {
			echo "$webId";
		}else{
			echo "0";
		}
		?>;

		var cId = <?php
		if (isset($competenceId)) {
			echo "$competenceId";
		}else{
			echo "0";
		}
		?>;

		function gradeWeb() {
			var answers = $(".answer");
			var len = answers.length;
			var data = {};

			for(var i=0; i<len; i++) {
				var item = $(answers[i]);
				var name = item.attr('name').split('-');
			    var q = name[0];      //id pregunta
			    var a = name[1];      //respuesta
			    var p =  item.val();  //ponderacion
			    if(p==""){ p = "1"}
			    var index = q+"-"+a;
			    data[index] = p;
			}

			$.post( "controls/doAction.php", { action:"gradeWeb", cId: cId, webId: webId, data: data})
			.done(function( data ) {

				data = JSON.parse(data);
				if(data.message == 'success'){
					window.location.replace('./competenceDetail.php?competence='+cId);
				}else{
					alert("Error: \n\n" + data.message);
				}

			});

			console.log(data);
		}

	</script>
</body>
</html>
