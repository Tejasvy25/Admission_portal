<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include '../../db_connect.php';
include '../../ma_config.php';
include '../class.phpmailer.php'; 
$upload_path = '../profile_images/';


//this for Registration
if (array_key_exists('Register', $_POST)) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $board = $_POST['board'];
    $boardno = $_POST['boardnumber'];
    $usertype = "student";
    $btech = "-";
    $phoneno = "-";
    $status = md5(uniqid(rand(), true));
    $profileupdate = "No";
    $photo_status = "no";
    $educationdetails_status = "no";
    $created = date('Y-m-d h:m:s');
    $email_status = "no";
    $i = 0;
    $result = array();
    
    $querry = "select * from ma_users where email='" . $email . "'";
    
    if ($stmt = $mysqli->prepare($querry)) {
        $stmt->execute();
        while ($stmt->fetch()) {
            $i = 1;
        }
        $stmt->close();
    }
    if ($i == 1) {

        $result = array('result' => 'failure', 'msg' => "User already exist with same email id");
    } else {
        //generating encryted random number

        $Salt_password = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));

        //generating encryted password by binding user given password and encryted random number

        $password = hash('sha512', $password . $Salt_password);


        $sql = "insert into ma_users (username, email, password, salt, usertype, status, board_name, board_number,btech, phone_no, photo_status, profileupdate,educationdetails_status,  created, email_status) 
        VALUES ('$name', '$email', '$password', '$Salt_password', '$usertype', '$status', '$board', '$boardno', '$btech', '$phoneno', '$photo_status', '$profileupdate','$educationdetails_status', '$created', '$email_status')";

        $query = $mysqli->prepare($sql);
        $query->execute();

        if ($query) {
            $sql1 = "INSERT INTO ma_user_profile (email, full_name) VALUES ('$email', '$name')";
            $query1 = $mysqli->prepare($sql1);
            $query1->execute();
            
            //counting android registrations
            $sql2 = "INSERT INTO androidDownloads (email,created_on) VALUES ('$email','$created')";
            $query2 = $mysqli->prepare($sql2);
            $query2->execute();

            //Sending email for Verification
            if ($query1) {
               if ($query1) {
                $ch = curl_init(SERVER.'authentication/GatServices/sendActivationEmail.php?');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "email=".$email);
                curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                curl_exec($ch);
                curl_close($ch);
                $result = array('result' => 'Success', 'msg' => "Registration Successfull Actvation Link has been Sent to email Please Activate ");
            }
        }
    }
}
echo json_encode($result);
} 

//This is for Login

else if (array_key_exists('Login', $_POST)) {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $result = array();
    $data = array();
    $querry = "select password,salt,status from ma_users where email='" . $email . "'";
    $i=0;
    if ($stmt = $mysqli->prepare($querry)) {
        $stmt->execute();

        $stmt->bind_result($db_password, $salt,$status);
        while($stmt->fetch()){
            $i=1;
        }
        //generating password with status from db
        $password = hash('sha512', $password.$salt);
        $stmt->close();
    }
    if ($i == 1) {

        if($status == "active")
        {
            if ($db_password == $password) {
                $sql = "select u.username,u.email,u.status,u.board_name,u.board_number,u.btech,u.phone_no,u.profileupdate,u.photo_status,m.image_url from ma_users as u,ma_user_profile as m where u.email='$email' and u.email =m.email ";
                $sql1 = mysqli_query($con,$sql);

                if (!$sql1) {
                    echo "Could not successfully run query ($sql1) from DB: " . mysql_error();
                    exit;
                }

                if (mysqli_num_rows($sql1) == 0) {
                    echo "No rows found, nothing to print so am exiting";
                    exit;
                }

                $index = 0;

                while ($row = mysqli_fetch_assoc($sql1)) {
                    $data=$row;
                }
                $result = array('result' => 'success', 'msg' => "Login Successful", "data" => $data);
            } else {
                $result = array('result' => 'failure', 'msg' => "Password you used is not correct.Please retry using the correct login details.");
            }
            
        } 
        //sending mail if the user is not activated and try to login
        else {
            $ch = curl_init(SERVER.'authentication/GatServices/sendActivationEmail.php?');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "email=".$email);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            curl_exec($ch);
            curl_close($ch);
            $result = array('result' => 'failure', 'msg' => "Your not activated your account please activate your account Login again");
        }
    }
    else {
        $result = array('result' => 'failure', 'msg' => "Username or Password you used is not correct.Please retry using the correct login details.");
    }
    echo json_encode($result);
} 

