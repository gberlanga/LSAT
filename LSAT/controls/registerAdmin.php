<?php
require '../core/init.php';

$user = new User();
$salt = Hash::salt(32);
echo "aqui <br/>";	
try {

	$user->create(array(
		'mail' 	=> "lsatitesm@gmail.com",
		'password' 	=> Hash::make("123", $salt),
		'salt'		=> $salt,
		'username' 		=> "Admin",
		'role'      => 'admin'
		));

	echo "success";	

} catch(Exception $e) {
	die($e->getMessage());
}


?>