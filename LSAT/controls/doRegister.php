<?php
require '../core/init.php';

if(Input::exists()) {
	if(Token::check(Input::get('token'))) {
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'mail' => array(
				'required' => true,
				'min' => 8,
				'max' => 100,
				'unique' => 'usuarios'),
			'password' => array(
				'required' => true,
				'min' => 6),
			'password_again' => array(
				'required' => true,
				'matches' => 'password'),
			'name' => array(
				'required' => false,
				'min' => 2,
				'max' => 50)
			));

		if($validation->passed()) {
			$user = new User();
			$salt = Hash::salt(32);

			try {

				$var = Input::get('dob');
				$dob = str_replace('/', '-', $var);

				$user->create(array(
					'mail' 	=> Input::get('mail'),
					'password' 	=> Hash::make(Input::get('password'), $salt),
					'salt'		=> $salt,
					'nombre' 		=> Input::get('name'),
					'fechaRegistro'	=> date('Y-m-d H:i:s'),
					'fechaNacimiento' => date('Y-m-d', strtotime($dob)),
					'group'		=> 1
					));

				$user = new User();

				$remember = false;
				$login = $user->login(Input::get('mail'), Input::get('password'), $remember);

				//Redirect::to('dashboard.php');
				echo "success";	

			} catch(Exception $e) {
				die($e->getMessage());
			}

		} 
	}
}
?>