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

$message = "";

/* ================================
   FETCH EXISTING FOOD
================================ */

$sql = "SELECT *
        FROM food_items
        WHERE id = ?
        AND user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "ii", $food_id, $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) != 1) {
    header("Location: view_food.php");
    exit();
}

$food = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/* ================================
   UPDATE FOOD
================================ */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $food_name = trim($_POST["food_name"]);
    $category_id = (int) $_POST["category_id"];
    $quantity = $_POST["quantity"];
    $unit = trim($_POST["unit"]);
    $purchase_date = $_POST["purchase_date"];
    $expiry_date = $_POST["expiry_date"];
    $storage_location = trim($_POST["storage_location"]);
    $notes = trim($_POST["notes"]);

    if (
        empty($food_name) ||
        empty($category_id) ||
        empty($quantity) ||
        empty($unit) ||
        empty($purchase_date) ||
        empty($expiry_date)
    ) {

        $message = "Please fill in all required fields.";

    } elseif ($expiry_date < $purchase_date) {

        $message = "Expiry date cannot be before purchase date.";

    } else {

        $sql = "UPDATE food_items
                SET
                    food_name = ?,
                    category_id = ?,
                    quantity = ?,
                    unit = ?,
                    purchase_date = ?,
                    expiry_date = ?,
                    storage_location = ?,
                    notes = ?
                WHERE id = ?
                AND user_id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "sidsssssii",
            $food_name,
            $category_id,
            $quantity,
            $unit,
            $purchase_date,
            $expiry_date,
            $storage_location,
            $notes,
            $food_id,
            $user_id
        );

        if (mysqli_stmt_execute($stmt)) {

            header("Location: view_food.php");
            exit();

        } else {

            $message = "Failed to update food.";

        }

        mysqli_stmt_close($stmt);
    }
}


/* ================================
   LOAD CATEGORIES
================================ */

$category_sql = "SELECT id, category_name
                 FROM categories
                 ORDER BY category_name ASC";

$category_result = mysqli_query($conn, $category_sql);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Edit Food - FoodSaver BD</title>

</head>

<body>

    <h1>Edit Food</h1>

    <?php if (!empty($message)): ?>

        <p style="color:red;">

            <?php echo htmlspecialchars($message); ?>

        </p>

    <?php endif; ?>


    <form method="POST">

        <label>Food Name</label>

        <br>

        <input
            type="text"
            name="food_name"
            value="<?php echo htmlspecialchars($food["food_name"]); ?>"
            required
        >

        <br><br>


        <label>Category</label>

        <br>

        <select name="category_id" required>

            <?php while ($category = mysqli_fetch_assoc($category_result)): ?>

                <option
                    value="<?php echo $category["id"]; ?>"
                    <?php
                    if (
                        $category["id"]
                        == $food["category_id"]
                    ) {
                        echo "selected";
                    }
                    ?>
                >

                    <?php echo htmlspecialchars(
                        $category["category_name"]
                    ); ?>

                </option>

            <?php endwhile; ?>

        </select>

        <br><br>


        <label>Quantity</label>

        <br>

        <input
            type="number"
            name="quantity"
            step="0.01"
            min="0"
            value="<?php echo htmlspecialchars($food["quantity"]); ?>"
            required
        >

        <br><br>


        <label>Unit</label>

        <br>

        <select name="unit" required>

            <option
                value="kg"
                <?php if ($food["unit"] == "kg") echo "selected"; ?>
            >
                Kilogram
            </option>

            <option
                value="gram"
                <?php if ($food["unit"] == "gram") echo "selected"; ?>
            >
                Gram
            </option>

            <option
                value="liter"
                <?php if ($food["unit"] == "liter") echo "selected"; ?>
            >
                Liter
            </option>

            <option
                value="piece"
                <?php if ($food["unit"] == "piece") echo "selected"; ?>
            >
                Piece
            </option>

            <option
                value="pack"
                <?php if ($food["unit"] == "pack") echo "selected"; ?>
            >
                Pack
            </option>

        </select>

        <br><br>


        <label>Purchase Date</label>

        <br>

        <input
            type="date"
            name="purchase_date"
            value="<?php echo $food["purchase_date"]; ?>"
            required
        >

        <br><br>


        <label>Expiry Date</label>

        <br>

        <input
            type="date"
            name="expiry_date"
            value="<?php echo $food["expiry_date"]; ?>"
            required
        >

        <br><br>


        <label>Storage Location</label>

        <br>

        <input
            type="text"
            name="storage_location"
            value="<?php echo htmlspecialchars(
                $food["storage_location"]
            ); ?>"
        >

        <br><br>


        <label>Notes</label>

        <br>

        <textarea
            name="notes"
            rows="5"
        ><?php echo htmlspecialchars($food["notes"]); ?></textarea>

        <br><br>


        <button type="submit">
            Update Food
        </button>

    </form>

    <br>

    <a href="view_food.php">
        Back to Inventory
    </a>

</body>

</html>