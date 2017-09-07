<?php

class Levels {
	private $_db,
	$_data = array(),
	$_tableName = 'difficulty';

	public function __construct() {
		$this->_db = DB::getInstance();
	}

	public function create($name) {
		if(!$this->_db->insert($this->_tableName, array("name" => $name))) {
			throw new Exception('There was a problem creating the level.');
		}
	}

	public function getAllLevels() {
			$sql = "SELECT * FROM difficulty";
			if(!$this->_db->query($sql)->error()) {
				if($this->_db->count()) {
					return $this->_db->results();
				}
			}
			return array();
		}

}
