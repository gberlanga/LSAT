<?php
require 'core/init.php';

if(Input::exists('get') ) {

	$user = new User();
	//Id del usuario a editar
	$uId = Input::get('uId');
	$uId = trim($uId);
	var_dump($uId);

	if($uId == "" || !is_numeric($uId)){
		Redirect::to('./index.php');
	}
	if(!$user->find($uId)){
  		//El usuario no existe
		Redirect::to('./index.php');
	}

	//Eliminar el usuario de la base de datos

	$user->delete($uId);


}else{
	Redirect::to('./index.php');
}

?>