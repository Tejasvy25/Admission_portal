<?php
include 'securelogin_functions.php';
include 'functions.php';	
include 'db_connect.php';
include 'ma_config.php';
include("communication/class.phpmailer.php"); 
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
			if ($stmt = $mysqli->prepare("SELECT status from forgotPassword WHERE emailID = ? ")) 
			{ 
				$stmt->bind_param('s', $emailID); 
				$stmt->execute();
				$stmt->bind_result($status);
				$fgflag = 0;
				while($stmt->fetch())
				{
					$fgflag=1;											
				}
				$stmt->close();													
			}
			if($fgflag==0)
			{
				if ($insert_stmt = $mysqli->prepare("INSERT INTO forgotPassword VALUES (?, ? ,?)")) 
				{    
					$insert_stmt->bind_param('sss', $emailID,$status, $email_status); 
					$insert_stmt->execute();
					$insert_stmt->close();
					$fgflag = 1;
				}	
			}
			if($fgflag==1)
			{
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
				$mail->FromName   = "MSIT";
				$mail->WordWrap = 40;                               
				$mail->IsHTML(true);                              
				$mail->Subject  =  "MSIT Account Activation"; 
				$mail->Body     =  "Please click on link to reset your Account. Click here ".SERVER."reset_process.php?userName=".$emailID."&key=".$status;  
				$mail->AddAddress($emailID); 
				$flag=1;
				while($flag<=3)
				{
					if($mail->Send())
					{
						$mail->ClearAddresses();
						$email_status = "sent";
						if ($insert_stmt = $mysqli->prepare("update forgotPassword set email_status=? where emailID = ? ")) 
						{   			
							$insert_stmt->bind_param('ss', $email_status, $emailID ); 
							$insert_stmt->execute();
							$insert_stmt->close();
						}
						$flag=5;
					}
					else
					{
						$flag = $flag + 1;
					}
				}
				if($flag!=5)
				{
					echo "ERR001 Unable to send email at this time.<br/>Please call us to Help you.";
					exit;
				}
				else
				{
					echo "success";
					exit;
				}
			}
			else
			{
				echo "ERR002 Unable to send email at this time.<br/>Please call us to Help you.";
				exit;
			}
		}		
	}	   
	else
	{
		echo "ERR003 Unable to send email at this time.<br/>Please call us to Help you.";
		exit;
	}
}	
else 
{ 
	echo 'invalid';
	exit;
}
?>
