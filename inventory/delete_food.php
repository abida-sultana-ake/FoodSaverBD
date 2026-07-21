<?php

session_start();

if (!isset($_SESSION['user_id'])) {

    header("Location: ../auth/login.php");

    exit();

}

include("../config/db.php");


$user_id = $_SESSION['user_id'];


/* Check Food ID */

if (!isset($_GET['id']) || empty($_GET['id'])) {

    header("Location: index.php");

    exit();

}


$food_id = intval($_GET['id']);


/* Get Food Image Before Delete */

$sql = "

SELECT image

FROM food_items

WHERE id = ?

AND user_id = ?

";


$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(

    $stmt,

    "ii",

    $food_id,

    $user_id

);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


if (mysqli_num_rows($result) == 0) {

    header("Location: index.php");

    exit();

}


$food = mysqli_fetch_assoc($result);


/* Delete Food */

$deleteSql = "

DELETE FROM food_items

WHERE id = ?

AND user_id = ?

";


$deleteStmt = mysqli_prepare(

    $conn,

    $deleteSql

);

mysqli_stmt_bind_param(

    $deleteStmt,

    "ii",

    $food_id,

    $user_id

);


if (

    mysqli_stmt_execute($deleteStmt)

) {


    /* Delete Image File */

    if (

        !empty($food['image']) &&

        file_exists(

            "../uploads/" .

            $food['image']

        )

    ) {

        unlink(

            "../uploads/" .

            $food['image']

        );

    }


    header(

        "Location: index.php?deleted=1"

    );

    exit();

}

else {

    echo "Failed to delete food.";

}


mysqli_stmt_close($stmt);

mysqli_stmt_close($deleteStmt);

mysqli_close($conn);

?>