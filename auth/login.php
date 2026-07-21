<?php

session_start();

include("../config/db.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {

        $message = "Please enter email and password.";

    } else {

        $sql = "SELECT id, full_name, email, password
                FROM users
                WHERE email=?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $email);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user["password"])) {

                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["full_name"] = $user["full_name"];
                $_SESSION["email"] = $user["email"];

                header("Location: ../dashboard/index.php");
                exit();

            } else {

                $message = "Invalid email or password.";

            }

        } else {

            $message = "Invalid email or password.";

        }

        mysqli_stmt_close($stmt);
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login | FoodSaver BD</title>
<link rel="stylesheet" href="../assets/css/style.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body>

<div class="auth-container">

<div class="auth-card">

<div class="logo">

<i class="fa-solid fa-leaf"></i>

<h2>FoodSaver BD</h2>

<p>Reduce Food Waste. Save More.</p>

</div>

<h3>Login</h3>

<?php if(!empty($message)){ ?>

<div class="alert">

<?php echo htmlspecialchars($message); ?>

</div>

<?php } ?>

<form method="POST">

<div class="input-group">

<label>Email</label>

<div class="input-box">

<i class="fa-solid fa-envelope"></i>

<input
type="email"
name="email"
placeholder="Enter your email"
required>

</div>

</div>

<div class="input-group">

<label>Password</label>

<div class="input-box">

<i class="fa-solid fa-lock"></i>

<input
type="password"
id="password"
name="password"
placeholder="Enter your password"
required>

<button
type="button"
class="toggle-password"
onclick="togglePassword()">

<i
id="eye"
class="fa-solid fa-eye">
</i>

</button>

</div>
</div>

<div class="options">

<label>

<button
type="submit"
class="login-btn">

<i class="fa-solid fa-right-to-bracket"></i>

Login

</button>

</form>

<div class="bottom-text">

<p>

Don't have an account?

<a href="register.php">

Register

</a>

</p>

</div>

</div>

</div>

<script>

function togglePassword(){

    const password =
    document.getElementById("password");

    const eye =
    document.getElementById("eye");

    if(password.type==="password"){

        password.type="text";

        eye.classList.remove("fa-eye");

        eye.classList.add("fa-eye-slash");

    }else{

        password.type="password";

        eye.classList.remove("fa-eye-slash");

        eye.classList.add("fa-eye");

    }

}

</script>

</body>

</html>