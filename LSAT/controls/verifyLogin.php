<?php
require '../core/init.php';

if(Input::exists()) {

	$user = new User();

	$remember = (Input::get('remember') === 'on') ? true : false;
	$login = $user->login(Input::get('username'), Input::get('password'), $remember);
	$response = array();

	if($login) {
		$page = "index.php";

		if($user->data()->role == "admin"){
			$page = 'registerTeacher.php';
		}
		else if($user->data()->role == "teacher"){
			$page = 'groups.php';
		}
		else if($user->data()->role == "student"){
			$page = 'dashboard.php';
		}

		$response = array( "message" => "success", "page" => $page);
	} else {
		$response = array( "message" => "error");
	}

	echo json_encode($response);

}else{
	echo "error";
}

?>