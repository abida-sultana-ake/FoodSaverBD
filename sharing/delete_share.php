<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

$user_id = $_SESSION['user_id'];


/* Check Share ID */

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$share_id = intval($_GET['id']);


/* Delete Only User's Own Shared Food */

$sql = "DELETE FROM shared_food
        WHERE id = ?
        AND user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $share_id,
    $user_id
);


if (mysqli_stmt_execute($stmt)) {

    header("Location: index.php?deleted=1");
    exit();

} else {

    echo "Delete failed.";

}


mysqli_stmt_close($stmt);

mysqli_close($conn);

?>