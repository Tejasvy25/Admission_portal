<?php
include 'securelogin_functions.php';
include 'functions.php';
include dirname(dirname(__FILE__)).'/ma_config.php';
include dirname(dirname(__FILE__)).'/db_connect.php';
include("class.phpmailer.php"); 
$post = (!empty($_POST)) ? true : false;
if($post)
{
	
	$usertype = "student";
	$name = stripslashes($_POST['name']);
	$email = trim($_POST['email']);
	$password = stripslashes($_POST['p']);
	$board = $_POST['board'];
	$boardno = $_POST['serialno'];
	$phone = "0";
	if(isset($_POST['phone']))
		$phoneno = $_POST['phone'];
	$status = md5(uniqid(rand(), true));
	$profileupdate = "no";
	$application_status = "no";
	$payment_status = "no";
	$photo_status = "no";
	$educationdetails_status = "no";
	$payment_type = "none";

	$i = 0;
	$j = 0;
	//$query = "select * from ma_users where email = '".$email."' and usertype = '".$usertype."'";
	$query = "select * from ma_users where email = '".$email."'";
	$query1 = "select * from ma_users where board_name = '".$board."' and board_number = '".$boardno."'";
	if($stmt = $mysqli->prepare($query))
	{
		$stmt->execute();
		while ($stmt->fetch()) 
		{
			$i = 1;
		}
		$stmt->close();
	}
	
	if($stmt = $mysqli->prepare($query1))
	{
		$stmt->execute();
		while ($stmt->fetch()) 
		{
			$j = 1;
		}
		$stmt->close();
	}
	
	if($i==1)
	{
			if($j==1)
			{	
				echo "invalid_email_board";
				exit;
			}
			else
			{
				echo "invalid_email";
				exit;
			}
	}
	else if($j==1)
	{
		echo "invalid_board";
		exit;
	}
	else
	{	
		$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
		$password = hash('sha512', $password.$random_salt);
 
		if ($insert_stmt = $mysqli->prepare("INSERT INTO ma_users (username, email, password, salt, usertype, status, board_name, board_number, phone_no, photo_status, profileupdate, educationdetails_status, application_status, payment_status, payment_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {    
			$insert_stmt->bind_param('sssssssssssssss', $name, $email, $password, $random_salt, $usertype, $status, $board, $boardno, $phoneno, $photo_status, $profileupdate, $educationdetails_status, $application_status, $payment_status,$payment_type); 
			$insert_stmt->execute();
			$insert_stmt->close();
		}
		else
		{
			echo "Unable to Register at this time.<br/>Please call us to Help you.";
			exit;
		}

			if ($insert_stmt = $mysqli->prepare("INSERT INTO ma_user_profile (email, full_name) VALUES (?, ?)")) {   			
			$insert_stmt->bind_param('ss', $email, $name); 
			$insert_stmt->execute();
			$insert_stmt->close();
			}
			
			if ($insert_stmt = $mysqli->prepare("INSERT INTO ma_user_education_details (email) VALUES (?)")) {   			
			$insert_stmt->bind_param('s', $email); 
			$insert_stmt->execute();
			$insert_stmt->close();
			}
			
			if ($insert_stmt = $mysqli->prepare("INSERT INTO ma_user_gat_exam_details (email) VALUES (?)")) {   			
			$insert_stmt->bind_param('s', $email); 
			$insert_stmt->execute();
			$insert_stmt->close();
			}
			
			$mail = new PHPMailer(); 
			$mail->IsSMTP(); 
			$mail->SMTPAuth   = true;                  
			$mail->SMTPSecure = "ssl"; 
			$mail->Host       = "smtp.gmail.com";      
			$mail->Port       = 465;                  
			$mail->Username   = "tentudileepkumar@msitprogram.net";
			$mail->Password   = "TDKUMAR@350";           
			$mail->SMTPKeepAlive = true;
			$mail->Timeout =10000000;
			$mail->From       = "msitadmissions@gmail.com"; 
			$mail->FromName   = "MSIT Admissions";
			
			$mail->WordWrap = 40;                               
			$mail->IsHTML(true);                              
			$mail->Subject  =  "MSIT Admissions 2014 Account Activation"; 
			$mail->Body     =  "Please Activate your Account. Clicke here http://localhost/new/msit/Jan1/Jan1/v3/authentication/activation_process.php?email=".$email."&key=".$status;  

			$mail->AddAddress($email); 
			$flag=0;
			if(!$mail->Send())
			{
				$flag=1;
			}
			$mail->ClearAddresses();
	
			if($flag==1)
			{
				echo "Error in sending Email";
				exit;
			}*/
			$ch = curl_init(SERVER.'communication/sendEmail.php?');
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "email=".$email);
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 1);
			curl_exec($ch);
			curl_close($ch);


			sec_session_start();
			$login_usertype = "student";
			$login_email = trim($_POST['email']);
			$login_password = stripslashes($_POST['p']);
	
			if(login($login_email, $login_password, $login_usertype, $mysqli) == true) 
			{
				echo 'login_success';
				exit;
			} 
			else 
			{
				echo 'login_failed';
				exit;
			}
		
	}	
}
else 
{ 
	echo 'invalid';
	exit;
}
?>
