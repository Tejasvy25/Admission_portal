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
$appID = "";
if ($stmt = $mysqli->prepare("SELECT appID FROM ma_offline_users WHERE email = ?")) { 
      $stmt->bind_param('s', $email); 
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($appID); 
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
			$mail->Body     =  "<html>
<body>
<table style=\"border: 1px solid #2E9AFE;\" cellspanning='0' cellspacing='0' width='600px'>
<tr>
<td align='center' height='50px' bgcolor='#2E9AFE'><font color='#FFFFFF' size='+2'>MSIT ADMISSIONS 2014</font></td>
</tr>
<tr>
<td width='100%'>
	<table width='100%'>
	<tr><td>
	<font size='+1'><br/>Congratulations! Your offline application is successfully submitted for MSIT ADMISSIONS 2014. <br/><br/>Your Application Number is <font color='red'>".$appID."</font>. <br/><br/>
	<br/><br/>
	For any queries, please call us at 7799834583 / 85</font></td></tr>
	</table>
</td>
</tr>
</table>
</body>
</html>";  
			$mail->AddAddress($email); 
			$flag=1;
			while($flag==1)
			{
				if($mail->Send())
				{
					$mail->ClearAddresses();
					$email_status = "sent";
					if ($insert_stmt = $mysqli->prepare("update ma_offline_users set email_status=? where email = ? ")) {   			
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
	if ($insert_stmt = $mysqli->prepare("update ma_offline_users set email_status=? where email = ? ")) {   			
			$insert_stmt->bind_param('ss', $email_status, $email ); 
			$insert_stmt->execute();
			$insert_stmt->close();
	}
}
	?>
