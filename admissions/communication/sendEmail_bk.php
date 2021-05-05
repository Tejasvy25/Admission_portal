<?php

include("class.phpmailer.php"); 
include dirname(dirname(__FILE__)).'/ma_config.php';
include dirname(dirname(__FILE__)).'/db_connect.php';


if(!isset($_POST['email']) || $_POST['email']=="")
{
	echo "no email";
	exit;
}
$email = $_POST['email'];
$status = "";
if ($stmt = $mysqli->prepare("SELECT status FROM ".prefix."ma_users WHERE email = ?")) { 
      $stmt->bind_param('s', $email); 
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($status); 
      $stmt->fetch();

	  
			$mail = new PHPMailer(); 
			$mail->IsSMTP(); 
			$mail->SMTPAuth   = true;                  
			$mail->SMTPSecure = "ssl"; 
			$mail->Host       = "smtp.gmail.com";      
			$mail->Port       = 465;                  
			$mail->Username   = "tentudileepkumar@msitprogram.net";
			$mail->Password   = "TDKUMAR@350";           
			$mail->SMTPKeepAlive = true;
			$mail->Timeout =100000000;
			$mail->From       = "msitadmissions@gmail.com"; 
			$mail->FromName   = "MSIT Admissions 2014";
			
			$mail->WordWrap = 40;                               
			$mail->IsHTML(true);                              
			$mail->Subject  =  "MSIT Admissions 2014 Account Activation"; 
			$mail->Body     =  "Please Activate your Account. Clicke here ".SERVER."authentication/activation_process.php?email=".$email."&key=".$status;  

			$mail->AddAddress($email); 
			$flag=1;
			while($flag==1)
			{
				if($mail->Send())
				{
					$mail->ClearAddresses();
					$email_status = "sent";
					if ($insert_stmt = $mysqli->prepare("update ".prefix."ma_users set email_status=? where email = ? ")) {   			
							$insert_stmt->bind_param('ss', $email_status, $email ); 
							$insert_stmt->execute();
							$insert_stmt->close();
					}
					$flag=0;
				}
			}
			echo "sent";
}
else
{	
	$email_status = "failed";
	if ($insert_stmt = $mysqli->prepare("update ".prefix."ma_users set email_status=? where email = ? ")) {   			
			$insert_stmt->bind_param('ss', $email_status, $email ); 
			$insert_stmt->execute();
			$insert_stmt->close();
	}
}
	?>