//this is for update Profile
else if (array_key_exists('UpdateProfile', $_POST)) {

    $email = $_POST['email'];
    $name = $_POST['FullName'];
    $parentname = $_POST['parent'];
    $relation1 = $_POST['relation'];
    $dob = $_POST['DOB'];
    $gender = $_POST['Gender'];
    $nationality = $_POST['Nationality'];
    $ad1 = $_POST['ad1'];
    $ad2 = $_POST['ad2'];
    $place = $_POST['village'];
    $city = $_POST['city'];
    $pincode = $_POST['pincode'];
    $phoneno = $_POST['Phone_Num'];
    $parentno = $_POST['Parent_Num'];
    $btech = $_POST['Btech'];
    $board = $_POST['Board'];
    $boardno = $_POST['board_number'];
    $image = $_POST['user_image'];
    $photo_status="yes";

    
    if ($image != '' ) {

        $data = "data:image/png;base64,$image";

        list($type, $data) = explode(';', $data);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);
        $image_ext_name = preg_replace('/[^\w\.\- ]/', '', $email);
        $url = $upload_path.$image_ext_name . ".jpeg";
        $img_name = preg_replace('/[^\w\.\- ]/', '', $email) . ".jpeg";
        // $url = $upload_path.$email . ".png";
        // $img_name = $email . ".png";
        //echo "urlll ".$img_name;
        file_put_contents($url, $data);
        $image = array(
            'user_image' => $url,
        );

    }

    if ($insert_stmt = $mysqli->prepare("update ma_user_profile set full_name=?, gender =?, date_of_birth=?, nationality=?, address_line1=?, address_line2=?, place_town=?, city = ?, pincode=?, mobile_no=?, landline_no=?, parent_name=?, parent_relation=?, image_url=? where email = ? ")) {
        $insert_stmt->bind_param('sssssssssssssss', $name, $gender, $dob, $nationality, $ad1, $ad2, $place, $city, $pincode, $phoneno, $parentno, $parentname, $relation1,$img_name, $email);
        $insert_stmt->execute();
        $insert_stmt->close();

        if ($insert_stmt1 = $mysqli->prepare("update ma_users set username=?, board_name =?, board_number=?, btech=?, phone_no=?,photo_status=? where email=?")) {
            $insert_stmt1->bind_param('sssssss', $name, $board, $boardno, $btech, $phoneno,$photo_status, $email);
            $insert_stmt1->execute();
            $insert_stmt1->close();

            if ('profileupdate' != "yes") {
                $profileupdate = "yes";
                if ($insert_stmt = $mysqli->prepare("update ma_users set profileupdate = ? where email = ? ")) {
                    $insert_stmt->bind_param('ss', $profileupdate, $email);
                    $insert_stmt->execute();
                    $insert_stmt->close();
                } else {
                    echo "Not able to Save your Personal Details. Please call us to help you.";
                    exit;
                }
            }
            $sql = "select username,email,status,board_name,board_number,btech,phone_no,profileupdate,photo_status from ma_users where email = '$email'";
            $sql1 = mysqli_query($con,$sql);

            if (!$sql1) {
                echo "Could not successfully run query ($sql1) from DB: " . mysql_error();
                exit;
            }

            if (mysqli_num_rows($sql1) == 0) {
                echo "No rows found, nothing to print so am exiting";
                exit;
            }

            $index = 0;
            while ($row = mysqli_fetch_assoc($sql1)) {

                    // $data[$index] = $row;
                    // $index++;
                $data=$row;
            }
            $result = array('result' => 'Success', 'msg' => "Profile Updated Sucessfully ",'data'=>$data);
        } else {
            $result = array('result' => 'Failure', 'msg' => "Please check errors ");
        }
    }
    echo json_encode($result);
}

