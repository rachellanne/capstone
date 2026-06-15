<?php
session_start();
include('db.php');

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgotPassword.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['reset_email'];

    if ($new_password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE accountManagement SET Password = ? WHERE Email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);

        if ($stmt->execute()) {
            session_destroy();
            echo "<script>alert('Password updated successfully!'); window.location.href='index.php';</script>";
            exit();
        } else {
            $message = "Error updating password. Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Password</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #0E1422;
            font-family: sans-serif;
            flex-direction: column;
        }

        .container {
            width: 450px;
            background: #163153;
            border: 2px solid #ffce1b;
            border-radius: 25px;
            padding: 30px 50px 50px;
            text-align: center;
            box-shadow: 0 0 25px rgba(255, 206, 27, 0.15);
        }

        .inputBox {
            width: 350px;
            margin: 0 auto 20px auto;
            position: relative;
        }

        input[type="password"], input[type="text"] {
            width: 100%;
            padding: 12px 60px 12px 15px;
            box-sizing: border-box;
            background-color: transparent;
            border: 3px solid #FFCE1B;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            outline: none;
        }

        label {
            position: absolute;
            left: 12px;
            top: 12px;
            color: grey;
            pointer-events: none;
            transition: 0.3s;
            background-color: #163153;
            padding: 0 5px;
        }

        input:focus + label, input:valid + label {
            top: -8px;
            font-size: 12px;
            color: white;
        }

        .toggle-btn {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 14px;
            color: #f0c800;
            user-select: none;
            z-index: 10;
        }

        input[type="submit"] {
            margin-top: 10px;
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

   
        h1{
    font-size: 20px;
    color: #f0c800;
    text-align: center;
    margin-bottom: 30px;
    letter-spacing: 2px;
}
a{
    color: white;
    text-decoration: none;
}
.back{
    margin-top: 20px;
    font-size: 12px;
    color: white;
}
    </style>
</head>
<body>

<div class="container">
    <img src="Logo.png" width="100">
    <h1>NEW <span style="color:white;">PASSWORD</span></h1>
    
    <?php if($message) echo "<p style='color:red;'>$message</p>"; ?>

    <form action="newPassword.php" method="POST">
        <div class="inputBox">
            <input type="password" name="password" class="password-field" required>
            <label>Enter new password:</label>
            <span class="toggle-btn" style="display: none;">Show</span>
        </div>

        <div class="inputBox">
            <input type="password" name="confirm_password" class="password-field" required>
            <label>Confirm password:</label>
            <span class="toggle-btn" style="display: none;">Show</span>
        </div>

        <input type="submit" value="Submit">
    </form>

    <div class="back">
       <a href="index.php">← Back to Login</a>
</div>

<script>
    document.querySelectorAll('.password-field').forEach(input => {
        const toggle = input.parentElement.querySelector('.toggle-btn');

     
        input.addEventListener('input', () => {
            if (input.value.length > 0) {
                toggle.style.display = 'block';
            } else {
                toggle.style.display = 'none';
            }
        });

       
        toggle.addEventListener('click', () => {
            if (input.type === 'password') {
                input.type = 'text';
                toggle.textContent = 'Hide';
            } else {
                input.type = 'password';
                toggle.textContent = 'Show';
            }
        });
    });
</script>

</body>
</html>