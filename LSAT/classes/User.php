<?php

class User {
	private $_db,
	$_sessionName = null,
	$_cookieName = null,
	$_data = array(),
	$_isLoggedIn = false,
	$_userTableName = 'user';

	public function __construct($user = null) {
		$this->_db = DB::getInstance();

		$this->_sessionName = Config::get('session/session_name');
		$this->_cookieName = Config::get('remember/cookie_name');

		// Check if a session exists and set user if so.
		if(Session::exists($this->_sessionName) && !$user) {
			$user = Session::get($this->_sessionName);

			if($this->find($user)) {
				$this->_isLoggedIn = true;
			} else {
				$this->logout();
			}
		} else {
			$this->find($user);
		}
	}

	public function exists() {
		return (!empty($this->_data)) ? true : false;
	}

	public function find($user = null) {
		// Check if user_id is specified and grab details
		if($user) {
			$field = (is_numeric($user)) ? 'id' : 'mail';
			$data = $this->_db->get($this->_userTableName, array($field, '=', $user));

			if($data->count()) {
				$this->_data = $data->first();
				return true;
			}
		}
		return false;
	}

	public function getByIdNumber($idNumber){
		$db = $this->_db->get($this->_userTableName, array('idNumber', '=', $idNumber));
		if($db->count()) {
			return $db->first();
		}
		return false;
	}

	public function create($fields = array()) {
		if(!$this->_db->insert('user', $fields)) {
			throw new Exception('There was a problem creating an account.');
		}
	}

	public function delete($id){
		$sql = "DELETE FROM user WHERE id = ?";

		if($this->_db->query($sql, array($id))->error()) {
			throw new Exception('There was a problem deleting the user.');
		}
	}

	public function checkIsLoggedIn(){
		//el usuario no esta logeado
		if(!$this->isLoggedIn()) {
			Redirect::to('index.php');
			exit();
		}

		return true;
	}

	public function checkIsValidUser($role = ""){
		//Si no existe usuario para ese mail o el usuario si existe pero no esta logeado redirigimos a index
		if(!$this->exists() || !$this->isLoggedIn()) {
			Redirect::to('index.php');
			exit();
		}

		if($role != "" && $this->data()->role != $role){
			Redirect::to('index.php');
			exit();
		}
		return true;
	}

	public function redirectToDefault(){
		$page = "index.php";
		if($this->data()->role == "admin"){
			$page = 'registerTeacher.php';
		}
		else if($this->data()->role == "teacher"){
			$page = 'groups.php';
		}
		else if($this->data()->role == "student"){
			$page = 'dashboard.php';
		}

		Redirect::to($page);

	}

	/*Regresa la informacion de los grupos en los que esta inscrito un alumno y las cometencias que tiene asignadas*/
	public function getInformationForStudent($studentId = null) {
		$sql = "SELECT U.username as professorName, G.id as groupId, G.name as groupName, GROUP_CONCAT(C.name SEPARATOR ', ')as competences,  GROUP_CONCAT(CONVERT(C.id, CHAR(8)) SEPARATOR ', ') as competencesIds  FROM
		`studentsingroup` SG JOIN `groups` G ON  SG.groupId = G.id
		JOIN `competenceingroup` CG ON G.id = CG.groupId
		JOIN `competence` C ON CG.competenceId = C.id
		JOIN `user` U ON U.id = G.professor
		WHERE SG.studentId = $studentId
		GROUP BY groupId";

		if(!$this->_db->query($sql, array())->error()) {
			if($this->_db->count()) {
				return $this->_db->results();
			}
		}
	}

	public function update($fields = array(), $id = null) {
		if(!$id && $this->isLoggedIn()) {
			$id = $this->data()->id;
		}

		if(!$this->_db->update($this->_userTableName, $id, $fields)) {
			throw new Exception('There was a problem updating.');
		}
	}

	public function login($username = null, $password = null, $remember = false) {
		if(!$username && !$password && $this->exists()) {
			Session::put($this->_sessionName, $this->data()->id);
		} else {
			$user = $this->find($username);
			if($user) {
				if($this->data()->password === Hash::make($password, $this->data()->salt)) {
					//var_dump('iguales');
					Session::put($this->_sessionName, $this->data()->mail);
					$this->_isLoggedIn = true;

					if($remember) {
						$hash = Hash::unique();
						$hashCheck = $this->_db->get('users_session', array('user_id', '=', $this->data()->id));

						if(!$hashCheck->count()) {
							$this->_db->insert('users_session', array(
								'user_id' => $this->data()->id,
								'hash' => $hash
								));
						} else {
							$hash = $hashCheck->first()->hash;
						}

						Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
					}

					return true;
				}
			}
		}

		return false;
	}

	public function isLoggedIn() {
		return $this->_isLoggedIn;
	}

	public function data() {
		return $this->_data;
	}

	public function logout() {
		$this->_db->delete('users_session', array('user_id', '=', $this->data()->id));

		Cookie::delete($this->_cookieName);
		Session::delete($this->_sessionName);
	}


	public function getUsersByRole($role){
		$roles = Config::get('roles');
		$error = "";
		if (! in_array($role, $roles)) {
			$error = "Invalid role";
		}

		$users = array();

		$db = $this->_db->get($this->_userTableName, array('role', '=', $role));
		if($db->count()) {
			$users = $db->results();
		}

		return $users;

	}


	public function getStudentProgress($studentId, $groupId, $competenceId){

		//Traer los registros de studentrecord y studentprogress que cumplan con los tres ids
		$studentprogress = array();
		$sql = "SELECT * FROM studentrecord sr JOIN studentprogress sp ON sr.studentProgressId = sp.id WHERE studentId = ? AND groupId = ? AND competenceId = ?";

		if(!$this->_db->query($sql, array($studentId, $groupId, $competenceId))->error()) {
			if($this->_db->count()) {
				$studentprogress  = $this->_db->results();
			}
		}
		return $studentprogress;
	}


	public function studentBelongInGroup($studentId = null, $groupId = null) {

		$sql = "SELECT * FROM studentsingroup WHERE studentId = $studentId AND groupId = $groupId";
		if(!$this->_db->query($sql, array())->error()) {
			if($this->_db->count() && $this->_db->count() == 1) {
				return true;
			}
		}else{
			return false;
		}
	}


	public function getBlockedStudents($groupsIds = null) {
		$idList = implode(",", $groupsIds);
		$studentsBlockedByGroup = array();

		foreach($groupsIds as $groupId) {
			$sql = "SELECT U.username, U.id as studentId, SR.competenceId, C.name as competenceName, G.name as groupName FROM
			`user` U JOIN `studentrecord` SR ON U.id = SR.studentId
			JOIN `competence` C ON SR.competenceId = C.id
			JOIN `groups` G ON G.id = SR.groupId
			WHERE groupId = $groupId AND isBlocked=1 GROUP BY studentId";
			if(!$this->_db->query($sql, array())->error()) {
				if($this->_db->count()) {
					$studentsBlockedByGroup[$groupId] = $this->_db->results();
				}
			}
		}

		return $studentsBlockedByGroup;
	}

	public function getStudentsUserData($studentIdList = ""){

		$sql = "SELECT id, username, mail, idNumber FROM user WHERE id IN ($studentIdList)";
		if(!$this->_db->query($sql, array())->error()) {
			if($this->_db->count()) {
				return $this->_db->results();
			}
		}else{
			return array();
		}
	}

}