//this for view profile
else if (array_key_exists('ViewProfile', $_POST)) {
    //require_once('database.php');


    $email = $_POST['email'];
    $result = array();
    $data = array();
    $querry = "select password,salt from ma_users where email='" . $email . "'";
    if ($stmt = $mysqli->prepare($querry)) {
        $stmt->execute();
        $stmt->bind_result($db_password, $salt);
        $stmt->fetch();
//$stmt->close();
    }
    
    if (count($stmt) == 1) {
        $sql = "select * from ma_users as u join ma_user_profile as m where u.email = '" . $email . "' and m.email=u.email ";
        $sql1 = mysqli_query($con,$sql);
        if (!$sql1) {
            echo "Could not successfully run query ($sql1) from DB: " . mysql_error();
            exit;
        }

        if (mysqli_num_rows($sql1) == 0) {
            echo "No rows found, nothing to print so am exiting";
            exit;
        }

        $index = 0;

        while ($row = mysqli_fetch_assoc($sql1)) {


            $data=$row;
        }


        $result = array('result' => 'success', 'msg' => "Profile View", "data" => $data);

    } else {
        $result = array('result' => 'failure', 'msg' => "Username / Password you used is not correct.Please retry using the correct login details.");
    }
    echo json_encode($result);
}


//this for changing password


else  if (array_key_exists('change_password', $_POST)) {
    $email=$_POST['email'];
    $current=$_POST['current'];
    $password=$_POST['password'];
    $cpassword=$_POST['cpassword'];
    
    $querry = "select password,salt,status from ma_users where email='" . $email . "'";
    $i=0;
    if ($stmt = $mysqli->prepare($querry)) {
        $stmt->execute();

        $stmt->bind_result($db_password, $salt,$status);
        while($stmt->fetch()){
            $i=1;
        }
        $current = hash('sha512', $current.$salt);

        $stmt->close();
    }

    if($current==$db_password)
    {

            //generating encryted random number

        $Salt_password = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));

        //generating encryted password by binding user given password and encryted random number

        $password = hash('sha512', $password . $Salt_password);

        if ($insert_stmt1 = $mysqli->prepare("update ma_users set password=?, salt =? where email=?")) {
            $insert_stmt1->bind_param('sss', $password, $Salt_password,$email);
            $insert_stmt1->execute();
            $insert_stmt1->close();
            $result=array('result'=>"Success",'msg'=>"Password Change Sucessfully");
        }
    }
    else
    {
     $result=array('result'=>"Failure",'msg'=>"Password You Entered is incorrect Please provide correct password");
 }

 echo json_encode($result);
}

//this is for forget Password.

elseif (array_key_exists('forget_password', $_POST)) {

 $post = (!empty($_POST)) ? true : false;
 if($post)
 {

    $emailID = stripslashes($_POST['email']);
    $status = md5(uniqid(rand(), true));
    $email_status = "No Response";
    $flag = 0;
    if ($stmt = $mysqli->prepare("SELECT username from ma_users WHERE email = ? ")) 
    { 
        $stmt->bind_param('s', $emailID); 
        $stmt->execute();
        $stmt->bind_result($userName);
        while($stmt->fetch())
        {
            $flag=1;                                            
        }
        $stmt->close();                                                 

        if($flag==0)
        {
            $result=array('result'=>"failure",'msg'=>"We did not find any registrations with the given email Id. Please register. ");
        }                                                                                   
        else
        {   


            if ($insert_stmt = $mysqli->prepare("INSERT INTO forgotPassword VALUES (?, ?, ? )")) 
            {    
                //echo $flag; 
                $insert_stmt->bind_param('sss', $emailID,$status, $email_status); 

                $insert_stmt->execute();
                $insert_stmt->close();

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
                $mail->Subject  =  "MSIT Admissions ".YEARTEXT." Account Activation"; 
                $mail->Body     =  "Please click on link to reset your Account. Click here ".SERVER."reset_process.php?userName=".$emailID."&key=".$status;  
                $mail->AddAddress($emailID); 
                $flag=1;

                while($flag==1)
                {
                    if($mail->Send())
                    {

                        $mail->ClearAddresses();
                        $email_status = "sent";
                        if ($insert_stmt = $mysqli->prepare("update forgot_password set email_status=? where emailID = ? ")) 
                        {               
                            $insert_stmt->bind_param('ss', $email_status, $emailID ); 
                            $insert_stmt->execute();
                            $insert_stmt->close();
                        }
                        $flag=0;
                    }
                }
                $result=array('result'=>"success",'msg'=>"Email sent Sucessfully ");
            }
            else
            {
             $result=array('result'=>"failure",'msg'=>"unable to send email please contct us");
         }   
     }      
 }   
}
else 
{ 
    $result=array('result'=>"failure",'msg'=>"Please give email Id");
}

echo json_encode($result);

}
