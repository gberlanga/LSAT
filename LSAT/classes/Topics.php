<?php

class Topics {
	private $_db,
	$_data = array(),
	$_tableName = 'topic';

	public function __construct() {
		$this->_db = DB::getInstance();
	}

	public function create($name) {
		if(!$this->_db->insert($this->_tableName, array("name" => $name))) {
			throw new Exception('There was a problem creating the topic.');
		}
	}

	public function getAllTopics() {
			$sql = "SELECT * FROM topic";
			if(!$this->_db->query($sql)->error()) {
				if($this->_db->count()) {
					return $this->_db->results();
				}
			}
			return array();
		}

}
