<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

$user_id = $_SESSION['user_id'];
$page_title = "Edit Profile";

/* Logged In User */

$userQuery = mysqli_query($conn,
"SELECT * FROM users
WHERE id='$user_id'");

$user = mysqli_fetch_assoc($userQuery);

$user_name = $user['full_name'];

$message = "";

/* Update Profile */

if(isset($_POST['update_profile'])){

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);




    $stmt = mysqli_prepare($conn,
    "UPDATE users
    SET
    full_name=?,
    email=?,
    phone=?,
    WHERE id=?");

    mysqli_stmt_bind_param(
        $stmt,
        "sssssi",
        $full_name,
        $email,
        $phone,
        $user_id
    );

    if(mysqli_stmt_execute($stmt)){

        $message = "Profile updated successfully.";

        $user = mysqli_fetch_assoc(
            mysqli_query(
                $conn,
                "SELECT * FROM users
                 WHERE id='$user_id'"
            )
        );

    }else{

        $message = "Something went wrong.";

    }

}

include("../includes/header.php");
include("../includes/sidebar.php");
include("../includes/topbar.php");
?>

<div class="main-content">

<div class="page-title">

<h2>

<i class="fa-solid fa-user-pen"></i>

Edit Profile

</h2>

</div>

<?php if($message!=""){ ?>

<div class="alert-success">

<?php echo $message; ?>

</div>

<?php } ?>

<div class="form-card">

<form method="POST" enctype="multipart/form-data">

<div class="form-row">

<div class="form-group">

<label>Full Name</label>

<input
type="text"
name="full_name"
value="<?php echo htmlspecialchars($user['full_name']); ?>"
required>

</div>

<div class="form-group">

<label>Email</label>

<input
type="email"
name="email"
value="<?php echo htmlspecialchars($user['email']); ?>"
required>

</div>

</div>

<div class="form-row">
    <div class="form-group">

<label>Phone Number</label>

<input
type="text"
name="phone"
value="<?php echo htmlspecialchars($user['phone']); ?>">

</div>


</div>

</div>

<div class="profile-actions">

<button
type="submit"
name="update_profile"
class="btn">

<i class="fa-solid fa-floppy-disk"></i>

Save Changes

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