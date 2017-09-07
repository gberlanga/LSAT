<?php

class Question {
	private $_db,
	$_tableName = 'question';

	public function __construct() {
		$this->_db = DB::getInstance();
	}

	public function create($fields = array()) {
		if(!$this->_db->insert($this->_tableName, $fields)) {
			throw new Exception('There was a problem creating the question.');
		}
	}

	public function update($questionId, $fields = array()) {
		if(!$this->_db->update($this->_tableName, $questionId, $fields)) {
			throw new Exception('There was a problem updating.');
		}
	}

	public function getFilteredQuestions($topic, $difficulty){

		$sql = "SELECT id, LEFT(text, 80) as text FROM question WHERE topic =  ? AND difficulty = ?";
		if(!$this->_db->query($sql, array($topic, $difficulty))->error()) {
			if($this->_db->count()) {
				return $this->_db->results();
			}
		}
		return array();
	}

	public function getQuestion($id){

		$sql = "SELECT * FROM question WHERE id = ?";
		if(!$this->_db->query($sql, array($id))->error()) {
			if($this->_db->count()) {
				return $this->_db->results();
			}
		}
		return array();
	}

	public function getQuestions($ids = array()){
		if (count($ids) == 0) return;

		$idList = implode(",", $ids);

		$sql = "SELECT * FROM question WHERE id IN ($idList)";
		if(!$this->_db->query($sql, array())->error()) {
			if($this->_db->count()) {
				return $this->_db->results();
			}
		}

		return array();
	}

	public function getNextQuestion($studentId, $groupId, $competenceId){
		try{

			$u = new User();
			$c = new Competence();
			$w = new Web();
			$competenciaTerminada = true;
			$studentprogress = array();

			$studentprogress = $u->getStudentProgress($studentId, $groupId, $competenceId);
			$competenciaTerminada = $c->isCompetenceCompleted($studentprogress);

			//Si la competencia ya fue terminada no vale la pena calcular todo lo demas
			if ($competenciaTerminada) {
				$response['nextQuestion'] = "completed";
				return $response;
			}

			// Necesitamos un arreglo que mantenga la relacion de que red ya fue completada
			$websTerminadas = array();
			$webTerminada = true;
			foreach ($studentprogress as $key => $sp) {
				if(!isset($sp->finishedDate)){
					$webTerminada = false;
				}
				$websTerminadas[$sp->webId] = $webTerminada;
			}

			//Si la competencia no esta termianda tenemos que saber el orden de las redes, para saber cual sigue
			//Obtenemos “websincompetence” con el competenceId
			$websincompetence = array();
			//Las comillas en 'order' son importantes porque es una palabra reservada
			$sql = "SELECT * FROM  websincompetence WHERE competenceId = $competenceId ORDER BY 'order'";
			if(!$this->_db->query($sql, array($competenceId))->error()) {
				if($this->_db->count()) {
					$websincompetence = $this->_db->results();
				}
			}

			//Cada elemento del arreglo resultante es una red, ordenadas de menor a mayor según el orden
			//iteramos el arreglo vamos viendo los ids de las webs uno por uno
			$webAContestar = 0;
			foreach ($websincompetence as $key => $web) {
			//Si ese webid ya fue terminado, nos pasamos a la siguiente
				$webId = $web->webId;
				if($websTerminadas[$webId] == false){
				// a la primera ocurrencia de web no terminada
				//sabemos que esa es la que tiene que seguir contestando
					$webAContestar = $webId;
					break;
				}
			}

			$lastAnsweredQuestionId = -1;
			$firstQ = -1;
			$lastQ = -1;
			$studentprogressId = -1;
			$grade = 999;
			//Vamos a last answeredQuestionId dentro de studentprogress
			foreach ($studentprogress as $key => $sp) {
				if($sp->webId == $webAContestar){
					$lastAnsweredQuestionId = $sp->lastAnsweredQuestion;
					$firstQ = $sp->firstQuestion;
					$lastQ = $sp->lastQuestion;
					$studentprogressId = $sp->id;
					$grade = $sp->lastAnswerGrade;
				}
			}

			//Si es la primera vez que se contesta la competencia el ultimo "grade" es -1
			if($grade == 999) $grade = 0;

			// echo "lastAnsweredQuestionId";
			// var_dump($lastAnsweredQuestionId);
			// var_dump($firstQ);
			// var_dump($lastQ);

		    //Si el ID de la ultima pregunta contestada es -1 quere decirque aun no ha contestado nada
			if($lastAnsweredQuestionId == '-1'){
				//La funcion debe regresar la primer pregunta
				$nextQuestion = null;
				$sql = "SELECT * FROM  questionsforstudent where id = ?";
				if(!$this->_db->query($sql, array($firstQ))->error()) {
					if($this->_db->count()) {
						$nextQuestion = $this->_db->first();
					}
				}

				$response = array();
				$response['competenceId'] = $competenceId;
				$response['studentProgressId'] = $studentprogressId;
				$response['webId'] = $webAContestar;
				$response['nextQuestion'] = $nextQuestion;


				return $response;
			}

			//Vamos a questionsforstudent y buscamos el nivel en el que se encuentra esa pregunta.
			$lastAnsweredQuestion = array();
			$sql = "SELECT * FROM  questionsforstudent where id = ?";
			if(!$this->_db->query($sql, array($lastAnsweredQuestionId))->error()) {
				if($this->_db->count()) {
					$lastAnsweredQuestion = $this->_db->first();
				}
			}

			//El siguiente nivel depende del grado que le manden como parametro
			//El grado es la ponderacion asignada a la respuesta para esa pregunta en esa combinacion de red-competencia
			$level = intval($lastAnsweredQuestion->level);
			$nextLevel = $level+$grade;

			//Buscamos ahí también la primerpregunta con “answered”=false del siguiente nivel
			$nextQuestion = null;
			$sql = "SELECT * FROM  questionsforstudent where level = $nextLevel AND id BETWEEN $firstQ AND $lastQ AND answered = false";
			if(!$this->_db->query($sql)->error()) {
				if($this->_db->count() == 0) {
					//Ya no hay preguntas de ese nivel para esa red, entonces te BLOQUEO
					$nextQuestion = 'blocked';
					//AGREGAR RED Y NIVEL
					$web = $w->getWeb($webAContestar);
					$webName = $web->name;
					var_dump($nextLevel);
					var_dump($webName);
					$c->blockCompetence($studentId, $groupId, $competenceId, $nextLevel, $webName);
				}else{
					$nextQuestion = $this->_db->first();
				}
			}


			//Buscar el studentProgressId para la red que estoy contestando

			$response = array();
			$response['competenceId'] = $competenceId;
			$response['studentProgressId'] = $studentprogressId;
			$response['webId'] = $webAContestar;
			$response['nextQuestion'] = $nextQuestion;

			return $response;

		}catch(PDOException $e){
			die($e->getMessage());
		}

	}

}
