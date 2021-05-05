<?php
include("class.phpmailer.php"); 
include dirname(dirname(__FILE__)).'/ma_config.php';
include dirname(dirname(__FILE__)).'/db_connect.php';
$myArray = array();

$emailstatus = $_GET['emailstatus'];
$onlyht = $_GET['onlyht'];

$qry = "select id, email, fullName, mobileno, appno, cdate, ctime from gat_call_letters where email_status = '".$emailstatus."'";
if( $onlyht!='NO' && $onlyht!='' &&  $onlyht!=null)
{	
	$qry = $qry." and appno = '".$onlyht."'";	
}

$qry = $qry." order by rank";

if ($result = $mysqli->query($qry)) 
{
	$appcount = 1;
	while($row = $result->fetch_object()) 
	{
		$gatAppId = $row->id;
		$email = $row->email;
		$appno = $row->appno;	
		$file4="callletters/".$appno.".pdf";
		$file1="callletters/choiceoflearningcentre.pdf";
		//$file2="callletters/CoursePassCertificate.pdf";
		$file3="callletters/StudentDeclarationForm.pdf";

		$mail = new PHPMailer(); 
		$mail->IsSMTP(); 
		$mail->SMTPAuth   = true;                  
		$mail->SMTPSecure = "ssl"; 
		$mail->Host       = gethostbyname("smtp.gmail.com"); //"smtp.gmail.com";      
		$mail->Port       = 465;                  
		$mail->Username   = "tentudileepkumar@msitprogram.net";
		$mail->Password   = "TDKUMAR@350";           
		$mail->SMTPKeepAlive = true;
		$mail->Timeout =100000000;
		$mail->From       = "msitadmissions@gmail.com"; 
		$mail->FromName   = "MSIT Admissions ".YEARTEXT;
		$mail->WordWrap = 40;                               
		$mail->IsHTML(true);                              
		$mail->Subject  =  "MSIT Admissions ".YEARTEXT." Call Letter"; 
		$mail->Body     =  "<html><body><table style=\"border: 1px solid #2E9AFE;\" cellspanning='0' cellspacing='0' width='600px'><tr>
		<td align='center' height='50px' bgcolor='#2E9AFE'><font color='#FFFFFF' size='+2'>MSIT ADMISSIONS ".YEARTEXT."</font></td>
		</tr>
		<tr>
		<td width='100%'>
			<table width='100%'>
			<tr><td>
			<font size='+1'><br/>Get Ready for your MSIT ".YEARTEXT." Admissions. <br/><br/>
			Please find the attached call letter documents. <br/><br/>Please read the instructions and carry a print out of these letters to the center.<br/><br/>
			For any queries, please call us at 7799834583 / 85</font></td></tr>
			</table>
		</td>
		</tr>
		</table>
		</body>
		</html>"; 
		//$email = "seshhu143@gmail.com";
		$mail->AddAddress($email); 
		$mail->AddAttachment($file4);		
		$mail->AddAttachment($file1);		
		//$mail->AddAttachment($file2);		
		$mail->AddAttachment($file3);		
		$outputtext =  $appcount." - ".$appno." - ".$email;
		if($mail->Send())
		{
			$outputtext = $outputtext." - EMAIL SENT";
			$mail->ClearAddresses();
			$email_status = 'YES'; 
			$email_time = date('Y-m-d H:s');
			if ($insert_stmt = $mysqli->prepare("update gat_call_letters set email_status=? where id = ? ")) 
			{   			
				$insert_stmt->bind_param('ss', $email_status, $gatAppId ); 
				if($insert_stmt->execute())
					$outputtext = $outputtext." - UPDATE SUCCESS";
				else
					$outputtext = $outputtext." - UPDATE FAILED";
				$insert_stmt->close();
			}
			else
			{
				$outputtext = $outputtext." $$ UPDATE FAILED";
			}
		}
		else
		{
			$outputtext = $outputtext." - EMAIL NOT SENT";
		}
		$appcount = $appcount + 1;
		echo $outputtext."<br/>";
	}
}
$result->close();
$mysqli->close();
//echo json_encode($myArray);
echo "<br/><br/><a href='https://msitprogram.net/admissions/admin/sendCL.php'>Go Back</a>";
?>
