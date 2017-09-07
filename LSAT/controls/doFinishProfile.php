<?php
require '../core/init.php';

if(Input::exists()) {
		
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'telefono' => array(
				'required' => true,
				)
			));

		if($validation->passed()) {
			
			$user = new User();
			try {

				$telefono = Input::get('telefono');
				$user->update(array(
						'telefono' => $telefono
					));
				
				echo "success";	

			} catch(Exception $e) {
				die($e->getMessage());
				//die("error");
			}
		} 
}
?>