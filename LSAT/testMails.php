<?php

require 'core/init.php';

$c = new Competence();
$studentId = 4;
$groupId = 6;
$competenceId = 15;

$c->blockCompetence($studentId, $groupId, $competenceId, '1', '2');

?>