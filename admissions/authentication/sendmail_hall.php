<?php
include("class.phpmailer.php"); 
include dirname(dirname(__FILE__)).'/ma_config.php';
include dirname(dirname(__FILE__)).'/db_connect.php';
if(!isset($_POST['email']) || $_POST['email']=="")
{
	exit;
}

if(!isset($_POST['appno']) || $_POST['appno']=="")
{
	exit;
}
$email = $_POST['email'];
$appno = $_POST['appno'];
/*******************************Edited By Renuka *********************************************** /
Modification of Email starts here 
   
/***************************************************************************************/
 $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
    $randomString = ''; 
  
    for ($i = 0; $i < 10; $i++) { 
        $index = rand(0, strlen($characters) - 1); 
        $randomString .= $characters[$index]; 
    } 
  

$pass=$randomString; 
/*  This is to take the values*/


		$name="";
		$profileimage = "";
		$paymenttype = "";
		$testcenter = "";
		$slotno = "";
		$date_of_exam = "";
		$email = "";
		if ($stmt = $mysqli->prepare("SELECT u.full_name, u.image_url, u.email, w.paymentType, w.center, w.slotNo, w.slotdate FROM walkinApplicationsView w, userProfilesView u where w.email = u.email and w.walkinAppNo = ?")) { 
			$stmt->bind_param('s', $appno); 
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name,$profileimage,$email,$paymenttype,$testcenter,$slotno,$date_of_exam); 
			$stmt->fetch();
		}
/***********************************Modification ends here*********************************************/

if ($slotno=="1") {

	$no="10 AM";
}
else
{
	$no="2 PM";
}

/***********************************************************************************************
$name="";
		$profileimage = "";
		$paymenttype = "";
		$testcenter = "";
		$slotno = "";
		$date_of_exam = "";
		$email = "";
		if ($stmt = $mysqli->prepare("SELECT u.full_name, u.image_url, u.email, w.paymentType, w.center, w.slotNo, w.slotdate FROM walkinApplicationsView w, userProfilesView u where w.email = u.email and w.walkinAppNo = ?")) { 
			$stmt->bind_param('s', $appno); 
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name,$profileimage,$email,$paymenttype,$testcenter,$slotno,$date_of_exam); 
			$stmt->fetch();


**********************************************************************************/
$sql2 = "INSERT INTO  Ediquity_Online (App_No,Password) VALUES ('$appno','$pass')";
		$query2 = $mysqli->prepare($sql2);
		$query2->execute();
/**********************************************************************************/
$path="halltickets/";
$extension=$path.$appno;
$file=$extension.".pdf";

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
			$mail->Subject  =  "MSIT Admissions ".YEARTEXT." Walkin Exam Invitation"; 
			$mail->Body     =  "<html><body><table style=\"border: 1px solid #2E9AFE;\" cellspanning='0' cellspacing='0' width='600px'><tr>
<td align='center' height='50px' bgcolor='#2E9AFE'><font color='#FFFFFF' size='+2'>MSIT ADMISSIONS ".YEARTEXT."</font></td>
</tr>
<tr>
<td width='100%'>
	<table width='100%'>
	<tr><td>
	<font size='+1'><br/> Dear ".$name.",Congratulations! You have successfully booked your slot for walkin-exam-From-HOME - MSIT ADMISSIONS ".YEARTEXT.". 

	<br/><br/>Your Application Number is ".$appno." 
	<br>
	
	Exam date: ".$date_of_exam."<br>
	Slot Time: ".$no."<br>
	<font color='red'>
	<u>Your credentials for exam</u><br>
	Username :".$appno."<br>
	Password :".$pass."<br> 
	</font><br>
	Please find the URL that has instructions and process  that need to follow to write the exam.<br/><br/>
	URL: 'https://online.cbexams.com/RPS/MSIT/Default.aspx'
	For any queries, please call us at 7799834583 / 85</font></td></tr>
	</table>
</td>
</tr>
</table>
</body>
</html>";  

			$mail->AddAddress($email); 
			$mail->AddAttachment($file);
			

			$flag=1;
			while($flag==1)
			{

				if($mail->Send())
				{
					$mail->ClearAddresses();
					$flag=0;
				}
				else
				{
					echo 'Message could not be sent.';
    				echo 'Mailer Error: ' . $mail->ErrorInfo;

				}
				
			}

	?>

