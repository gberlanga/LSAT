<?php

class Web {
	private $_db,
	$_data = array(),
	$_tableName = "web";

	public function __construct() {
		$this->_db = DB::getInstance();
	}

	public function getWeb($webId = null) {
		if ($webId == null) return;

		$db = $this->_db->get($this->_tableName, array('id', '=', $webId));

		if($db && $db->count()) {
			return $db->first();
		}

		return null;

	}

	public function isWebReadyToUseInCompetence($webId = null) {

		$web = $this->getWeb($webId);

		if( $web!= null && $web->isPublished){
			return true;
		}

		return false;
	}

	public function getAllPublishedWebs() {
		$sql = "SELECT W.id, W.name, W.createdDate, W.isPublished, U.username as professor FROM web W JOIN user U ON W.professor = U.id WHERE isPublished = 1";
		if(!$this->_db->query($sql)->error()) {
			if($this->_db->count()) {
				return $this->_db->results();
			}
		}

		return null;

	}

	public function getWebsForTeacher($teacherId = null) {
		if ($teacherId == null) return;

		$db = $this->_db->get($this->_tableName, array('professor', '=', $teacherId));

		if($db && $db->count()) {
			return $db->results();
		}

		return array();
	}

	public function getQuestionsInWeb($webId = null) {
		if ($webId == null) return;

		$data = array();

		$db = $this->_db->get("questionsinweb", array('webId', '=', $webId));

		if($db && $db->count()) {
			foreach ($this->_db->results() as $q) {
				$data[$q->questionId] = $q->level;

			}

			return $data;
		}

		return array();

	}

	public function getQuestionsIds($webId = null) {
		if ($webId == null) return;

		$questionsIds = array();

		$sql = "SELECT questionId FROM questionsinweb WHERE webId = $webId";
		if(!$this->_db->query($sql, array())->error()) {
			if($this->_db->count()) {
				foreach ($this->_db->results() as $questionsInWeb) {
					foreach ($questionsInWeb as $q) {
					  array_push($questionsIds, $q);
					}
				}

				return $questionsIds;
			}
		}

		return array();

	}

	public function getLevelsInWeb($webId = null) {
		if ($webId == null) return;

		$levels = array();

		$sql = "SELECT DISTINCT level FROM questionsinweb WHERE webId = $webId";
		if(!$this->_db->query($sql, array())->error()) {
			if($this->_db->count()) {
				foreach ($this->_db->results() as $levelsInWeb) {
					foreach ($levelsInWeb as $l) {
					  array_push($levels, $l);
					}
				}

				return $levels;
			}
		}

		return array();

	}

	public function getWebIfValidAndEditable($webId = null) {
		if ($webId == null) return;

		$web = $this->getWeb($webId);

		//La red no existe
		if ($web == null) return false;

		//La red ya esta publicada
		if ($web->isPublished) return false;

		return $web;

	}

	public function create($fields = array()) {
		if(!$this->_db->insert($this->_tableName, $fields)) {
			throw new Exception('There was a problem creating the group.');
		}
	}

	public function update($webId, $fields = array()) {
		if(!$this->_db->update($this->_tableName, $webId, $fields)) {
			throw new Exception('There was a problem updating the web.');
		}
	}

	public function data() {
		return $this->_data;
	}

	public function addQuestionInWeb($questionId, $webId, $level){

		$values = array("questionId" => $questionId,
						"webId" => $webId,
						"level" => $level);

		if($this->_db->insert('questionsinweb', $values)) {
			return true;
		}

		return false;
	}

	public function deleteAllQuestionsInWeb($webId){

		if(!$this->_db->delete("questionsinweb", array("webId" , "=" , $webId))) {
			throw new Exception('There was a problem deleting all questions from the web.');
		}

	}


	public function getWebsInCompetenceId($webId = null, $competenceId = null) {
		if ($webId == null || $competenceId == null) return;

		$sql = "SELECT * FROM websincompetence WHERE competenceId = $competenceId AND webId = $webId";

		if(!$this->_db->query($sql)->error()) {
			if($this->_db->count()) {
				return $this->_db->first();
			}
		}

		return null;

	}

}

?>
