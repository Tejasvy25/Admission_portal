



<?php



include("class.phpmailer.php"); 

include dirname(dirname(__FILE__)).'/ma_config.php';

include dirname(dirname(__FILE__)).'/db_connect.php';

session_start();

$id=$_POST['id'];

$appid=$id;

$path="halltickets/";

$extension=$path.$appid;

$file=$extension.".pdf";

 if ($stmt = $mysqli->prepare("SELECT emailID,walkinAppNo FROM ma_walkin WHERE walkinAppNo = ?")) { 

									$stmt->bind_param('s', $id); 

									$stmt->execute();

									$stmt->store_result();

									$stmt->bind_result($email,$appno); 

									$stmt->fetch();

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

			$mail->Timeout =100000000;

			$mail->From       = "msitadmissions@gmail.com"; 

			$mail->FromName   = "MSIT Admissions 2015";

			

			$mail->WordWrap = 40;                               

			$mail->IsHTML(true);                              

			$mail->Subject  =  "MSIT Admissions 2015 Walkin Hallticket"; 

			$mail->Body     =  "Please find the attachment and download your Hall Ticket";  



			$mail->AddAddress($email); 

			

			$mail->AddAttachment($file);

				$mail->Send()

			

				

	?>

