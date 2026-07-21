<?php

session_start();

include("../config/db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: view_food.php");
    exit();
}

$food_id = (int) $_GET["id"];


/* =================================
   GET FOOD IMAGE BEFORE DELETE
================================= */

$sql = "SELECT image
        FROM food_items
        WHERE id = ?
        AND user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $food_id,
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) != 1) {
    header("Location: view_food.php");
    exit();
}

$food = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/* =================================
   DELETE FOOD
================================= */

$sql = "DELETE FROM food_items
        WHERE id = ?
        AND user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $food_id,
    $user_id
);

if (mysqli_stmt_execute($stmt)) {

    /* Delete image file if exists */

    if (
        !empty($food["image"]) &&
        file_exists("../uploads/" . $food["image"])
    ) {

        unlink("../uploads/" . $food["image"]);
    }
}

mysqli_stmt_close($stmt);

header("Location: view_food.php");

exit();

?>