<?php

class Groups {
	private $_db,
	$_data = array(),
	$_tableName = "groups";

	public function __construct() {
		$this->_db = DB::getInstance();
	}

	public function find($groupId = null) {
		// Check if group_id is specified
		if($groupId) {
			$data = $this->_db->get($this->_tableName, array('id', '=', $groupId));

			if($data->count()) {
				return true;
			}
		}
		return false;
	}

	public function getGroupsForTeacher($teacherId = null){
		if ($teacherId == null) return;

		$db = $this->_db->get($this->_tableName, array('professor', '=', $teacherId));

		if($db && $db->count()) {
			return $db->results();
		}

		return array();
	}

	public function getGroupByName($groupname = null){
		if ($groupname == null) return false;

		$db = $this->_db->get($this->_tableName, array('name', '=', $groupname));

		if($db && $db->count()) {
			return $db->first();
		}

		return false;
	}

	public function getGroupById($groupId= null){
		if ($groupId == null) return false;

		$db = $this->_db->get($this->_tableName, array('id', '=', $groupId));

		if($db && $db->count()) {
			return $db->first();
		}

		return false;
	}

	public function verifyGroupOwnership($groupId, $teacherId){
		$group = $this->getGroupById($groupId);
		if($group->professor == $teacherId){
			return true;
		}
		return false;
	}

	public function getCompetencesForGroup($groupId = null){
		if ($groupId == null) return;

		$competences = new Competence();
		$competencesIds = $competences->getCompetencesIdsForGroup($groupId);
		$details = $competences->getCompetencesDetails($competencesIds);

		return $details;
	}

	//Regresa una lista separada por comas de los ids de los alumnos que estan inscritos a ese grupo
	public function getAllStudentsIdsFromGroup($groupId = null) {

		$sql = "SELECT GROUP_CONCAT(studentId SEPARATOR ', ') as studentIds FROM studentsingroup WHERE groupId = $groupId GROUP BY groupId";
		if(!$this->_db->query($sql, array())->error()) {
			if($this->_db->count()) {
				return $this->_db->first()->studentIds;
			}
		}else{
			return "";
		}
	}

	public function getAllStudentsFromGroup($groupId = null){
		$studentIds = $this->getAllStudentsIdsFromGroup($groupId);
		$u = new User();
		return $u->getStudentsUserData($studentIds);
	}

	public function create($fields = array()) {
		if(!$this->_db->insert($this->_tableName, $fields)) {
			throw new Exception('There was a problem creating the group.');
		}
	}

	public function update($groupId, $fields = array()) {
		if(!$this->_db->update($this->_tableName, $groupId, $fields)) {
			throw new Exception('There was a problem updating.');
		}
	}

	public function data() {
		return $this->_data;
	}

}

?>
