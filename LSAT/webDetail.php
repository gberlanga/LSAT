<?php

require 'core/init.php';

$user = new User();
$user->checkIsValidUser('teacher');

$w = new Web();
$webId = Input::get("web");

if ($webId != ''){
  $web = $w->getWeb($webId);

  if ($web == null) {
    Redirect::to('webs.php');
  }
}else{
  Redirect::to('webs.php');
}

$levels = $w->getLevelsInWeb($webId);
$questionsByLevel = $w->getQuestionsInWeb($webId);

$questionsIds = $w->getQuestionsIds($webId);
$question = new Question();
$questions = $question->getQuestions($questionsIds);

$answer = new Answer();
$answers = $answer->getAnswersForQuestionList($questions);

?>

<!doctype html>
<html class="no-js" lang="en">
<head>
  <title>LSAT | Detalle de Red</title>
  <?php include 'includes/templates/headTags.php' ?>
</head>

<body>

  <?php include 'includes/templates/header.php' ?>

  <section class="scroll-container" role="main">

    <div class="row">

    <?php include 'includes/templates/teacherSidebar.php' ?>
      <div class="large-9 medium-8 columns">

        <h3>
          <?php echo $web->name; ?>
        </h3>

        <div class="">
          <?php

            if(is_array($levels) && count($levels) == 0){
              echo " <p>Esta red aun no tiene preguntas. <a href='newWeb.php?web=$webId'>Continuar editando</a></p> ";
            }else{

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
                                  <th width='50%'>Respuesta</th>
                                  <th width='50%'>Feedback</th>
                                </tr>
                              </thead>
                              <tbody>";
                  $answersForQuestion = $answers[$question->id];
                  foreach($answersForQuestion as $a){
                    $answerText = $a[0]->text;
                    $answerImage = $a[0]->urlImage;
                    $feedbackText = $a[0]->textFeedback;
                    $feedbackImage = $a[0]->imageFeedback;
                    echo "<tr>
                              <td>";
                    if ($a[0]->correct == 1){
                      echo "    <label class='label'> Correcta </label>";
                    }
                      echo "    <p> $answerText </p>";
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

</script>
</body>
</html>
