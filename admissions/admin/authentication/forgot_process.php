<?php
include 'securelogin_functions.php';
include 'functions.php';
include dirname(dirname(__FILE__)).'/db_connect.php';
include dirname(dirname(__FILE__)).'/ma_config.php';
include("class.phpmailer.php"); 
$post = (!empty($_POST)) ? true : false;
if($post)
{
	
	$emailID = stripslashes($_POST['email']);
	$status = md5(uniqid(rand(), true));
	$email_status = "No Response";
	$flag = 0;
	if ($stmt = $mysqli->prepare("SELECT username from ma_users WHERE email = ? ")) 
	{ 
		$stmt->bind_param('s', $emailID); 
		$stmt->execute();
		$stmt->bind_result($userName);
		while($stmt->fetch())
		{
			$flag=1;											
		}
		$stmt->close();													

		if($flag==0)
		{
			echo "We did not find any registrations with the given email Id. Please register. ";
			exit;
		}																					
		else
		{			
			if ($insert_stmt = $mysqli->prepare("INSERT INTO forgot_password VALUES (?, ?, ? ,?)")) 
			{    
				$insert_stmt->bind_param('ssss', $userName, $emailID,$status, $email_status); 
				$insert_stmt->execute();
				$insert_stmt->close();
				
				$mail = new PHPMailer(); 
				$mail->IsSMTP(); 
				$mail->SMTPAuth   = true;                  
				$mail->SMTPSecure = "ssl"; 
				$mail->Host       = "smtp.gmail.com";      
				$mail->Port       = 465;                  
				$mail->Username   = "*******";
				$mail->Password   = "*******";           
				$mail->SMTPKeepAlive = true;
				$mail->Timeout =100000000;
				$mail->From       = "msitadmissions@gmail.com"; 
				$mail->FromName   = "MSIT Admissions 2016";
				$mail->WordWrap = 40;                               
				$mail->IsHTML(true);                              
				$mail->Subject  =  "MSIT Admissions 2016 Account Activation"; 
				$mail->Body     =  "Please click on link to reset your Account. Click here ".SERVER."authentication/reset_process.php?userName=".$emailID."&key=".$status;  
				$mail->AddAddress($emailID); 
				$flag=1;
				while($flag==1)
				{
					if($mail->Send())
					{
						$mail->ClearAddresses();
						$email_status = "sent";
						if ($insert_stmt = $mysqli->prepare("update forgot_password set email_status=? where emailID = ? ")) 
						{   			
							$insert_stmt->bind_param('ss', $email_status, $emailID ); 
							$insert_stmt->execute();
							$insert_stmt->close();
						}
						$flag=0;
					}
				}
				echo "success";
				exit;
			}
			else
			{
				echo "Unable to send email at this time.<br/>Please call us to Help you.";
				exit;
			}	
		}	   
	}	
}
else 
{ 
	echo 'invalid';
	exit;
}
?>
