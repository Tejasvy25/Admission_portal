<?php



include("class.phpmailer.php"); 

include dirname(dirname(__FILE__)).'/ma_config.php';

include dirname(dirname(__FILE__)).'/db_connect.php';





if(!isset($_GET['email']) || $_GET['email']=="")

{

	echo "no email";

	exit;

}

$email = $_GET['email'];

$status = "";

if ($stmt = $mysqli->prepare("SELECT status FROM forgot_password WHERE emailID = ?")) { 

      $stmt->bind_param('s', $email); 

      $stmt->execute();

      $stmt->store_result();

      $stmt->bind_result($status); 

	  

      $stmt->fetch();

	  ?>

	  

	  <?php

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

			$mail->FromName   = "MSIT Admissions 2015";

			

			$mail->WordWrap = 40;                               

			$mail->IsHTML(true);                              

			$mail->Subject  =  "MSIT Admissions 2015 Account Activation"; 

			$mail->Body     =  "Please click on link to reset your Account. Click here ".SERVER."authentication/reset_process.php?userName=".$email."&key=".$status;  



			$mail->AddAddress('srinath@ibongo.com'); 

			$flag=1;

			while($flag==1)

			{

				if($mail->Send())

				{

					$mail->ClearAddresses();

					$email_status = "sent";

					if ($insert_stmt = $mysqli->prepare("update ".prefix."forgot_password set email_status=? where emailID = ? ")) {   			

							$insert_stmt->bind_param('ss', $email_status, $email ); 

							$insert_stmt->execute();

							$insert_stmt->close();

					}

					$flag=0;

				}

			}

			

}

else

{	

	$email_status = "failed";

	if ($insert_stmt = $mysqli->prepare("update ".prefix."forgot_password set email_status=? where emailID = ? ")) {   			

			$insert_stmt->bind_param('ss', $email_status, $email ); 

			$insert_stmt->execute();

			$insert_stmt->close();

	}

}

	?>

