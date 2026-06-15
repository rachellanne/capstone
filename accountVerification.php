<?php
session_start();
include('db.php');

$message = "";
$status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['reset_email'] ?? '';

    if (!empty($_POST['otp'])) {
        $user_otp = implode("", $_POST['otp']);

        $stmt = $conn->prepare("SELECT * FROM otpVerification WHERE Email = ? AND OtpCode = ? AND Expires > NOW()");
        $stmt->bind_param("ss", $email, $user_otp);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $conn->query("DELETE FROM otpVerification WHERE Email = '$email'");
            $status = "success";
            $message = "Verification Successful!";
        } else {
            $status = "error";
            $message = "Invalid or expired code!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Account Verification</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
  font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.container{
    width: 450px;
    background: #163153;
    border: 2px solid #ffce1b;
    border-radius: 25px;
    padding: 30px 50px 50px;
    height: 320px;
    text-align: center;
    box-shadow: 0 0 25px rgba(255, 206, 27, 0.15);
}

.container img{
    width: 100px;
    height: 70px;
    margin-bottom: 10px;
}

.inputBox{
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 10px;
}

.otpInput{
    width: 40px;
    height: 45px;
    text-align: center;
    font-size: 18px;
    border: 3px solid #FFCE1B;
    border-radius: 8px;
    background: transparent;
    color: white;
    outline: none;
}

.otpInput:focus{
    background: rgba(255,255,255,0.1);
}

input[type="submit"]{
    display: block;
    width: 350px;
    margin: 20px auto 0;
    font-size: 18px;
    padding: 10px;
    border: 1px solid white;
    border-radius: 8px;
    background: #f0c800;
    color: #0E1422;
    font-weight: bold;
    cursor: pointer;
}

input[type="submit"]:hover{
    background: #ffd633;
}

.highlight{
    color: white;
}

label{
    color: rgb(219, 215, 215);
    display: block;
    margin-top: 10px;
}

.back{
    margin-top: 20px;
    font-size: 12px;
}

.back a{
    color: white;
    text-decoration: none;
}
.popup {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: 0.3s;
        }

        .popup.show {
            opacity: 1;
            pointer-events: auto;
        }

        .popup-box {
            background-color: #0E1422;
          
            border: 1px solid #f0c800;
            border-radius: 14px;
            padding: 25px;
            width: 280px;
            text-align: center;
            transform: scale(0.85);
            transition: 0.3s;
        }

        .popup.show .popup-box { transform: scale(1); }

        .popup-icon { font-size: 42px; margin-bottom: 10px; }

        .success { color: #22c55e; }
        .error { color: #ef4444; }

        .popup button {
            margin-top: 15px;
            background:#f0c800;
            color: black;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

    </style>
</head>

<body>
    <div class="container">
        <img src="Logo.png" width="100" style="margin-bottom:10px;">
        <h1>ACCOUNT <span class="highlight">VERIFICATION</span></h1>
        <label style="color:white;">Enter Verify Code Below</label>
        <form id="otpForm" action="accountVerification.php" method="POST">
            <div class="inputBox">
                <?php for ($i = 0; $i < 6; $i++): ?>
                    <input type="text" name="otp[]" class="otpInput" maxlength="1" required>
                <?php endfor; ?>
            </div>
            <input type="submit" value="Verify Code">
        </form>

        <div class="back" style="margin-top: 20px;"><a href="forgotPassword.php" style="color: white; text-decoration: none;">← Back</a></div>
    </div>

 <div id="popup" class="popup">
        <div class="popup-box">
            <div id="icon" class="popup-icon">✔</div>
            <h3 id="title" style="color: white;">Success</h3>
            <p id="msg" style="color: #ccc;"></p>
            <button onclick="closePopup()">OK</button>
        </div>
    </div>



    </div>

    <script>
    const inputs = document.querySelectorAll(".otpInput");
    const popup = document.getElementById("popup");
    const icon = document.getElementById("icon");
    const title = document.getElementById("title");
    const msg = document.getElementById("msg");

    inputs.forEach((input, index) => {
        input.addEventListener("input", (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, "");
            if (e.target.value && index < inputs.length - 1) inputs[index + 1].focus();
        });
        input.addEventListener("keydown", (e) => {
            if (e.key === "Backspace" && !input.value && index > 0) inputs[index - 1].focus();
        });
        input.addEventListener("paste", (e) => {
            e.preventDefault();
            const pasteData = e.clipboardData.getData("text").slice(0, 6);
            pasteData.split("").forEach((char, i) => { if (inputs[index + i]) inputs[index + i].value = char; });
        });
    });

    function showPopup(type, message) {
        popup.classList.add("show");
        title.innerText = type === "success" ? "Success" : "Error";
        icon.innerText = type === "success" ? "✔" : "✖";
        icon.className = "popup-icon " + type;
        msg.innerText = message;
    }

    function closePopup() {
        popup.classList.remove("show");
        if (title.innerText === "Success") {
            window.location.href = 'newPassword.php'; 
        }
    }

  <?php if ($status == 'success'): ?>
        showPopup("success", "<?php echo $message; ?>");
    <?php elseif ($status == 'error'): ?>
        showPopup("error", "<?php echo $message; ?>");
    <?php endif; ?>
</script>
</body>

</html>