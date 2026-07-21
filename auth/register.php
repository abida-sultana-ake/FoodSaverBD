<?php

include("../config/db.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = $_POST["password"];

    if (empty($full_name) || empty($email) || empty($password)) {

        $message = "Please fill in all required fields.";

    } else {

        $check_sql = "SELECT id FROM users WHERE email=?";

        $check_stmt = mysqli_prepare($conn, $check_sql);

        mysqli_stmt_bind_param($check_stmt,"s",$email);

        mysqli_stmt_execute($check_stmt);

        mysqli_stmt_store_result($check_stmt);

        if(mysqli_stmt_num_rows($check_stmt)>0){

            $message="Email already exists.";

        }else{

            $hashed_password=password_hash($password,PASSWORD_DEFAULT);

            $sql="INSERT INTO users
            (full_name,email,password,phone)
            VALUES (?,?,?,?)";

            $stmt=mysqli_prepare($conn,$sql);

            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $full_name,
                $email,
                $hashed_password,
                $phone
            );

            if(mysqli_stmt_execute($stmt)){

                header("Location: login.php?registered=1");
                exit();

            }else{

                $message="Registration failed.";

            }

            mysqli_stmt_close($stmt);

        }

        mysqli_stmt_close($check_stmt);

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Register | FoodSaver BD</title>

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

<p>Create your account</p>

</div>

<h3>Register</h3>

<?php if(!empty($message)){ ?>

<div class="alert">

<?php echo htmlspecialchars($message); ?>

</div>

<?php } ?>

<form method="POST">

<div class="input-group">

<label>Full Name</label>

<div class="input-box">

<i class="fa-solid fa-user"></i>

<input
type="text"
name="full_name"
placeholder="Enter your full name"
required>

</div>

</div>

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

<label>Phone</label>

<div class="input-box">

<i class="fa-solid fa-phone"></i>

<input
type="text"
name="phone"
placeholder="Enter your phone number">

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
placeholder="Create a password"
required>

<button
type="button"
class="toggle-password"
onclick="togglePassword()">

<i id="eye" class="fa-solid fa-eye"></i>

</button>

</div>
</div>

<button
type="submit"
class="login-btn">

<i class="fa-solid fa-user-plus"></i>

Create Account

</button>

</form>

<div class="bottom-text">

<p>

Already have an account?

<a href="login.php">

Login

</a>

</p>

</div>

</div>

</div>

<script>

function togglePassword(){

    const password=document.getElementById("password");

    const eye=document.getElementById("eye");

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