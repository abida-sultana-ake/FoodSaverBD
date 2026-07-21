<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

$user_id = $_SESSION['user_id'];

$page_title = "Change Password";

/* Logged In User */

$userQuery = mysqli_query($conn,
"SELECT full_name,password
FROM users
WHERE id='$user_id'");

$user = mysqli_fetch_assoc($userQuery);

$user_name = $user['full_name'];

$message = "";
$error = "";

if(isset($_POST['change_password'])){

    $current_password = $_POST['current_password'];

    $new_password = $_POST['new_password'];

    $confirm_password = $_POST['confirm_password'];

    if($current_password != $user['password']){

        $error = "Current password is incorrect.";

    }

    elseif($new_password != $confirm_password){

        $error = "New passwords do not match.";

    }

    else{

        $stmt = mysqli_prepare($conn,
        "UPDATE users
        SET password=?
        WHERE id=?");

        mysqli_stmt_bind_param(
            $stmt,
            "si",
            $new_password,
            $user_id
        );

        if(mysqli_stmt_execute($stmt)){

            $message = "Password changed successfully.";

        }else{

            $error = "Something went wrong.";

        }

    }

}

include("../includes/header.php");
include("../includes/sidebar.php");
include("../includes/topbar.php");
?>

<div class="main-content">

<div class="page-title">

<h2>

<i class="fa-solid fa-lock"></i>

Change Password

</h2>

</div>

<?php if($message!=""){ ?>

<div class="alert-success">

<?php echo $message; ?>

</div>

<?php } ?>

<?php if($error!=""){ ?>

<div class="alert-danger">

<?php echo $error; ?>

</div>

<?php } ?>

<div class="form-card">

<form method="POST">
    <div class="form-group">

<label>Current Password</label>

<input
type="password"
name="current_password"
placeholder="Enter current password"
required>

</div>

<div class="form-group">

<label>New Password</label>

<input
type="password"
name="new_password"
placeholder="Enter new password"
required>

</div>

<div class="form-group">

<label>Confirm New Password</label>

<input
type="password"
name="confirm_password"
placeholder="Confirm new password"
required>

</div>

<div class="profile-actions">

<button
type="submit"
name="change_password"
class="btn">

<i class="fa-solid fa-floppy-disk"></i>

Change Password

</button>

<a
href="index.php"
class="btn btn-edit">

<i class="fa-solid fa-arrow-left"></i>

Back

</a>

</div>

</form>

</div>

</div>

<?php

mysqli_close($conn);

include("../includes/footer.php");

?>