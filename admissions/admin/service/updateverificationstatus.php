<?php
	include '../authentication/securelogin_functions.php';
	include("../../communication/class.phpmailer.php");
	sec_session_start();
	include '../db_connect.php';
	if (!login_check($mysqli)) #checking if session already exists
	{
		header("Location:index.php?message=Invalid Access. Please login first.");
		exit;
	}
	$email = $_POST['email'];
	$type = $_POST['type'];
	$status = $_POST['status'];
	$remarks = $_POST['remarks'];
	$updator_email = $_SESSION["email"];
	$updatedat = date("Y-m-d H:i:s");

	if($type=="details"){
		$qry = "select remarks from student where primaryEmail='".$email."'";
		if ($result = $mysqli->query($qry)) 
		{
			if($row= $result->fetch_assoc()) 
			{
				$remarks = $row["remarks"]."<br/>".$remarks;
				$qry = "update student set Verification='$status', remarks='$remarks', updatedBy='$updator_email', updated_at='$updatedat' where  primaryEmail='".$email."'";
				if ($mysqli->query($qry)) {
				  sendMail("Personal & Contact Details ", $email, $status,$_POST['remarks']);
			      echo "Status updated successfully";
			    } 
			    else {
			      //echo("Error description: " . $mysqli -> error);
			      echo "failed";
			    }
			}
		}
		else{
			echo "failed";
		}
	}
	else if($type=="certificates"){
		$qry = "select remarks from counselling_certificates where primaryEmail='".$email."'";
		if ($result = $mysqli->query($qry)) 
		{
			if($row= $result->fetch_assoc()) 
			{
				$remarks = $row["remarks"]."<br/>".$remarks;
				$qry = "update counselling_certificates set Verification='$status', remarks='$remarks', updatedBy='$updator_email', updated_at='$updatedat' where  primaryEmail='".$email."'";
				if ($mysqli->query($qry)) {
				  sendMail("Certificates ", $email, $status, $_POST['remarks']);
			      echo "Status updated successfully";
			    } 
			    else {
			      //echo("Error description: " . $mysqli -> error);
			      echo "failed";
			    }
			}
		}
		else{
			echo "failed";
		}
	}
	else if($type=="payment"){
		$qry = "select remarks from counselling_payment where primaryEmail='".$email."'";
		if ($result = $mysqli->query($qry)) 
		{
			if($row= $result->fetch_assoc()) 
			{
				$remarks = $row["remarks"]."<br/>".$remarks;
				$qry = "update counselling_payment set Verification='$status', remarks='$remarks', updatedBy='$updator_email', updated_at='$updatedat' where  primaryEmail='".$email."'";
				if ($mysqli->query($qry)) {
				  sendMail("Payment Details ", $email, $status, $_POST['remarks']);
			      echo "Status updated successfully";
			    } 
			    else {
			      //echo("Error description: " . $mysqli -> error);
			      echo "failed";
			    }
			}
		}
		else{
			echo "failed";
		}
	}
	$result->close();
	$mysqli->close();

	function sendMail($type, $email, $status, $remarks){
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
		height: 297mm;
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
    <div style="width:210mm; height: 297mm;  margin: auto; margin-top:10mm;">
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
				<span style="font-weight:bold; font-size:18px;">Couselling Registration Verification</span>
			</td>
		</tr>
		<tr>
			<td style="text-align:center;">				
			</td>
		</tr>
		
		<tr>
		<tr>
			<td colspan='2' align='center'>
			<table>
				<tr>
					<td style="border: 1px solid;">Verification of </td> <td style="font-weight:bold;border: 1px solid;">$type</td>
				</tr>
				<tr>
					<td style="border: 1px solid;">Status</td> <td style="border: 1px solid;">$status</td>
				</tr>
				<tr>
					<td style="border: 1px solid;">Remarks </td> <td style="border: 1px solid;">$remarks</td>
				</tr>
				<tr>
					<td  colspan='2'><br/></td> 
				</tr>				
			</table>
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
		  //$email= $email; //'satyawin007@gmail.com';
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
		  $mail->Subject  =  "MSIT ADMISSIONS ".$YEARTEXT." Couselling Registration Verification"; 
		  $mail->Body     =  $html;
		  $mail->AddAddress($email); 
		  if($mail->Send())
		  {
				//$outputtext = $outputtext." - EMAIL SENT";
				//$mail->ClearAddresses();
				echo "Email Sent and ";
					
		 }
		 else{
			 echo "Email Failed and ";
		 }
	}
?>
