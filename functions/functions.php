<?php 
	function test_input($data) {
	  $data = trim($data);
	  $data = stripslashes($data);
	  $data = strip_tags($data);
	  $data = htmlspecialchars($data);
	  return $data;
	}

	function checkNumber($number) {
	    //Lets really know if the input is not empty, which if it is, return false
	    if(!$number) {
	        return false;
	    }
	    //Checking if its really numerics
	    elseif(!is_numeric($number)) {
	        return false;
	    }
	    //Checking if number starts with 080, 090, 070 and 081
	    elseif(!preg_match('/^080/', $number) and !preg_match('/^070/', $number) and !preg_match('/^090/', $number) and !preg_match('/^081/', $number)) {
	        return false;
	    }
	    //Check if the length is 11 digits
	    elseif(strlen($number)!==11) {
	        return false;
	    }
	    //Every requirements are made
	    else {
	        return true;
	    }
	}
	
	function setValue( $fieldName ) {
		if ( isset( $_POST[$fieldName] ) ) {
		echo $_POST[$fieldName];
		}
	}

	/*require 'PHPMailer/class.phpmailer.php';
	require 'PHPMailer/PHPMailerAutoload.php';
	$mail = new PHPMailer;

	$mail->SMTPDebug = 3;                               // Enable verbose debug output

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'mail.mabamidje.com';  					  // Specify main and backup SMTP servers
	$mail->SMTPAuth = false;                               // Enable SMTP authentication
	$mail->Username = 'autoReport@mabamidje.com';                 // SMTP username
	$mail->Password = 'pass123@ADMIN';                           // SMTP password
	$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 465;                                    // TCP port to connect to

	$mail->setFrom('autoReprot@mabamidje.com', 'Mailer');
	$mail->addAddress('okoroefe16@gmail.com', 'Joe User');     // Add a recipient
	$mail->addReplyTo('okoroefe18@gmail.com', 'Information');
	
	$mail->Subject = 'Here is the subject';
	$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

	if(!$mail->send()) {
	    echo 'Message could not be sent.';
	    echo 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
	    echo 'Message has been sent';
	}*/
?>