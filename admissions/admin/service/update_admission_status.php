<?php
	ini_set( "display_errors", 0); 
	include '../authentication/securelogin_functions.php';
	include("../../communication/class.phpmailer.php");
	require('../../authentication/fpdf/fpdf.php');
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

	if($type=="declation_form"){
		$qry = "update student_zoomlinks set verification_status='$status', verifier_remarks='$remarks', verified_by='$updator_email', updated_at='$updatedat' where  email='".$email."'";
		if ($mysqli->query($qry)) {
		  //sendMail("Declaration Form ", $email, $status,$_POST['remarks']);
	      echo "Status updated successfully";
	    } 
	    else {
	      //echo("Error description: " . $mysqli -> error);
	      echo "failed";
	    }
	}
	else if($type=="admission"){
		$learning_center = $_POST['learning_center'];
		$serialno = $_POST['serial_no'];
		$qry = "update student_zoomlinks set admission_code='$serialno', learning_center='$learning_center', admission_status='$status', admission_remarks='$remarks', admission_updated_by='$updator_email', updated_at='$updatedat' where  email='".$email."'";
		//echo $qry; die();
		if ($mysqli->query($qry)) {
		  //sendMail("Certificates ", $email, $status, $_POST['remarks']);
		  if($status=="Alloted"){
		  	generateAdmitLetterPDF($mysqli, $email);
		  }
	      echo "Status updated successfully";
	    } 
	    else {
	      //echo("Error description: " . $mysqli -> error);
	      echo "failed";
	    }
	}
	$mysqli->close();


	function generateAdmitLetterPDF($mysqli, $email){
		$qry = "select firstName, lastName, fatherName, admission_code, learning_center, appno from student_zoomlinks join student on student.primaryEmail=student_zoomlinks.email join gat_call_letters on gat_call_letters.email=student_zoomlinks.email where student_zoomlinks.email='$email'";
		$result = $mysqli->query($qry);
		$total = 0;
		while($temp = $result->fetch_object())
		{
			$firstName=$temp->firstName;
			$lastName=$temp->lastName;
			$father_name=$temp->fatherName;
			$admission_code =$temp->admission_code;
			$learning_center =$temp->learning_center;
			$appno = $temp->appno;
			$fullName = $firstName." ".$lastName;
			$name = ucwords($fullName);
			$pdf = new FPDF('P','mm','A4');
			$pdffilename='../../authentication/admitletters/'.$appno.'.pdf';
			$pdf->SetAuthor("MSIT");
			$pdf->SetTitle("MSIT");	
			$pdf->SetCreator("MIST");
			$pdf->SetSubject("MSIT 2020 – Counseling and Allotment of MSIT Learning Center");
			$pdf->AddPage();	
			$pdf->SetFont('times','B',20);
			$pdf->SetXY(18,15);
			$pdf->Cell('','5','Consortium of Institutions of Higher Learning  ','','','C');		
			$pdf->Cell('','3','');
			$pdf->SetXY(15,25);
			$pdf->SetFont('times','B',10);
			$pdf->Cell('','','IIIT Campus, Gachibowli, Hyderabad - 32, Phone: 040-23001970 Mobile: 7799834583 / 84 / 85','','','C');
			$pdf->Cell('','3','');
			$pdf->Image('../../authentication/images/line.jpg','25','30','162');	
			$pdf->Cell('','3','');
			$pdf->Image('../../authentication/images/msit.jpg','25','31','35');

			$pdf->SetFont('times','B',16);
			$pdf->Cell('','3','');
			$pdf->SetXY(30,39);
			$pdf->Cell('','','Letter of Admission into ','','','C');

			$pdf->SetFont('times','B',14);			
			$pdf->SetXY(30,46);
			$pdf->Cell('','','Master of Science in Information Technology (MSIT)','','','C');

			$pdf->SetFont('times','B',12);
			$pdf->Cell('','3','');
			$pdf->SetXY(25,58);
			$rollno = "";
			if($admission_code<10){
				$rollno = "00".$admission_code;
			}
			else if($admission_code<100){
				$rollno = "0".$admission_code;
			}
			else {
				$rollno = $admission_code;
			}
			$lc_code = "";	
			$admission_no = "";		
			if($learning_center=="IIITH"){
				$lc_code = "2020501";
				$admission_no = 'Admission No: CIHL/MSIT 2020/'.$learning_center.'/'.$lc_code.$rollno;
			}
			else if($learning_center=="JNTUH"){
				$lc_code = "20031J0";
				$admission_no = 'Admission No: CIHL/MSIT 2020/'.$learning_center.'/'.$lc_code.$rollno;
			}
			else if($learning_center=="JNTUK"){
				$lc_code = "20021J0";
				$admission_no = 'Admission No: CIHL/MSIT 2020/'.$learning_center.'/'.$lc_code.$rollno;
			}
			else if($learning_center=="JNTUA"){
				$lc_code = "200A1J0";
				$admission_no = 'Admission No: CIHL/MSIT 2020/'.$learning_center.'/'.$lc_code.$rollno;
			}
			else if($learning_center=="SVU"){
				$lc_code = "122";
				$admission_no = 'Admission No: CIHL/MSIT 2020/'.$learning_center.'/'.$lc_code.$rollno;
			}
			
			$pdf->Cell('','',$admission_no,'','','');

			$pdf->SetFont('times','B',12);
			$pdf->Cell('','3','');
			$pdf->SetXY(140,58);
			$dt = date("d-m-Y");
			$pdf->Cell('','','Date : '.$dt.'','','','C');

			$pdf->SetFont('times','',12);
			$pdf->Cell('','3','');
			$pdf->SetXY(25,65);
			
			$txt = "Mr/Ms. ".$name." S/o/D/o. ".$father_name." is admitted into the MSIT program at ".$learning_center." Learning Center.";
		    // Times 12
		    $pdf->SetFont('Times','',12);
		    $pdf->MultiCell(0,6,$txt);
		    $pdf->Ln();

			$pdf->SetFont('times','',12);
			$pdf->Cell('','3','');
			$pdf->SetXY(25,80);
			if($learning_center=="IIITH"){
				$txt = "\t\t\t\t1. The duration of the program is 2 Years.\n\t\t\t\t2. Due to COVID-19 special circumstances, this year students are regularized only after \t\t\t\t\t\t\t\t\tcompletion of the eligibility degree (BE/BTECH).\n\t\t\t\t3.The Fee payable for the MSIT program is Rs. 2,00,000/- (Rupees Two Lakhs only) per annum \t\t\t\t\t\t\t\tand the same is non-refundable. Last date for fee payment is 1st September 2020. Students \t\t\t\t\t\t\tcan pay the remaining Rs. 1,70,000/- (excluding admission fee Rs 30,000/-) through wire \t\t\t\t\t\t\ttransfer or pay online through the admissions portal. For wire transfer use Account name: CIHL, \t\t\t\t\t\t\taccount number: 52081085235, IFSC code: SBIN0021161, and branch name: SBI, IIIT Campus, \t\t\t\t\t\t\tGachibowli.\n\t\t\t\t4.In addition to the above, student is also liable for payment of any deposits / fee charged by \t\t\t\t\t\t\tLearning Centre concerned viz., Library, Caution deposit, Hostel, Mess etc.";	
			}
			else if($learning_center=="JNTUH"){
				$txt = "\t\t\t\t1. The duration of the program is 2 Years.\n\t\t\t\t2. Due to COVID-19 special circumstances, this year students are regularized only after \t\t\t\t\t\t\t\t\tcompletion of the eligibility degree (BE/BTECH).\n\t\t\t\t3.The Fee payable for the MSIT program is Rs. 1,70,000/- (Rupees One Lakh Seventy Thousand \t\t\t\t\t\t\t\tonly) per annum and the same is non-refundable. Last date for fee payment is 14th September  \t\t\t\t\t\t\t2020. Students can pay the remaining Rs.1,40,000/- (excluding admission fee Rs. 30,000/-)  \t\t\t\t\t\t\tthrough wire transfer or pay online through the admissions portal. For wire transfer use \t\t\t\t\t\t\tAccount name: CIHL, account number: 52081085235, IFSC code: SBIN0021161, and branch  \t\t\t\t\t\t\tname: SBI, IIIT Campus, Gachibowli.\n\t\t\t\t4.In addition to the above, student is also liable for payment of any deposits / fee charged by \t\t\t\t\t\t\tLearning Centre concerned viz., Library, Caution deposit, Hostel, Mess etc.";
			}
			else {
				$txt = "\t\t\t\t1. The duration of the program is 2 Years.\n\t\t\t\t2. Due to COVID-19 special circumstances, this year students are regularized only after \t\t\t\t\t\t\t\t\tcompletion of the eligibility degree (BE/BTECH).\n\t\t\t\t3.The Fee payable for the MSIT program is Rs. 1,30,000/- (Rupees One Lakh Thirty Thousand \t\t\t\t\t\t\t\tonly) per annum and the same is non-refundable. Last date for fee payment is 14th September  \t\t\t\t\t\t\t2020. Students can pay the remaining Rs.1,00,000/- (excluding admission fee Rs. 30,000/-)  \t\t\t\t\t\t\tthrough wire transfer or pay online through the admissions portal. For wire transfer use \t\t\t\t\t\t\tAccount name: CIHL, account number: 52081085235, IFSC code: SBIN0021161, and branch  \t\t\t\t\t\t\tname: SBI, IIIT Campus, Gachibowli.\n\t\t\t\t4.In addition to the above, student is also liable for payment of any deposits / fee charged by \t\t\t\t\t\t\tLearning Centre concerned viz., Library, Caution deposit, Hostel, Mess etc.";
			}
			$pdf->SetFont('Times','',12);
		    $pdf->MultiCell(0,6,$txt);
		    $pdf->Ln();
			
			$pdf->SetXY(25,150);
		    // Times 12
		    $txt = "Institute will conduct an online Induction program between 4th September 2020 and 5th September 2020 to which all admitted students must attend and submit all required documents and show fee payment proofs. Failure to do so will result in forfeiting claims to admission and fee paid during counseling. The commencement of class work is 7th September 2020. \n\nUntil campuses are reopened, all MSIT classes will be conducted online. Students must procure a competent laptop as per specifications, along with a broadband connection before 4th September 2020. Please download guidelines for the purchasing the laptop and internet, loan documents, MSIT estimates, etc. in addition to this document from the admissions portal.";
		    $pdf->SetFont('Times','',12);
		    $pdf->MultiCell(0,6,$txt);
		    $pdf->Ln();

		    $pdf->SetXY(25,210);
		    if($learning_center=="IIITH"){
				$txt = "Report to\nMr.Manjunath Mattam, \nHead MSIT,\nMSIT Division, IIIT Campus, Gachibowli, Hyderabad-500032.\nPh.040 - 66531342, Mob.7799834582.\n\nOn behalf of MSIT 2020 Admission Committee";
			}
			else if($learning_center=="JNTUH"){
		    	$txt = "Report to\nMr.P.Mohankrishna, \nHead MSIT,\nSchool of Information Technology, JNTU, Kukatpally, Hyderabad-500085.\nMob.7799834581.\n\nOn behalf of MSIT 2020 Admission Committee";
			}
			else if($learning_center=="JNTUK"){
		    	$txt = "Report to\nDr.K Sahadevaiah, Professor of CSE,\nCo-coordinator, MSIT Program,\nPrincipal Building Block, JNTUK University, KAKINADA-533003,\nMobile:+91- 7799834586.\n\nOn behalf of MSIT 2020 Admission Committee";
			}
			else if($learning_center=="JNTUA"){
		    	$txt = "Report to\nDr. C. Shoba Bindu,\nProfessor of CSE & MSIT Coordinator,\nDepartment of Computer Science and Engineering, JNT University, Anantapur-515002,\nPhone No +91-8143289089\n\nOn behalf of MSIT 2020 Admission Committee";
			}
			else if($learning_center=="SVU"){
		    	$txt = "Report to\nDr.G.V. Marutheswar, Professor of EEE,\nCo-ordinator, MSIT Program,\nS .V. University College of Engineering, TIRUPATI-517502\nMobile: +91-9290728020.\n\nOn behalf of MSIT 2020 Admission Committee";
			}
		    $pdf->SetFont('Times','',12);
		    $pdf->MultiCell(0,6,$txt);
		    $pdf->Ln();


		    $pdf->SetFont('times','',12);
			$pdf->Image('../../authentication/images/sign.jpg','25','253','40');

			$pdf->Cell('','3','');
			$pdf->SetXY(25,265);
			$pdf->Cell('','','Dean,','','','');
			$pdf->Cell('','3','');
			$pdf->SetXY(25,270);
			$pdf->Cell('','','CIHL, MSIT Division,','','','');
			$pdf->Cell('','3','');
			$pdf->SetXY(25,275);
			$pdf->Cell('','','IIIT Hyderabad.','','','');

			$pdf->Output($pdffilename,'F');
			$pdf->Close();
			if($learning_center=="IIITH" || $learning_center=="JNTUH"){
				generateLoanDocPDF($mysqli, $email);
			}
			echo "Admit Letter PDF Generated Successfuly, ";
		}
	}

	function generateLoanDocPDF($mysqli, $email){
		$qry = "select firstName, lastName, fatherName, admission_code, learning_center, appno from student_zoomlinks join student on student.primaryEmail=student_zoomlinks.email join gat_call_letters on gat_call_letters.email=student_zoomlinks.email where student_zoomlinks.email='$email'";
		$result = $mysqli->query($qry);
		$total = 0;
		while($temp = $result->fetch_object())
		{
			$firstName=$temp->firstName;
			$lastName=$temp->lastName;
			$father_name=$temp->fatherName;
			$admission_code =$temp->admission_code;
			$learning_center =$temp->learning_center;
			$appno = $temp->appno;
			$fullName = $firstName." ".$lastName;

			$rollno = "";
			if($admission_code<10){
				$rollno = "00".$admission_code;
			}
			else if($admission_code<100){
				$rollno = "0".$admission_code;
			}
			else {
				$rollno = $admission_code;
			}

			$name = ucwords($fullName);
			$pdf = new FPDF('P','mm','A4');
			$pdffilename='../../authentication/admitletters/'.$appno.'_loan_doc.pdf';

			if($learning_center=="IIITH"){
				$rollno = "2020501".$rollno;
				$pdf->SetAuthor("MSIT");
				$pdf->SetTitle("MSIT");	
				$pdf->SetCreator("MIST");
				$pdf->SetSubject("MSIT 2020 – Counseling and Allotment of MSIT Learning Center");
				$pdf->AddPage();	
				
				$pdf->Cell('','3','');
				$pdf->Image('../../authentication/images/IIIT.jpg','130','10','35');	
				$pdf->Cell('','3','');
				$pdf->Image('../../authentication/images/line.jpg','80','32','180');

				$pdf->SetFont('times','',12);
				$pdf->Cell('','3','');
				$pdf->SetXY(90,38,39);
				$pdf->Cell('','','International Institute of Information Technology, Hyderabad','','','C');
				$pdf->SetXY(100,43,39);
				$pdf->Cell('','','A Research University','','','C');

				$pdf->SetFont('times','',11);			
				$pdf->SetXY(20,45);
				$pdf->Cell('','','Manjunath Kumar Mattam,','','');

				$pdf->SetXY(20,50);
				$pdf->Cell('','','Head MSIT, IIIT-H.,','','');
				$pdf->SetXY(20,55);
				$pdf->Cell('','','Phone: +91 40 6653 1268.','','');
				$pdf->SetXY(20,65);
				$pdf->Cell('','','Reference:','','');
				$pdf->SetFont('times','B',11);

				$pdf->SetXY(20,65);
				$ref = "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tIIIT-H/MSIT/2020-2021/First year fee/".$rollno;
				$pdf->Cell('','',$ref,'','');

				$pdf->SetFont('times','B',12);
				$pdf->Cell('','3','');
				$pdf->SetXY(150,55);
				$pdf->Cell('','','Hyderabad,','','','');

				$pdf->SetFont('times','B',12);
				$pdf->Cell('','3','');
				$pdf->SetXY(150,60);
				$dt = date("d-m-Y");
				$pdf->Cell('','','Date : '.$dt.'','','','');

				$pdf->SetFont('times','',12);
				$pdf->Cell('','3','');

				$pdf->SetXY(20,80);
				$pdf->Cell('','','To Whom It May Concern:','','');
				$pdf->SetXY(20,90);
				$pdf->Cell('',''," Subject: First year tuition fee & other charges due for Mr. /Ms. ".$name.",",'','');

			    $pdf->Ln();

				$pdf->SetFont('times','',12);
				$pdf->Cell('','3','');
				$pdf->SetXY(20,95);
				
			    $txt = "\t\t\t\tMr./Ms ".$name." is admitted into International Institute of Information Technology, Hyderabad (IIIT-H) to pursue Master of Science in Information Technology (MSIT) with roll number ".$rollno.". Mr./Ms. ".$name." has completed all entrance procedures and is admitted into MSIT first year.";

				$pdf->SetFont('Times','',12);
			    $pdf->MultiCell(0,6,$txt);
			    //$pdf->Ln();
			    $pdf->SetFont('times','B',12);
				$pdf->Cell('','3','');
			    $pdf->SetXY(20,125);
				$pdf->Cell('','',' The fee and other charges for MSIT first year (Academic year 2020-2021) shall be as below:','','');
				
				$pdf->SetXY(25,130);
				$txt = "The course fee: Rs 200000/- (Two lakhs). After adjustment of Rs 30,000/- (Rupees thirty thousand) paid at the time of admission, the balance amount of Rs. 1,70,000/- (Rupees one lakh seventy thousand only) must be paid through demand draft payable at Hyderabad in favor of CIHL Hyderabad (Consortium of Institutions of Higher Learning). Or maybe remitted into CIHL bank account (Account number: 52081085235, Bank: SBI, Branch: IIIT Campus Gachibowli, IFSC Code: SBIN0021161)";
			    // Times 12
			    $pdf->SetFont('Times','',12);
			    $pdf->MultiCell(0,6,$txt);
			    $pdf->Ln();

			    $pdf->SetXY(25,170);
				$txt = "Laptop: Students are instructed to procure a laptop of their own directly from the vendor. The institute has decided the suitable configuration of the laptop. Approximate market rate of the same would be around Rs.50,000 /- (Rupees fifty thousand only).";
			    // Times 12
			    $pdf->SetFont('Times','',12);
			    $pdf->MultiCell(0,6,$txt);
			    $pdf->Ln();

			    $pdf->SetXY(25,190);
				$txt = "Caution deposit: One-time refundable caution deposit amounting to Rs. 10,000 /- (Rupees ten thousand only) must be paid through demand draft payable at Hyderabad drawn in favor of CIHL Hyderabad (Consortium of Institutions of Higher Learning)";
			    // Times 12
			    $pdf->SetFont('Times','',12);
			    $pdf->MultiCell(0,6,$txt);
			    $pdf->Ln();

			    $pdf->SetXY(25,210);
				$txt = "Student medical insurance: Rs. 1000 /- (Rupees One Thousand only) must be paid through demand draft payable at Hyderabad in favor of CIHL Hyderabad.";
			    // Times 12
			    $pdf->SetFont('Times','',12);
			    $pdf->MultiCell(0,6,$txt);
			    $pdf->Ln();

			    $pdf->SetXY(25,225);
				$txt = "Above mentioned course fee and other charges must be paid on or before 1st September 2020. In case of delay a fine of Rs 5000/- will be levied till 6th September 2020. Beyond this period, admission will be cancelled, and the student has to drop from the MSIT program.";
			    // Times 12
			    $pdf->SetFont('Times','',12);
			    $pdf->MultiCell(0,6,$txt);
			    $pdf->Ln();

			    $pdf->SetFont('times','',12);
				$pdf->Image('../../authentication/images/manjunath.jpg','25','248','40');

				$pdf->Cell('','3','');
				$pdf->SetXY(25,270);
				$pdf->Cell('','','(Manjunath Kumar Mattam),','','','');
				$pdf->Cell('','3','');
				$pdf->SetXY(25,275);
				$pdf->Cell('','','Head MSIT, IIIT-H','','','');

				$pdf->Output($pdffilename,'F');
				$pdf->Close();

			}
			else if($learning_center=="JNTUH"){
				$rollno = "20031J0".$rollno;
				$pdf->SetAuthor("MSIT");
				$pdf->SetTitle("MSIT");	
				$pdf->SetCreator("MIST");
				$pdf->SetSubject("MSIT 2020 –JNTUH Loan Document Letter");
				$pdf->AddPage();	
				//$pdf->SetFont('times','B',20);
				//$pdf->SetXY(18,15);
				//$pdf->Cell('','5','Consortium of Institutions of Higher Learning  ','','','C');	25','30','55	
				$pdf->Cell('','3','');
				$pdf->Image('../../authentication/images/Jntuhletterhead.JPG','20','10','180');

				$pdf->SetFont('times','B',12);
				$pdf->Cell('','3','');
				$pdf->SetXY(20,55,39);
				$pdf->Cell('','','Dr. A. Damodaram,','','','');
				$pdf->SetXY(20,60,39);
				$pdf->Cell('','','                                  M.Tech, Ph.D.,','','','');
			    $pdf->SetXY(20,65,39);
				$pdf->Cell('','','DIRECTOR  &  Professor of CSE.,','','');

				$pdf->SetFont('times','B',12);
				$pdf->Cell('','3','');
				$pdf->SetXY(160,70);
				$dt = date("d-m-Y");
				$pdf->Cell('','','Date : '.$dt.'','','','C');

				$pdf->SetFont('times','BU',12);
				$pdf->Cell('','3','');

				$pdf->SetXY(65,80);
				$pdf->Cell('','','TO WHOM SO EVER IT MAY CONCERN','','C');

				$pdf->SetFont('times','',12);
				$pdf->Cell('','3','');
				$pdf->SetXY(25,85);
			     $txt = "\t\t\t\tThis is to certify that ".$name."	Bearing  Roll No. ".$rollno."		got admission into the two years of MSIT Program (Master of Science in Information Technology) at School of Information Technology, JNT University  Hyderabad  for the academic years  2020-21 and 2021-22.";

				$pdf->SetFont('Times','',12);
			    $pdf->MultiCell(0,6,$txt);
			    $pdf->Ln();
			    $pdf->SetFont('times','',12);
				$pdf->Cell('','3','');
			    $pdf->SetXY(20,110);
				$pdf->Cell('','',' The Fee Structure for the two year MSIT Program is as given below.','','');

			    $pdf->SetXY(25,115);
			    $width_cell=array(15,20,30,40,25,25);
			    $pdf->SetFont('times','',12);
			    $pdf->SetFillColor(20,229,252);
			    $pdf->Cell($width_cell[0],10,'S.No',1,0,C,false);
			    $pdf->Cell($width_cell[1],10,'Year',1,0,C,false);
			    $pdf->Cell($width_cell[2],10,'Tuition Fee',1,0,C,false);
			    $pdf->Cell($width_cell[3],10,'Development Fee',1,0,C,false);
			    $pdf->Cell($width_cell[4],10,'Laptop Fee',1,0,C,false);
			    $pdf->Cell($width_cell[5],10,'Total',1,1,C,false);
			    $pdf->SetXY(25,125);

			    $pdf->Cell($width_cell[0],10,'1',1,0,C,false);
			    $pdf->Cell($width_cell[1],10,'I Year',1,0,C,false);
			    $pdf->Cell($width_cell[2],10,'1,70,000/-',1,0,C,false);
			    $pdf->Cell($width_cell[3],10,'6,200/-',1,0,C,false);
			    $pdf->Cell($width_cell[4],10,'50,000/-',1,0,C,false);
			    $pdf->Cell($width_cell[5],10,'2,26,200/-',1,1,C,false);

			    $pdf->SetXY(25,135);

			    $pdf->Cell($width_cell[0],10,'2',1,0,C,false);
			    $pdf->Cell($width_cell[1],10,'II Year',1,0,C,false);
			    $pdf->Cell($width_cell[2],10,'1,70,000/-',1,0,C,false);
			    $pdf->Cell($width_cell[3],10,'3000/-',1,0,C,false);
			    $pdf->Cell($width_cell[4],10,'',1,0,C,false);
			    $pdf->Cell($width_cell[5],10,'1,73,000/-',1,1,C,false);

			    $width_cell=array(130,25);
			    $pdf->SetXY(25,145);
			    $pdf->Cell($width_cell[0],10,'Total',1,0,C,false);
			    $pdf->Cell($width_cell[1],10,'3,99,200/-',1,1,C,false);

			    $pdf->SetXY(25,165);
				$txt = "\t\t\t\t\tThe student has to pay the first Year fee into CIHL Bank Account( Account Number: 52081085235, Bank: SBI, Branch : IIIT Campus Gachibowli, IFSC Code : SBIN0021161 )  on or before 31-08-2020.";
			    // Times 12
			    $pdf->SetFont('Times','',12);
			    $pdf->MultiCell(0,6,$txt);
			    $pdf->Ln();

			    $pdf->SetXY(25,190);
				$txt = "The laptop amount would be disbursed to the student directly by the bank to facilitate the student for procuring laptop .";
				 
			    // Times 12
			    $pdf->SetFont('Times','B',12);
			    $pdf->MultiCell(0,6,$txt);
			    $pdf->Ln();

			    $pdf->SetFont('times','',12);
				//$pdf->Image('../../authentication/images/ravi.jpeg','25','205','40');

				$pdf->Cell('','3','');
				

			    $pdf->Image('../../authentication/images/ravi.jpeg','25','215','40');

			    $pdf->SetXY(25,230);
				$pdf->Cell('','',' for Director ','','','');
				

				$pdf->Output($pdffilename,'F');
				$pdf->Close();
			}			
			echo "Bank Letter PDF Generated Successfuly, ";
		}
	}


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
