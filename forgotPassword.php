<?php
session_start();
include('db.php'); 

$email_value = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email_value = $_POST['email'];
    $email = trim($_POST['email']);

    $check = $conn->prepare("SELECT Email FROM accountManagement WHERE Email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {

        $otp = rand(100000, 999999);
        $expires = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        $stmt = $conn->prepare("INSERT INTO otpVerification (Email, OtpCode, Expires) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $otp, $expires);
        $stmt->execute();

        $_SESSION['reset_email'] = $email;

        echo "<script src='https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js'></script>";
        echo "<script>
            (function(){ emailjs.init('vce1kKWjaclFPVjaA'); })();

            var templateParams = {
                email: '$email',
                passcode: '$otp',
                time: '15 minutes'
            };

            emailjs.send('service_ig46uf6', 'template_4o0z2ga', templateParams)
            .then(function() {
                alert('OTP sent to your email!');
                window.location.href = 'accountVerification.php';
            }, function(error) {
                alert('Failed to send email: ' + JSON.stringify(error));
            });
        </script>";
        exit();

    } else {
        echo "<script>alert('Email not found!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Forgot Password</title>

<style>
body{
    margin: 0;
    height: 100vh;
    display:flex;
    justify-content: center;
    align-items: center;
    background-color: #0E1422;
    font-family: sans-serif;
    flex-direction: column;
}

h1{
    font-size: 20px;
    color: #f0c800;
    text-align: center;
    margin-bottom: 30px;
    letter-spacing: 2px;
}

.container{
    width: 450px;
    background: #163153;
    border: 2px solid #ffce1b;
    border-radius: 25px;
    padding: 30px 50px 50px;
    text-align: center;
    box-shadow: 0 0 25px rgba(255, 206, 27, 0.15);
}

.inputBox{
    width: 350px;
    margin: 0 auto;
    position: relative;
}

input[type="email"]{
    width: 350px;
    box-sizing: border-box;
    padding: 10px;
    background-color: transparent;
    border: 3px solid #FFCE1B;
    outline: none;
    color: white;
    font-size: 16px;
    border-radius: 8px;
}

label{
    position: absolute;
    left: 12px;
    top: 12px;
    color: grey;
    pointer-events: none;
    transition: 0.3s;
    background-color: #163153;
    padding: 0 5px;
}

input:focus + label,
input:not(:placeholder-shown) + label{
    top: -8px;
    font-size: 12px;
    color: white;
}

input[type="submit"]{
    margin-top: 20px;
    width: 350px;
    font-size: 18px;
    padding: 10px;
    border-radius: 8px;
    background: #f0c800;
    color: #0E1422;
    font-weight: bold;
    cursor: pointer;
    border: none;
}

input[type="submit"]:hover{
    background: #ffd633;
}

.back{
    margin-top: 20px;
    font-size: 12px;
    color: white;
}

a{
    color: white;
    text-decoration: none;
}
</style>
</head>

<body>

<div class="container">
    <img src="Logo.png" width="100">

    <h1>FORGOT <span style="color:white;">PASSWORD</span></h1>

    <form action="forgotPassword.php" method="POST">

        <div class="inputBox">
            <input type="email" name="email"
                   value="<?php echo htmlspecialchars($email_value); ?>"
                   required placeholder=" ">
            <label>Enter your email:</label>
        </div>

        <input type="submit" value="Submit">

    </form>

    <div class="back">
        <a href="index.php">← Back to Login</a>
    </div>
</div>

</body>
</html>