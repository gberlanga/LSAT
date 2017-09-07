<?php
require 'core/init.php';

if(Input::exists('get') ) {

	$user = new User();
	//Id del usuario a editar
	$uId = Input::get('uId');
	$uId = trim($uId);
	$db = DB::getInstance();

	if($uId == "" || !is_numeric($uId)){
		Redirect::to('./index.php');
	}
	if(!$user->find($uId)){
  		//El usuario no existe
		Redirect::to('./index.php');
	}

	//Eliminar el usuario de la base de datos
	// paso 1
	$user->delete($uId);

	//paso 2

	try{
			$g = new Groups();
			$c = new Competence();
			$groups = $g->getGroupsForTeacher($uId);

			//Tenemos las competencias por cada grupo y su lista de estudiantes
			$gInfo = array();
			foreach($groups as $group) {
				$cIds = $c->getCompetencesIdsForGroup($group->id);
				$sIds = $g->getAllStudentsIdsFromGroup($group->id);
				$sIds = explode(',', $sIds);
				$gInfo[$group->id] = array('students'=>$sIds, "competences" =>$cIds);
			}

			//Borramos todo avance de cada alumno en todas las competencias de todos los grupos de ese maestro
			var_dump($gInfo);
			foreach($gInfo as $groupId => $value) {
				$students = $value['students'];
				$competences = $value['competences'];
				foreach($competences as $competenceId) {
					foreach($students as $studentId){
						var_dump($studentId);
						var_dump($groupId);
						var_dump($competenceId);
						$c->deleteStudentProgress($studentId, $groupId, $competenceId);
					}
				}
			}

			//Borrar la relacion alumno-grupo para todos los grupos del maestro
			foreach($groups as $group) {
				$sql = "DELETE FROM studentsingroup WHERE groupId = ?";

				if($db->query($sql, array($group->id))->error()) {
					throw new Exception('There was a problem deleting studentsingroup.');
				}

				//Borrar tambien el grupo
				$sql = "DELETE FROM groups WHERE id = ?";

				if($db->query($sql, array($group->id))->error()) {
					throw new Exception('There was a problem deleting groups.');
				}
			}

			//
			$competences = $c->getCompetencesForTeacher($uId);
			foreach($competences as $competence) {
				$cId = $competence->id;
				$sql = "DELETE FROM competenceingroup WHERE competenceId = $cId";

				if($db->query($sql)->error()) {
					throw new Exception('There was a problem deleting competenceingroup.');
				}

				$sql = "DELETE FROM competence WHERE id = $cId";

				if($db->query($sql)->error()) {
					throw new Exception('There was a problem deleting competence.');
				}

				$websInCompetence = $c->getWebsInCompetenceIds($cId);
				var_dump($websInCompetence);
				$websInCompetenceIds = implode(",", $websInCompetence);

				$sql = "DELETE FROM answersinwebsincompetence WHERE webInCompetence IN ($websInCompetenceIds)";

				if($db->query($sql)->error()) {
					throw new Exception('There was a problem deleting answersinwebsincompetence.');
				}

				$sql = "DELETE FROM websincompetence WHERE competenceId = $cId";

				if($db->query($sql)->error()) {
					throw new Exception('There was a problem deleting websincompetence.');
				}

			}

	}

	catch(Exception $e) {
		var_dump($e);
	}

	Redirect::to('./manageTeachers.php');

}else{
	Redirect::to('./manageTeachers.php');
}

?>
