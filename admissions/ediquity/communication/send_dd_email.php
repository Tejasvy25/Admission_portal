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
			$mail->Subject  =  "MSIT Admissions 2014 DD Verification"; 
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
									<font size='+1'><br/>Congratulations! <br/> We have received your DD.<br/> Your DD has been successfully Verified.</font>. <br/><br/>
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
					$flag=0;
				}
			}
			echo "<script>alert('DD verified and SMS, E-Mail Sent successfully');window.location.assign('/admissions2014/admin/verify_dd.php');</script>";

	?>
