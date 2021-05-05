<?php
include("class.phpmailer.php"); 
include dirname(dirname(__FILE__)).'/ma_config.php';
include dirname(dirname(__FILE__)).'/db_connect.php';
$myArray = array();

$html = <<<HTML
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>MSIT</title>
    
    <style>
    table {
	  border-collapse: collapse;
	  margin-left:10mm;
	  margin-right:10mm;
	}
	footer {
	  font-size: 9px;
	  color: #f00;
	  text-align: center;
	}


	@page {
	  size: portrait;
	  margin: 0mm 10mm 7mm 10mm;
	}

	td{
		padding:5px;
	}

	@media print {
		
	  #Header, #Footer { display: none !important; }
	  
	  table {
		  border-collapse: collapse;
		  margin-left:0mm;
		  margin-right:0mm;
		}

	  .content-block, p {
		page-break-inside: avoid;
	  }

	  html, body {
		width: 210mm;
	  }

	  td{
		padding:5px;
	  }

	  #header {
		  display: table-header-group;
		}

		#main {
		  display: table-row-group;
		}

		#footer {
		  display: table-footer-group;
		}
	}

    </style>
</head>

<body style="line-height:1.5;">
    <div style="width:210mm; margin: auto; margin-top:10mm;">
<table width="100%" border="1px;">
  <tr>
   <td colspan='2' align='center'>
	<b><img src="https://www.msitprogram.net/webcounselling/images/msit.jpg"/></b><br/>Consortium of Institutions of Higher Learning
	<b><br/> IIIT Campus, Gachibowli, Hyderabad - 500032 <br/>Phone: 040-66531342 Mobile: +91 7799834583,+91 7799834585, E-mail: enquiries@msitprogram.net</b>
  </td>
  </tr>
   <tr>
	<td colspan='2' align='center'>
	<table style="margin:0px;"  width="100%">
		<tr>
			<td style="text-align:center;">
				<span style="font-weight:bold; font-size:18px;">Call Letter for MSIT 2020 Online Counseling</span>
			</td>
		</tr>
		<tr>
			<td style="text-align:left;">
				Call Letter for MSIT 2020 Online Counseling is attached to this email.
				<br/>Please read the instructions carefully and join Online Counseling at the allotted time slot.<br/>
				During MSIT online counseling,
				<ol>
				<li>Join Online Counseling session through zoom meeting with the help of the link provided in the call letter
				</li>
				<li>
				During the Counseling session, you will need to login the web counseling portal (https://www.msitprogram.net/webcounselling/) with existing credentials and provide attendance for participating in the admission process
				</li>
				<li>
				You will need to confirm the learning center selected during online web counseling registrations
				</li>
				<li>
					After seat allotment download the admit letter
				</li>
				</ol>
				<p>
				Seats are allotted in rank order, if seats of any learning center are filled up; you will need to update the learning center choice. In case of powercuts/internet outages please expect a phone call from the MSIT counseling session, and kindly participate through audio /phone call mode. Failure to participate in MSIT online counseling is considered as absent. Absentees can only avail seats in the second phase of counseling for the remaining/available seats in the rank order. This email also has another attachment, the declaration form must be signed and uploaded in the web counseling portal two days before the MSIT online counseling.

				</p>			
			</td>
		</tr>
		<tr>
			<td style="text-align: center;"><b >Incase of any query, please call us at 7799834583 / 84 / 85.</b></td>
		</tr>
	</table>		
	</td>
  </tr>    
	</table>
	   
   </div>
</body>
</html>
HTML;

$qry = "select student_zoomlinks.id, student_zoomlinks.email, gat_call_letters.appno from student_zoomlinks join gat_call_letters on student_zoomlinks.email=gat_call_letters.email where email_sent='not yet'";
$qry = $qry." order by student_zoomlinks.rank";
if ($result = $mysqli->query($qry)) 
{
	$appcount = 1;
	while($row = $result->fetch_object()) 
	{
		$gatAppId = $row->id;
		$email = $row->email;
		$appno = $row->appno;	
		$file1="callletters/".$appno.".pdf";
		$file2="callletters/declaration_form.pdf";
		//$file1="callletters/choiceoflearningcentre.pdf";
		//$file2="callletters/CoursePassCertificate.pdf";
		//$file3="callletters/StudentDeclarationForm.pdf";

		$YEARTEXT="2020";
		$mail = new PHPMailer(); 
		$mail->IsSMTP(); 
		$mail->SMTPAuth   = true;                  
		$mail->SMTPSecure = "ssl"; 
		$mail->Host       = gethostbyname("smtp.gmail.com"); //"smtp.gmail.com";      
		$mail->Port       = 465;                  
		$mail->Username   = "tentudileepkumar@msitprogram.net";
		$mail->Password   = "TDKUMAR@350";           
		$mail->SMTPKeepAlive = true;
		$mail->Timeout =1000000000;
		$mail->From       = "msitadmissions@gmail.com"; 
		$mail->FromName   = "MSIT Admissions ".$YEARTEXT;
		$mail->WordWrap = 40;                               
		$mail->IsHTML(true);                          
		$mail->Subject  =  "MSIT ".$YEARTEXT." - Call Letter for Online Counseling"; 
		$mail->Body     =  $html;
		//$email = "satyawin007@gmail.com";
		$mail->AddAddress($email); 
		$mail->AddAttachment($file1);
		$mail->AddAttachment($file2);
		$outputtext =  $appcount." - ".$appno." - ".$email;
		if($mail->Send())
		{
			$outputtext = $outputtext." - EMAIL SENT";
			$mail->ClearAddresses();
			$email_status = 'yes'; 
			$email_time = date('Y-m-d H:s');
			if ($insert_stmt = $mysqli->prepare("update student_zoomlinks set email_sent=?, updated_at=? where id = ? ")) 
			{   			
				$insert_stmt->bind_param('sss', $email_status, $email_time, $gatAppId ); 
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
		sleep(1);
	}
}
$result->close();
$mysqli->close();
//echo json_encode($myArray);
echo "<br/><br/><a href='https://msitprogram.net/admissions/admin/sendCL.php'>Go Back</a>";
?>
