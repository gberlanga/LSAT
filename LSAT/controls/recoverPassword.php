<?php
require '../core/init.php';

if(Input::exists()) {	
	$validate = new Validate();
	$validation = $validate->check($_POST, array(
		'mail' => array(
			'required' => true,
			'min' => 8,
			'max' => 100)
		));

	if($validation->passed()) {
		$user = new User(Input::get('mail'));
		if(!$user->exists()){
			$response = array( "message" => "error");
			echo json_encode($response);
			return;
		}
		
		$salt = Hash::salt(32);
		$password = substr(md5(microtime()),rand(0,26),8);  //un string random de 8 caracteres

		try {
			$mailer = new Mailer();

			$user->update(array(
				'password' 	=> Hash::make($password, $salt),
				'salt'		=> $salt,
				), $user->data()->id);

			$to = Input::get('to');
			$subject = "LSAT - Recuperar contrasena";
			$message = "Tu nueva contrasena es: $password \n\n Este es un email automático, por favor no responda a este mensaje";
			$mailer->send($to, $subject, $message);

			$response = array( "message" => "success");
			echo json_encode($response);	

		} catch(Exception $e) {
			die($e->getMessage());
		}

	}else{
		echo "error";	
	} 
}
else{
	echo "error Input not exists";	
} 
?>