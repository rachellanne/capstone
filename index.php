<?php
session_start();
include("db.php");

$error = "";
$username_value = "";

if(isset($_POST['login']))
{
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

   
    $username_value = $username;

    $query = mysqli_query($conn,
        "SELECT * FROM accountManagement
         WHERE BINARY Username='$username'
         LIMIT 1"
    );

    if(mysqli_num_rows($query) > 0)
    {
        $row = mysqli_fetch_assoc($query);

        if(password_verify($password, $row['Password']))
        {
            $_SESSION['ID'] = $row['ID'];
            $_SESSION['Username'] = $row['Username'];

            header("Location: adminDashboard.php");
            exit();
        }
        else
        {
            $error = "Invalid Username or Password!";
        }
    }
    else
    {
        $error = "Invalid Username or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Page</title>

<style>
body{
    margin: 0;
    height: 100vh;
    display:flex;
    justify-content: center;
    align-items: center;
    background-color: #0E1422;
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    flex-direction: column;
}

.container{
    display: flex;
    height: 100vh;
    align-items: center;
    justify-content: space-evenly;
}

.left{
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
}

.left img{
    width: 350px;
}

.loginCard {
  background: #163153;
  border: 2px solid #c9a800;
  border-radius: 20px;
  padding: 30px 50px 50px;
  width: 270px;
  box-shadow: 0 0 30px rgba(200,168,0,0.18);
}

h1{
    font-size: 25px;
    color: #f0c800;
    text-align: center;
    margin-bottom: 30px;
    letter-spacing: 2px;
}

.highlight{ color: white; }

.inputBox{
    position: relative;
    width: 250px;
    margin-bottom: 14px;
}

input{
    width: 100%;
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

input:focus+label,
input:valid+label{
    top: -8px;
    font-size: 12px;
    color: white;
}

.checkBox{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
    width: 100%;
}

.remember{
    display: flex;
    align-items: center;
    gap: 6px;
    color: white;
}

.remember input{
    margin: 0;
    width: 14px;
    height: 14px;
}

.remember label{
    position: static;
    color: white;
    font-size: 12px;
    padding: 0;
    background: none;
}

.forgot a{
    color: white;
    font-size: 12px;
    cursor: pointer;
}

.btn-login {
    width: 100%;
    margin-top: 20px;
    padding: 10px;
    border: 1px solid white;
    border-radius: 8px;
    background: #f0c800;
    color: #0E1422;
    font-weight: bold;
    cursor: pointer;
}

.btn-login:hover {
    background: #ffd633;
}

#password{
    width: 100%;
}

#toggle{
    position: absolute;
    right: 10px;
    top: 30%;
    transform: translate(45%);
    cursor: pointer;
    font-size: 14px;
    color: aliceblue;
    user-select: none;
    display: none;
}


.error-message{
    width: 100%;
    background: rgba(255, 0, 0, 0.15);
    border: 1px solid #ff4d4d;
    color: #ff4d4d;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
    text-align: center;
    font-size: 14px;
    box-sizing: border-box;
}
</style>
</head>

<body>

<div class="container">

    <div class="left">
        <img src="nowatermark_gif.gif" alt="logo">
    </div>

    <div class="loginCard">

        <h1>LOG<span class="highlight">IN</span></h1>

        <?php if(!empty($error)) { ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <form method="POST">

            <div class="inputBox">
                <input type="text"
                       name="username"
                       value="<?php echo htmlspecialchars($username_value); ?>"
                       required>
                <label>Username</label>
            </div>

            <div class="inputBox">
                <input type="password" name="password" id="password" required>
                <label>Password</label>
                <span id="toggle">Show</span>
            </div>

            <div class="checkBox">
                <div class="remember">
                    <input type="checkbox">
                    <label>Remember Me</label>
                </div>

                <div class="forgot">
                    <a href="forgotPassword.php"><u>Forgot Password?</u></a>
                </div>
            </div>

            <button type="submit" name="login" class="btn-login">
                Login
            </button>

        </form>

    </div>

</div>

<script>

      document.addEventListener("contextmenu", e=>{
        e.preventDefault();
      
    }) 

    document.addEventListener("keydown",e=>{
        if(e.key == "F12"){
            e.preventDefault();
            
        }
    })
const password = document.getElementById("password");
const toggle = document.getElementById("toggle");

password.addEventListener("input", () => {
    if(password.value.length > 0){
        toggle.style.display = "block";
    }else{
        toggle.style.display = "none";
        password.type = "password";
        toggle.textContent = "Show";
    }
});

toggle.addEventListener("click", () => {
    if(password.type == "password"){
        password.type = "text";
        toggle.textContent = "Hide";
    }else{
        password.type = "password";
        toggle.textContent = "Show";
    }
});
</script>

</body>
</html>