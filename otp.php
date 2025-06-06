<?php 
  session_start();
  include_once "php/config.php";
  if(!isset($_SESSION['unique_id'])){
    header("location: login.php");
  }
?>
<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="form signup">
      <header>Realtime Chat App</header>
      <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="error-text"></div>
        <div class="field input">
          <label>Enter verification code</label>
          <input type="text" name="vcode" placeholder="Enter verification code" required>
        </div>
        <div class="field button">
        <input type="submit" name="cc" value="Continue to Chat">
        </div>
      </form>
    </section>
  </div>

  <?php
// session_start();
if (isset($_POST['cc'])) {
    include_once "php/config.php";
    $otp_code = mysqli_real_escape_string($conn, $_POST['vcode']);
    $check_code = "SELECT * FROM users WHERE code = $otp_code";
    $code_res = mysqli_query($conn, $check_code);
    if(mysqli_num_rows($code_res) > 0){
        $fetch_data = mysqli_fetch_assoc($code_res);
        $fetch_code = $fetch_data['code'];
        $email = $fetch_data['email'];
        $code = 0;
        $otpstatus = 'verified';
        $update_otp = "UPDATE users SET code = $code, otpstatus = '$otpstatus' WHERE code = $fetch_code";
        $update_res = mysqli_query($conn, $update_otp);
        if($update_res){
            header("location: users.php");  
        }
}
}

?>
</body>
</html>
