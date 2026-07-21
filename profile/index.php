<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

$user_id = $_SESSION['user_id'];

$page_title = "My Profile";

/* Get User Information */

$sql = "SELECT *
        FROM users
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

/* Dashboard Statistics */

/* Total Foods */

$total = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) AS total
FROM food_items
WHERE user_id='$user_id'"))['total'];

/* Fresh Foods */

$fresh = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) AS total
FROM food_items
WHERE user_id='$user_id'
AND expiry_date > DATE_ADD(CURDATE(), INTERVAL 3 DAY)
"))['total'];

/* Expired Foods */

$expired = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) AS total
FROM food_items
WHERE user_id='$user_id'
AND expiry_date < CURDATE()
"))['total'];

/* Shared Foods */

$shared = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) AS total
FROM shared_food
WHERE user_id='$user_id'
"))['total'];

$user_name = $user['full_name'];

include("../includes/header.php");
include("../includes/sidebar.php");
include("../includes/topbar.php");
?>

<div class="main-content">

<div class="page-title">

    <h2>

        <i class="fa-solid fa-user"></i>

        My Profile

    </h2>

</div>

<div class="profile-card">

    <div class="profile-left">

        <?php if(!empty($user['profile_image'])){ ?>

            <img
            src="../uploads/<?php echo htmlspecialchars($user['profile_image']); ?>"
            class="profile-image"
            alt="Profile">

        <?php } else { ?>

            <div class="profile-placeholder">

                <i class="fa-solid fa-user"></i>

            </div>

        <?php } ?>

        <h2>

            <?php echo htmlspecialchars($user['full_name']); ?>

        </h2>

        <p>

            <?php echo htmlspecialchars($user['email']); ?>

        </p>

    </div>

    <div class="profile-right">
                <div class="details-grid">

            <div class="detail-item">

                <span><i class="fa-solid fa-phone"></i> Phone</span>

                <strong>

                    <?php
                    echo !empty($user['phone'])
                        ? htmlspecialchars($user['phone'])
                        : "Not Provided";
                    ?>

                </strong>

            </div>

            <div class="detail-item">

                <span><i class="fa-solid fa-location-dot"></i> Address</span>

                <strong>

                    <?php
                    echo !empty($user['address'])
                        ? htmlspecialchars($user['address'])
                        : "Not Provided";
                    ?>

                </strong>

            </div>

            <div class="detail-item">

                <span><i class="fa-solid fa-calendar-days"></i> Joined</span>

                <strong>

                    <?php
                    echo date("d M Y", strtotime($user['created_at']));
                    ?>

                </strong>

            </div>

            <div class="detail-item">

                <span><i class="fa-solid fa-envelope"></i> Email</span>

                <strong>

                    <?php echo htmlspecialchars($user['email']); ?>

                </strong>

            </div>

        </div>

        <div class="stats">

            <div class="stat-card">

                <h3>Total Foods</h3>

                <h1><?php echo $total; ?></h1>

            </div>

            <div class="stat-card">

                <h3>Fresh Foods</h3>

                <h1><?php echo $fresh; ?></h1>

            </div>

            <div class="stat-card">

                <h3>Expired Foods</h3>

                <h1><?php echo $expired; ?></h1>

            </div>

            <div class="stat-card">

                <h3>Shared Foods</h3>

                <h1><?php echo $shared; ?></h1>

            </div>

        </div>

        <div class="profile-actions">

            <a href="edit_profile.php" class="btn">

                <i class="fa-solid fa-user-pen"></i>

                Edit Profile

            </a>

            <a href="change_password.php" class="btn btn-edit">

                <i class="fa-solid fa-lock"></i>

                Change Password

            </a>

        </div>

    </div>

</div>

</div>

<?php

mysqli_stmt_close($stmt);
mysqli_close($conn);

include("../includes/footer.php");

?>