<?php
require 'core/init.php';

if(Input::exists('get') ) {

	$user = new User();
	$g = new Groups();
	//Id del usuario a editar
	$studentId = Input::get('sId');
	$groupId = Input::get('gId');
	$studentId = trim($studentId);
	$groupId = trim($groupId);
	$db = DB::getInstance();

	if($studentId == "" || !is_numeric($studentId) || $groupId == "" || !is_numeric($groupId)){
		Redirect::to('./index.php');
	}
	if(!$user->find($studentId)){
  		//El usuario no existe
		Redirect::to('./index.php');
	}
	if(!$g->find($groupId)){
  		//El grupo no existe
		Redirect::to('./index.php');
	}

	try{
		$c = new Competence();

		//Eliminar el avance de ese alumno en las competencias del grupo
		$competenceIds = $c->getCompetencesIdsForGroup($groupId);
		foreach($competenceIds as $competenceId) {
			var_dump($competenceId);
			$c->deleteStudentProgress($studentId, $groupId, $competenceId);
		}

		//Borrar al alumno de este grupo
		$sql = "DELETE FROM studentsingroup WHERE groupId = ? AND studentId = ?";

		if($db->query($sql, array($groupId, $studentId))->error()) {
			throw new Exception('There was a problem deleting studentsingroup.');
		}

	}

	catch(Exception $e) {
		var_dump($e);
	}

	Redirect::to('./editGroup.php?g='.$groupId);

}else{
	Redirect::to('./editGroup.php?g='.$groupId);
}

?>
