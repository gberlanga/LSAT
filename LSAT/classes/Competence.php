<?php

class Competence {
	private $_db,
	$_data = array(),
	$_tableName = 'competence';

	public function __construct() {
		$this->_db = DB::getInstance();
	}

	public function getCompetence($competenceId = null) {
		if ($competenceId == null) return;

		$db = $this->_db->get($this->_tableName, array('id', '=', $competenceId));

		if($db && $db->count()) {
			return $db->first();
		}

		return null;

	}

	public function getWebsInCompetence($competenceId = null){
		if ($competenceId == null) return;

		$sql = "SELECT * FROM web W JOIN websincompetence WC ON
		W.id = WC.webId WHERE WC.competenceId = $competenceId";

		if(!$this->_db->query($sql)->error()) {
			if($this->_db->count()) {
				return $this->_db->results();
			}
		}

	}

	//Regresa un arreglo con los ids de websincompetence que le corresponden a una competencia
	public function getWebsInCompetenceIds($competenceId = null){
		if ($competenceId == null) return;

		$sql = "SELECT WC.id FROM web W JOIN websincompetence WC ON
		W.id = WC.webId WHERE WC.competenceId = $competenceId";

		if(!$this->_db->query($sql)->error()) {
			if($this->_db->count()) {
				$ids = array();
				foreach($this->_db->results() as $competence){
					array_push($ids, $competence->id);
				}
				return $ids;
			}
		}

	}

	public function getCompetencesForTeacher($teacherId = null){
		if ($teacherId == null) return;

		$sql = "SELECT * FROM competence WHERE professor = ?";

		if(!$this->_db->query($sql, array($teacherId))->error()) {
			if($this->_db->count()) {
				return $this->_db->results();
			}
		}

		return array();
	}

	public function getCompetencesByGroupOfTeacher($teacherId = null){
		if ($teacherId == null) return;

		$sql = "SELECT * FROM competence C JOIN competenceingroup CG ON
		C.id = CG.competenceId WHERE C.professor = $teacherId";

		if(!$this->_db->query($sql, array($teacherId, true))->error()) {
			if($this->_db->count()) {
				return $this->_db->results();
			}
		}

		return array();
	}

	//Regresa un arreglo con los ids de todas las competencias del grupo
	public function getCompetencesIdsForGroup($groupId = null){
		if ($groupId == null) return;

		//$db = $this->_db->get('competenceingroup', array('groupId', '=', $groupId));
		$sql = "SELECT * FROM competenceingroup WHERE groupId = ?";

		if(!$this->_db->query($sql, array($groupId))->error()) {
			if($this->_db->count()) {
				$results = $this->_db->results();
				$ids = array();

				foreach ($results as $key => $value) {
					array_push($ids, $value->competenceId);
				}

				return $ids;
			}
		}

		return array();
	}

	//Le llega un arreglo de ids de competencias y regresa sus detalles
	public function getCompetencesDetails($competencesIds = null){
		if ($competencesIds == null) return;

		$ids = implode(",", $competencesIds);
		$sql = "SELECT * FROM competence C WHERE id IN ($ids)";

		if(!$this->_db->query($sql, array())->error()) {
			if($this->_db->count()) {
				return $this->_db->results();
			}
		}

		return array();
	}

	public function createNewCompetence($name, $professor, $webIds = array()){
		//Crear el registro de la nueva competencia en la BD
		$this->create(array("name"=>$name, "professor"=> $professor));

		$competenceId = intval($this->_db->lastInsertId());
		try{
			//Agregar todas las redes para la competencia
			$table = "websincompetence";
			foreach ($webIds as $key => $value) {
				$fields = array("order"=> (intval($key)+1), "webId"=>$value, "competenceId"=> $competenceId);
				if(!$this->_db->insert($table, $fields)) {
					throw new Exception('There was a problem creating websincompetence.');
				}
			}
			return $competenceId;
		}
		catch(PDOException $e){
			return false;
		}
	}

	public function create($fields = array()) {
		if(!$this->_db->insert($this->_tableName, $fields)) {
			throw new Exception('There was a problem creating the competence.');
		}
	}

	public function update($competenceId, $fields = array()) {
		if(!$this->_db->update($this->_tableName, $competenceId, $fields)) {
			throw new Exception('There was a problem updating.');
		}
	}

	public function data() {
		return $this->_data;
	}


