<?php

include("../includes/header.php");
include("../config/db.php");

$user_id = $_SESSION["user_id"];

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
AND expiry_date > DATE_ADD(CURDATE(), INTERVAL 3 DAY)"))['total'];

/* Expiring Soon */
$expiring = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) AS total
FROM food_items
WHERE user_id='$user_id'
AND expiry_date BETWEEN CURDATE()
AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)"))['total'];

/* Expired */
$expired = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) AS total
FROM food_items
WHERE user_id='$user_id'
AND expiry_date < CURDATE()"))['total'];

/* Shared Food */
$shared = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) AS total
FROM shared_food
WHERE user_id='$user_id'"))['total'];

include("../includes/sidebar.php");
include("../includes/topbar.php");

?>