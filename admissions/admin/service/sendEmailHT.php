<?php
include dirname(dirname(__FILE__)).'/db_connect.php';
header('Access-Control-Allow-Origin: *');
$myArray = array();

$testcenter = $_GET['testcenter'];
$emailstatus = $_GET['emailstatus'];
$onlyht = $_GET['onlyht'];

$qry = "select email, full_name, mobile_no, gatAppNo, center from gat_halltickets where emailStatus = '".$emailstatus."'";
if( $testcenter!='All' )
{	
	$qry = $qry." and center = '".$testcenter."'";	
}
if( $onlyht!='NO' && $onlyht!='' &&  $onlyht!=null)
{	
	$qry = $qry." and gatAppNo = '".$onlyht."'";	
}
$qry = $qry." order by gatAppNo";

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
$mail->FromName   = "MSIT Admissions 2016";
$mail->WordWrap = 40;                               
$mail->IsHTML(true);                              
$mail->Subject  =  "MSIT Admissions 2016 Hallticket"; 
$mail->Body     =  "<html><body><table style=\"border: 1px solid #2E9AFE;\" cellspanning='0' cellspacing='0' width='600px'><tr>
<td align='center' height='50px' bgcolor='#2E9AFE'><font color='#FFFFFF' size='+2'>MSIT ADMISSIONS 2016</font></td>
</tr>
<tr>
<td width='100%'>
	<table width='100%'>
	<tr><td>
	<font size='+1'><br/>Get Ready for your MSIT 2016 GAT Entrance Test. <br/><br/>
	Please find the attached hallticket. Please carry a print out of this hallticket to the center.<br/><br/>
	For any queries, please call us at 7799834583 / 85</font></td></tr>
	</table>
</td>
</tr>
</table>
</body>
</html>";  
if ($result = $mysqli->query($qry)) 
{
	while($row = $result->fetch_object()) 
	{
		$email = 'seshhu143@gmail.com';
		$appno = $row->gatAppNo;
		$path="halltickets/";
		$extension=$path.$appno;
		$file=$extension.".pdf"

		$mail->AddAddress($email); 
		$mail->AddAttachment($file);
	
		$ch = curl_init('http://msitprogram.net/admissions/authentication/sendmail_hall.php');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "email=".$email."&appno=".$appno);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);
		curl_exec($ch);
		curl_close($ch);
	}
}
$result->close();
$mysqli->close();
echo json_encode($myArray);
?>
