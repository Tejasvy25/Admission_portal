<?php
include("class.phpmailer.php"); 
include dirname(dirname(__FILE__)).'/ma_config.php';
include dirname(dirname(__FILE__)).'/db_connect.php';
$myArray = array();

$testcenter = $_GET['testcenter'];
$emailstatus = $_GET['emailstatus'];
$onlyht = $_GET['onlyht'];
/******************************* Password Generation*******************************************/
$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
    $randomString = ''; 
  
    for ($i = 0; $i < 8; $i++) { 
        $index = rand(0, strlen($characters) - 1); 
        $randomString .= $characters[$index]; 
    } 
  

$pass=$randomString; 




/*************************************************************************************************/

$qry = "select id, email, gatAppNo, full_name,testDate,testTime,center from gat_halltickets where emailStatus = '".$emailstatus."'";
if( $testcenter!='All' )
{	
	$qry = $qry." and center = '".$testcenter."'";	
}
if( $onlyht!='NO' && $onlyht!='' &&  $onlyht!=null)
{	
	$qry = $qry." and gatAppNo = '".$onlyht."'";	
}
$qry = $qry." order by gatAppNo";

if ($result = $mysqli->query($qry)) 
{
	$appcount = 1;
	while($row = $result->fetch_object()) 
	{
		$gatAppId = $row->id;
		$email = $row->email;
		$appno = $row->gatAppNo;	
		$name=$row->full_name;
		$date_of_exam=$row->testDate;
		$time=$row->testTime;
		/* commented as we are not sending halltickets
		$file="halltickets/".$row->center."/".$appno.".pdf";*/

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
		$mail->FromName   = "MSIT Admissions ".YEARTEXT;
		$mail->WordWrap = 40;                               
		$mail->IsHTML(true);                          
		$mail->Subject  =  "MSIT ADMISSIONS ".YEARTEXT." GAT EXAM Invitation"; 
		
		$mail->Body     =  "<html><body><table style=\"border: 1px solid #2E9AFE;\" cellspanning='0' cellspacing='0' width='800px'><tr>
		<td align='center' height='50px' bgcolor='#2E9AFE'><font color='#FFFFFF' size='+2'>MSIT ADMISSIONS ".YEARTEXT."</font></td>
		</tr>
		<tr>
		<td width='100%'>
			<table width='100%'>
			<tr><td>
			<font size='+1'><br/> Dear Ms/Mr ".$name.",<br/><br/>
			      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Thanks for applying to MSIT ADMISSIONS 2020 .<br>
			      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Due to Covid-19 it has been   decided to conduct the Entrance examination (GAT) online which can be taken from your home using laptop or PC.
			      <br><br>


			    <u>Please find the online exam details below: </u>
			        
	<br>
	
	EXAM DATE: ".$date_of_exam."<br>
	EXAM TIME: ".$time."<br>
	<font color='red'>
	<br>
	<u>Your credentials for exam</u><br>
	Username :".$appno."<br>
	Password :".$pass."<br> 
	</font><br>
	Please find the URL below that has instructions and process that need to be followed to take the exam. <br/>
	URL: 'https://online.cbexams.com/RPS/MSIT/Default.aspx'
	<br><br>
	*Important Note1:- Please open the URL half-an-hour before  the exam start time, as it has instructions to read, which is very important to write the exam accurately.<br>
	<br>
	<font color='red'>*Important Note2:- Please take mock test immediately at this URL https://online.cbexams.com/RPS/MSIT/Practice_Instructions.aspx to ensure smooth functioning of the software and also to check your system compatibilty like camera, OS , internet etc... <br></font>

	<br>
For further queries email to enquiries@msitprogram.net or contact  7799834583, +91 7799834585    
	</table>



			
			</table>
		</td>
		</tr>
		</table>
		</body>
		</html>";  
/**********************************************************************************************/
$sql2 = "INSERT INTO  gat_ediquity_online (username,Password) VALUES ('$appno','$pass')";
		$query2 = $mysqli->prepare($sql2);
		$query2->execute();
/**********************************************************************************/


		$mail->AddAddress($email); 
		$mail->AddAttachment($file);		
		$outputtext =  $appcount." - ".$appno." - ".$email;
		if($mail->Send())
		{
			$outputtext = $outputtext." - EMAIL SENT";
			$mail->ClearAddresses();
			$email_status = 'YES'; 
			$email_time = date('Y-m-d H:s');
			if ($insert_stmt = $mysqli->prepare("update gat_halltickets set emailStatus=?, emailTime=? where id = ? ")) 
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
	}
}
$result->close();
$mysqli->close();
//echo json_encode($myArray);
echo "<br/><br/><a href='http://msitprogram.net/admissions/admin/sendHT.php'>Go Back</a>";

?>

