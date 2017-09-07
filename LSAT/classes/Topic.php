<?php

class Topic {
	private $_db,
	$_data = array(),
	$_tableName = "topic";

	public function __construct() {
		$this->_db = DB::getInstance();
	}

	public function getTopics() {
		$db = $this->_db->get($this->_tableName);

		if($db && $db->count()) {
			return $db->results();
		}

		return array();
	}

	public function data() {
		return $this->_data;
	}

}

?>
