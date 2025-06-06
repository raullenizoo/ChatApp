<?php
session_start();
include_once "config.php";
$fname = mysqli_real_escape_string($conn, $_POST['fname']);
$lname = mysqli_real_escape_string($conn, $_POST['lname']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);
if (!empty($fname) && !empty($lname) && !empty($email) && !empty($password)) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
        if (mysqli_num_rows($sql) > 0) {
            echo "$email - This email already exist!";
        } else {
            if (isset($_FILES['image'])) {
                $img_name = $_FILES['image']['name'];
                $img_type = $_FILES['image']['type'];
                $tmp_name = $_FILES['image']['tmp_name'];

                $img_explode = explode('.', $img_name);
                $img_ext = end($img_explode);

                $extensions = ["jpeg", "png", "jpg"];
                if (in_array($img_ext, $extensions) === true) {
                    $types = ["image/jpeg", "image/jpg", "image/png"];
                    if (in_array($img_type, $types) === true) {
                        $time = time();
                        $new_img_name = $time . $img_name;
                        if (move_uploaded_file($tmp_name, "images/" . $new_img_name)) {
                            $ran_id = rand(time(), 100000000);
                            $status = "Active now";
                            $encrypt_pass = md5($password);
                            $code = rand(999999, 111111);
                            $otpstatus = "notverified";
                            $insert_query = mysqli_query($conn, "INSERT INTO users (unique_id, fname, lname, email, password, img, status,code,otpstatus)
                                VALUES ({$ran_id}, '{$fname}','{$lname}', '{$email}', '{$encrypt_pass}', '{$new_img_name}', '{$status}', '{$code}', '{$otpstatus}')");
                            if ($insert_query) {
                                $subject = "Email Verification Code";
                                $message = "Your verification code is $code";
                                $sender = "From: deeppatel.dp1910@gmail.com";
                                if (mail($email, $subject, $message, $sender)) {
                                    $select_sql2 = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
                                    if (mysqli_num_rows($select_sql2) > 0) {
                                        $result = mysqli_fetch_assoc($select_sql2);
                                        $_SESSION['unique_id'] = $result['unique_id'];
                                        echo "success";
                                    } else {
                                        echo "This email address not Exist!";
                                    }
                                }         
                            } else {
                                echo "Something went wrong. Please try again!";
                            }
                        }
                    } else {
                        echo "Please upload an image file - jpeg, png, jpg";
                    }
                } else {
                    echo "Please upload an image file - jpeg, png, jpg";
                }
            }
        }
    } else {
        echo "$email is not a valid email!";
    }
} else {
    echo "All input fields are required!";
}

// if(isset($_POST['check'])){
//     $_SESSION['info'] = "";
//     $otp_code = mysqli_real_escape_string($conn, $_POST['otp']);
//     $check_code = "SELECT * FROM usertable WHERE code = $otp_code";
//     $code_res = mysqli_query($conn, $check_code);
//     if(mysqli_num_rows($code_res) > 0){
//         $fetch_data = mysqli_fetch_assoc($code_res);
//         $fetch_code = $fetch_data['code'];
//         $email = $fetch_data['email'];
//         $code = 0;
//         $otpstatus = 'verified';
//         $update_otp = "UPDATE usertable SET code = $code, otpstatus = '$otpstatus' WHERE code = $fetch_code";
//         $update_res = mysqli_query($conn, $update_otp);
//         if($update_res){
//             $_SESSION['name'] = $name;
//             $_SESSION['email'] = $email;
//             $select_sql2 = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
//                                 if (mysqli_num_rows($select_sql2) > 0) {
//                                     $result = mysqli_fetch_assoc($select_sql2);
//                                     $_SESSION['unique_id'] = $result['unique_id'];
//                                     echo "success";
//         }else{
//             $errors['otp-error'] = "Failed while updating code!";
//         }
//     }else{
//         $errors['otp-error'] = "You've entered incorrect code!";
//     }
// }}

?>