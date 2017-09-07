<?php

/*
	Estamos usando la libreria de PHPMailer para enviar los correos.
	Pueden encontrar ejemplos y documentacion en la siguiente liga: (https://github.com/PHPMailer/PHPMailer)
*/

	class Mailer {
		private $_db;
		private $systemMail;
		private $systemName;

		public function __construct($token = null) {
			$this->_db = DB::getInstance();
			$this->systemMail = 'lsatitesm@gmail.com';
			$this->systemMailPassword = 'laurasanchez';
			$this->systemName = 'LSAT';
		}

		/*Enviar un mail generico*/
		public function send ($to, $subject, $message) {
			//Create a new PHPMailer instance
			$mail = new PHPMailer();
			//Set who the message is to be sent from
			$mail->setFrom($this->systemMail, $this->systemName);
			//Set an alternative reply-to address
			$mail->addReplyTo($this->systemMail, $this->systemName);
			//Set who the message is to be sent to
			$mail->addAddress($to, '');
			//Set the subject line
			$mail->Subject = $subject;
			//Read an HTML message body from an external file, convert referenced images to embedded,
			//convert HTML into a basic plain-text alternative body
			$mail->msgHTML($message, dirname(__FILE__));
			$mail->AltBody = $message;

			$mail->IsSMTP();
			$mail->Mailer = 'smtp';
			$mail->SMTPAuth = true;
			$mail->Host = 'smtp.gmail.com'; 
			$mail->Port = 465;
			$mail->SMTPSecure = 'ssl';
			$mail->Username = $this->systemMail;
			$mail->Password = $this->systemMailPassword;
		$mail->IsHTML(true); // For HTML formatted mails

		//send the message, check for errors
		if (!$mail->send()) {
			echo "Mailer Error: " . $mail->ErrorInfo;
		}
	}


	/*Enviar un mail si el alumno respondio mal todas las preguntas de un nivel
	Se le envia mail al profesor responsable de ese grupo y al alumno */
	public function sendBlockedMails($studentId, $groupId, $competenceId, $level, $webName) {
		//Obtener los datos del maestro, del estudiante, del grupo y de la competencia
		$u = new User();
		$g = new Groups();
		$c = new Competence();

		$competence = $c->getCompetence($competenceId);
		$group = $g->getGroupById($groupId);
		$u->find($group->professor);
		$teacher = $u->data();
		$u->find($studentId);
		$student = $u->data();

		$teacherMailTemplate = file_get_contents('./includes/templates/mails/levelFailed.html', true);
		$studentMailTemplate = file_get_contents('./includes/templates/mails/levelFailedStudent.html', true);

		$teacherMailHtml = str_replace(
			array('$idNumber', '$username', '$studentName', '$group', '$competence', '$web', '$level'), 
			array($student->idNumber, $teacher->username, $student->username, $group->name, $competence->name, $webName, $level), 
			$teacherMailTemplate
			);

		$studentMailHtml = str_replace(
			array('$competence'), 
			array($competence->name), 
			$studentMailTemplate
			);

		//Enviar el correo al maestro
		$to = $teacher->mail;
		$subject = "LSAT - Alumno bloqueado";
		$message = $teacherMailHtml;
		$this->send($to, $subject, $message);

		//Enviar el correo al estudiante				
		$to = $student->mail;
		$subject = "LSAT - Asesoria necesaria";
		$message = $studentMailHtml;
		$this->send($to, $subject, $message);
	}


}