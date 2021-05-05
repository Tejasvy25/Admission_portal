<?php
include("class.phpmailer.php"); 
include dirname(dirname(__FILE__)).'/ma_config.php';
include dirname(dirname(__FILE__)).'/db_connect.php';
include 'securelogin_functions.php';
sec_session_start();
$post = (!empty($_POST)) ? true : false;
if($post)
{
	
	$appID = trim($_POST['appID']);
	$email = stripslashes($_POST['email']);
	$profileupdate = "no";
	$application_status = "no";
	$payment_status = "no";
	$photo_status = "no";
	$educationdetails_status = "no";
	$payment_type = "DD";
	$email_status = "inactive";
	
	$_SESSION['email'] = $email;
	$_SESSION['offlineAppID'] = $appID;
	$_SESSION['profileupdate'] = $profileupdate;
	$_SESSION['application_status'] = $application_status;
	$_SESSION['payment_status'] = $payment_status;
	$_SESSION['photo_status'] = $photo_status;
	$_SESSION['educationdetails_status'] = $educationdetails_status;
	
	$i = 0;
	$j = 0;
	$k = 0;
	//$query = "select * from ma_users where email = '".$email."' and usertype = '".$usertype."'";
	$query = "select * from ma_offline_users where email = '".$email."' and appID='".$appID."'";
	$query1 = "select * from ma_offline_details where appID = '".$appID."'"; 
	$query2 = "select * from ma_offline_details where email = '".$email."'"; 
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
	
	if($stmt = $mysqli->prepare($query2))
	{
		$stmt->execute();
		while ($stmt->fetch()) 
		{
			$k = 1;
		}
		$stmt->close();
	}
	
	if($i==1)
	{
			
				if ($stmt = $mysqli->prepare("select photo_status, profileupdate, educationdetails_status, application_status, payment_status, payment_type from ma_offline_users where email=? and appID=?")) {    
				$stmt->bind_param('ss', $email,$appID); 
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($photo_status, $profileupdate, $educationdetails_status, $application_status, $payment_status,$payment_type); 
				$stmt->fetch();
				$_SESSION['email'] = $email;
				$_SESSION['profileupdate'] = $profileupdate;
				$_SESSION['application_status'] = $application_status;
				$_SESSION['payment_status'] = $payment_status;
				$_SESSION['photo_status'] = $photo_status;
				$_SESSION['educationdetails_status'] = $educationdetails_status;
				echo "success";
				exit;
				}
		
	}
	else if($j==1)
	{
		echo "invalid_email";
		exit;
	}
	else if($k==1)
	{
		echo "invalid_email";
		exit;
	}
	
	else
	{	
		if ($insert_stmt = $mysqli->prepare("INSERT INTO ma_offline_users (appID, email, photo_status, profileupdate, educationdetails_status, application_status, payment_status, payment_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")) {    
			$insert_stmt->bind_param('ssssssss', $appID, $email, $photo_status, $profileupdate, $educationdetails_status, $application_status, $payment_status,$payment_type); 
			$insert_stmt->execute();
			$insert_stmt->close();
		}
		else
		{
			echo "Unable to Register at this time.<br/>Please call us to Help you.";
			exit;
		}
			if ($insert_stmt = $mysqli->prepare("INSERT INTO ma_offline_details (appID, email) VALUES (?, ?)")) {    
			$insert_stmt->bind_param('ss', $appID, $email);
			$insert_stmt->execute();
			$insert_stmt->close();
			}
			
			if ($insert_stmt = $mysqli->prepare("INSERT INTO ma_user_profile (email) VALUES (?)")) {   			
			$insert_stmt->bind_param('s', $email); 
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
			}
			$ch = curl_init(SERVER.'admin/communication/sendEmail.php?');
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "email=".$email);
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 1);
			curl_exec($ch);
			curl_close($ch);
			
			
			echo 'success';
			exit;
	}	
}
else 
{ 
	echo 'invalid';
	exit;
}
?>