	/* Metodos para estudiantes*/
	public function validateStudentCanAnswer($studentId, $groupId, $competenceId){

		//Ver que el estudiante si este en ese grupo
		$u = new User();

		$validStudent = $u->studentBelongInGroup($studentId, $groupId);
		if(!$validStudent){
			return false;
		}

		//Ver que la competencia este publicada y le pertenezca al grupo
		$validCompetence = $this->isCompetencePublishedAndBelongsToGroup($groupId, $competenceId);
		if(!$validCompetence){
			return false;
		}

		return true;
	}

	public function isCompetencePublishedAndBelongsToGroup($groupId, $competenceId) {
		$sql = "SELECT * FROM  competenceingroup CG JOIN competence C ON CG.competenceId = C.id WHERE CG.groupId = $groupId AND C.id = $competenceId";

		if(!$this->_db->query($sql, array())->error()) {
			if($this->_db->count() && $this->_db->count() == 1) {
				return true;
			}
		}

		return false;
	}

	public function competenceExistsInGroup($groupId, $competenceId) {
		$sql = "SELECT * FROM  competenceingroup  WHERE groupId = $groupId AND competenceId = $competenceId";

		if(!$this->_db->query($sql, array())->error()) {
			if($this->_db->count() && $this->_db->count() == 1) {
				return true;
			}
		}

		return false;
	}

	public function isCompetenceStarted($studentId, $groupId, $competenceId) {
		//Si la competencia ya fue inicializada para ese alumno entonces
		//La tabla de studentrecord debe de tener al menos un registro
		//ya uqe guarda uno por cada red de la competencia

		$sql = "SELECT * FROM  studentrecord WHERE studentId = $studentId AND groupId = $groupId AND competenceId = $competenceId";
		if(!$this->_db->query($sql, array())->error()) {
			if($this->_db->count() && $this->_db->count() > 0) {
				//var_dump($this->_db->count());
				return true;
			}
		}
		// var_dump($this->_db->error());
		// var_dump($this->_db->count());
		// var_dump($this->_db->results());
		return false;
	}

	function shuffle_assoc($list) {
		if (!is_array($list)) return $list;

		$keys = array_keys($list);
		shuffle($keys);
		$random = array();
		foreach ($keys as $key) {
			$random[$key] = $list[$key];
		}
		return $random;
	}

	public function startCompetence($studentId, $groupId, $competenceId) {
		$w = new Web();

		//Hay que llenar tres tablas
		//1) Student record - 1 registro por cada web de la competencia
		//2) Student progress - 1 registro por cada web, mantiene tambien el rango de preguntas disponibles y la ultima pregunta contestada
		//3) Questionsforstudent - Muchos registros, 1 por cada pregunta de la red


		//Para llenar registros para tabla 'questionsforstudent' hay que crear una secuancia unica de preguntas para este estudiante

		//1. Traer redes de competencia
		$websInCompetence = $this->getWebsInCompetence($competenceId);
		//2. Traer preguntas de red
		$questionsInWebs = array();
		foreach ($websInCompetence as $key => $web) {
			$questionsInWeb = $w->getQuestionsInWeb($web->webId);

			//3. Random por nivel de las preguntas
			$questionsInWeb = $this->shuffle_assoc($questionsInWeb);
			asort($questionsInWeb);
			$questionsInWebs[$web->webId] = $questionsInWeb;
		}

		// echo"websInCompetence";
		// var_dump($websInCompetence);
		// echo"questionsInWebs";
		// var_dump($questionsInWebs);

		//Llenar student progress que mantiene la informacion del progreso
		//del alumno en las redes de esa competencia
		foreach ($questionsInWebs as $webId => $questions) {

			$firstQuestion = 0;
			$lastQuestion = 0;
			$i=0;
			try{

				foreach ($questions as $questionId => $level) {
					//Llenar registros en questionsforstudent.
					//Lista random unica para cada estudiante con su combinacion de preguntas por red

					$fields = array('level' => intval($level), 'questionId' => intval($questionId));
					if(!$this->_db->insert('questionsforstudent', $fields)) {
						throw new Exception('There was a problem creating this student record.');
					}

					if($i==0){ $firstQuestion = intval($this->_db->lastInsertId()); }

					$i++;
				}

				$lastQuestion = intval($this->_db->lastInsertId());

				$fields = array(
					'webId' => intval($webId),
					'lastAnsweredQuestion' => -1,
					'firstQuestion' => intval($firstQuestion),
					'lastQuestion' => intval($lastQuestion)
					);

				if(!$this->_db->insert('studentprogress', $fields)) {
					throw new Exception('There was a problem inserting into studentprogress.');
				}

				$studentProgressId = intval($this->_db->lastInsertId());

				//StudentRecord tiene las estadisticas del avance por Red,
				//Entoncesva a haber un regisro en la base de datos por cada una de las redes en studentprogress
				$fields = array(
					'studentId' => intval($studentId),
					'groupId' => intval($groupId),
					'competenceId' => intval($competenceId),
					'studentProgressId' => intval($studentProgressId)
					);

				if(!$this->_db->insert('studentrecord', $fields)) {
					throw new Exception('There was a problem inserting into studentrecord.');
				}

			}catch(PDOExeption $e) {
				die($e->getMessage());
			}

		}
	}

