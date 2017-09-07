<?php

class Answer {
	private $_db,
	$_data = array(),
	$_tableName = 'answer';

	public function __construct() {
		$this->_db = DB::getInstance();
	}

	public function create($fields = array()) {
		if(!$this->_db->insert($this->_tableName, $fields)) {
			throw new Exception('There was a problem creating the answer.');
		}
	}

	public function update($answerId, $fields = array()) {
		if(!$this->_db->update($this->_tableName, $answerId, $fields)) {
			throw new Exception('There was a problem updating.');
		}
	}

	public function getAnswer($id) {
			$sql = "SELECT * FROM answer WHERE id = ?";
			if(!$this->_db->query($sql, array($id))->error()) {
				if($this->_db->count()) {
					return $this->_db->results();
				}
			}
			return array();
		}

	

	public function getAnswersForQuestionList($questions = array()) {
		if (count($questions) == 0) return;

		$data = array();
		$answers = array();

		foreach ($questions as $q) {
			$answersIds = array($q->optionA, $q->optionB, $q->optionC, $q->optionD);

			foreach ($answersIds as $item){

				$answer = $this->getAnswer($item);
				array_push($answers, $answer);
			}
			$data[$q->id] = $answers;
			$answers = array();
		}

		return $data;
	}

	public function data() {
		return $this->_data;
	}
}
