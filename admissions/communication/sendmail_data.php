



<?php


include("class.phpmailer.php"); 



$usersfile=$_REQUEST['users'];
$gatfile=$_REQUEST['gat'];
$walkinfile=$_REQUEST['walk'];

echo $usersfile;
//die();



$path="http://msitprogram.net/admissions2015/backup/";

$usersfile =$path.$usersfile;
$gatfile =$path.$gatfile;
$walkinfile =$path.$walkinfile;

echo  $usersfile ;





 

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

			$mail->Subject  =  "MSIT Admissions 2015 Back up Data"; 

			$mail->Body     =  "Please find the attachements";  



			$mail->AddAddress('srinat@ibongo.com'); 

			

			$mail->AddAttachment($usersfile);
			//$mail->AddAttachment($gatfile);
			//$mail->AddAttachment($walkinfile);

				$mail->Send()

			

				

	?>

