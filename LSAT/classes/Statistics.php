<?php

class Statistics {
	private $_db;

	public function __construct() {
		$this->_db = DB::getInstance();
	}

	public function getGroupProgress($groupId = null) {
		if ($groupId == null) return;
		$results = array();

		try {

			$c = new Competence();
			$g = new Groups();
			$u = new User();

			//Obtener competencias asigandas al grupo
			$competences = $g->getCompetencesForGroup($groupId);


			//Obtener lista de estudiantes del grupo
			$students = $g->getAllStudentsFromGroup($groupId);
			//$students = $u->getStudentsUserData($studentsIds);

			$results["activeCompNumber"] = count($competences);
			$studentsProgress = array();

			if ($results["activeCompNumber"] > 0) {
				//Obtener el progreso de cada estudiante en cada competencia
				foreach ($students as $key => $student) {
					$competencesDetails = array();
					foreach ($competences as $key => $competence) {
						$studentId = $student->id;
						$competenceId = $competence->id;
						$status = 0;
						
						$studentProgress = $u->getStudentProgress($studentId, $groupId, $competenceId);
		           		//El status indica cual es el avance del alumno en esa competencia
		           		//  0 - No ha empezado a contestar
		           		//  1 - Empezado, pero aun no termina
		           		//  2 - Termino de contestar la competencia
		           		// -1 - Esta bloqueado
						//var_dump($studentProgress);
	           			//Checar el status de esta competencia
						if(count($studentProgress) == 0) {
							$status = 0;
						} else {
							$isBlocked = $c->isCompetenceBlocked($studentId, $groupId, $competenceId);
							if($isBlocked == true) {
								$status = -1;
							} else {
								$isCompleted = $c->isCompetenceCompleted($studentProgress);
								if($isCompleted == true) {
									$status = 2;
								} else {
									$status = 1;
								}
							}
						}

						$competencesDetails[$competenceId] = array($status, $competence->name);

					} //foreach competencias

					$studentsProgress[$studentId] = array($student, $competencesDetails);

				} //foreach estudiantes

				$results["students"] = $studentsProgress;
			} else {
				$results["students"] = null;
			}

	} catch(Exception $e) {
		die(array());
	}

	return $results;

}



}
