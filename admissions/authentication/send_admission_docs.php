<?php
include("class.phpmailer.php"); 
include dirname(dirname(__FILE__)).'/ma_config.php';
include dirname(dirname(__FILE__)).'/db_connect.php';

$email = $_GET['email'];
$appno = "";
$learning_center = "";
$fullName = "";
$qry = "select gat_call_letters.appno, 	gat_call_letters.fullName, student_zoomlinks.learning_center from student_zoomlinks join gat_call_letters on gat_call_letters.email=student_zoomlinks.email where student_zoomlinks.email='$email'";
if ($result = $mysqli->query($qry)) 
{		
	if($row = $result->fetch_array()) 
	{
		$learning_center = $row["learning_center"];
		$appno = $row["appno"];
		$fullName = $row["fullName"];
	}
	$result->close();
	$mysqli->close();
}

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
				<div style="font-weight:bold; font-size:18px;">Allotment of Learning Center</div>
				<div style="font-weight:bold; font-size:14px;">Dear Mr./Ms. <b>$fullName</b>, You have been alloted a seat in <b>$learning_center</b> Learning Center, Download the attached documents</div>
			</td>
		</tr>	
	</table>		
	</td>
  </tr>   
  
	</table>
	   
   </div>
</body>
</html>
HTML;
	
	$YEARTEXT="2020";
	$email= $email; // 'satyawin007@gmail.com'; //
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
	$mail->Subject  =  "MSIT ADMISSIONS ".$YEARTEXT." Allotment of Learning Center"; 
	$mail->Body     =  $html;
	$mail->AddAddress($email);
	$file_path = "../authentication/admitletters/";
	$file1 = $file_path.$appno.".pdf";
	$file2 = $file_path.$appno."_loan_doc.pdf";
	$file3 = $file_path.$learning_center."/Laptop_Internet_Guidelines.pdf";
	$file4 = $file_path.$learning_center."/Annexure.pdf";
	$file5 = $file_path."/Bank Synopsis.pdf";
	$file6 = $file_path."/jntuh_bank_docs.zip";
	
	$mail->AddAttachment($file1);	
	if($learning_center=="IIITH" || $learning_center=="JNTUH"){
		$mail->AddAttachment($file2);
		$mail->AddAttachment($file3);
	}	
	if($learning_center=="IIITH"){
		$mail->AddAttachment($file4);
		$mail->AddAttachment($file5);
	}
	if($learning_center=="JNTUH"){
		$mail->AddAttachment($file6);
	}
	$outputtext = $email;
	if($mail->Send())
	{
		$outputtext = $outputtext." - EMAIL SENT";
		$mail->ClearAddresses();					
	}
	else{
	 $outputtext = $outputtext." - EMAIL COULD NOT BE SENT";
	}
	echo $outputtext;
?>