	public function isCompetenceCompleted($studentprogress = array()){
		$isCompleted = true;

		// Si todos los de student progress tienen seteado un finished date quiere decir que la competencia fue terminada
		foreach ($studentprogress as $key => $sp) {
			if(!isset($sp->finishedDate)){
				$isCompleted = false;
				return $isCompleted;
			}
		}

		return $isCompleted;
	}

	public function blockCompetence($studentId, $groupId, $competenceId, $level, $webName){
		//Nesecitamos hacer dos cosas
		//1) Marcar la competencia como bloqueada para ese alumno en la base de datos
        //2) Enviarle un email al maestro y otro al alumno 
		try {
			// 1)
			$sql = "UPDATE studentrecord SET isBlocked = 1 WHERE studentId = ? AND groupId = ? AND competenceId = ?";
			if(!$this->_db->query($sql, array($studentId, $groupId, $competenceId))->error()) {
			    // 2)
			    $mailer = new Mailer();
			    $mailer->sendBlockedMails($studentId, $groupId, $competenceId, $level, $webName);

				return true;
			}
		} catch(Exception $e) {
			die($e->getMessage());
		}

		return false;
	}

	public function isCompetenceBlocked($studentId, $groupId, $competenceId){
		$isBlocked = false;
		$sql = "SELECT * FROM studentrecord WHERE studentId = ? AND groupId = ? AND competenceId = ? AND isBlocked = 1";

		if(!$this->_db->query($sql, array($studentId, $groupId, $competenceId))->error()) {
			if($this->_db->count()) {
				$isBlocked = true;
			}
		}

		return $isBlocked;
	}

	public function unlockCompetence($studentId, $groupId, $competenceId) {

		try{
			$u = new User();
			$sp = $u->getStudentProgress($studentId, $groupId, $competenceId);
			$ids = array();

			foreach ($sp as $key => $studentprogress) {
				$firstQ = $studentprogress->firstQuestion;
				$lastQ = $studentprogress->lastQuestion;
				array_push($ids, $studentprogress->id);


				$sql = "DELETE FROM questionsforstudent WHERE id BETWEEN ? AND ?";

				if($this->_db->query($sql, array($firstQ, $lastQ))->error()) {
					throw new Exception('There was a problem deleting questionsforstudent.');
				}
			}

			$idsList = implode(",", $ids);

			$sql = "DELETE FROM studentprogress WHERE id IN ($idsList)";

			if($this->_db->query($sql, array())->error()) {
				throw new Exception('There was a problem deleting studentprogress.'.$sql);
			}

			$sql = "DELETE FROM studentrecord WHERE studentProgressId IN ($idsList)";

			if($this->_db->query($sql, array())->error()) {
				throw new Exception('There was a problem deleting studentrecord.');
			}

		}

		catch(Exception $e) {
			$response = array( "message" => "Error:015 ".$e->getMessage());
			die(json_encode($response));
		}


	}

	public function deleteStudentProgress($studentId, $groupId, $competenceId) {

		try{
			$u = new User();
			$sp = $u->getStudentProgress($studentId, $groupId, $competenceId);
			$ids = array();

			foreach ($sp as $key => $studentprogress) {
				$firstQ = $studentprogress->firstQuestion;
				$lastQ = $studentprogress->lastQuestion;
				array_push($ids, $studentprogress->id);


				$sql = "DELETE FROM questionsforstudent WHERE id BETWEEN ? AND ?";

				if($this->_db->query($sql, array($firstQ, $lastQ))->error()) {

				}
			}

			$idsList = implode(",", $ids);

			$sql = "DELETE FROM studentprogress WHERE id IN ($idsList)";

			if($this->_db->query($sql, array())->error()) {

			}

			$sql = "DELETE FROM studentrecord WHERE studentProgressId IN ($idsList)";

			if($this->_db->query($sql, array())->error()) {

			}

		}

		catch(Exception $e) {
			$response = array( "message" => "Error:015 ".$e->getMessage());
			die(json_encode($response));
		}


	}

}
