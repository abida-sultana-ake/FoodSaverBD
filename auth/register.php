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

        // Check if email already exists
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);

        mysqli_stmt_bind_param($check_stmt, "s", $email);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {

            $message = "Email already exists.";

        } else {

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $sql = "INSERT INTO users (full_name, email, password, phone)
                    VALUES (?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $full_name,
                $email,
                $hashed_password,
                $phone
            );

            if (mysqli_stmt_execute($stmt)) {

                $message = "Registration successful!";

            } else {

                $message = "Registration failed.";

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
    <title>Register - FoodSaver BD</title>
</head>

<body>

    <h1>FoodSaver BD</h1>

    <h2>Create Account</h2>

    <?php if (!empty($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST">

        <label>Full Name</label><br>
        <input type="text" name="full_name" required>

        <br><br>

        <label>Email</label><br>
        <input type="email" name="email" required>

        <br><br>

        <label>Phone</label><br>
        <input type="text" name="phone">

        <br><br>

        <label>Password</label><br>
        <input type="password" name="password" required>

        <br><br>

        <button type="submit">Register</button>

    </form>

</body>

</html>