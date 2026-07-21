<?php

session_start();

include("../config/db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION["user_id"];

    $food_name = trim($_POST["food_name"]);
    $category_id = $_POST["category_id"];
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

        $image_name = NULL;

        // Image Upload
        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {

            $allowed_types = ["jpg", "jpeg", "png", "webp"];

            $file_name = $_FILES["image"]["name"];
            $file_tmp = $_FILES["image"]["tmp_name"];
            $file_size = $_FILES["image"]["size"];

            $file_ext = strtolower(
                pathinfo($file_name, PATHINFO_EXTENSION)
            );

            if (!in_array($file_ext, $allowed_types)) {

                $message = "Only JPG, JPEG, PNG and WEBP images are allowed.";

            } elseif ($file_size > 5 * 1024 * 1024) {

                $message = "Image size must be less than 5MB.";

            } else {

                $image_name = uniqid("food_", true) . "." . $file_ext;

                $upload_path = "../uploads/" . $image_name;

                move_uploaded_file($file_tmp, $upload_path);
            }
        }

        if (empty($message)) {

            $sql = "INSERT INTO food_items
                    (
                        user_id,
                        category_id,
                        food_name,
                        quantity,
                        unit,
                        purchase_date,
                        expiry_date,
                        storage_location,
                        image,
                        notes
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "iisissssss",
                $user_id,
                $category_id,
                $food_name,
                $quantity,
                $unit,
                $purchase_date,
                $expiry_date,
                $storage_location,
                $image_name,
                $notes
            );

            if (mysqli_stmt_execute($stmt)) {

                $message = "Food added successfully!";

            } else {

                $message = "Failed to add food.";

            }

            mysqli_stmt_close($stmt);
        }
    }
}


/* Load Categories */

$category_sql = "SELECT id, category_name
                 FROM categories
                 ORDER BY category_name ASC";

$category_result = mysqli_query($conn, $category_sql);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Add Food - FoodSaver BD</title>

</head>

<body>

    <h1>Add Food</h1>

    <?php if (!empty($message)): ?>

        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>


    <form method="POST" enctype="multipart/form-data">

        <label>Food Name</label><br>

        <input
            type="text"
            name="food_name"
            required
        >

        <br><br>


        <label>Category</label><br>

        <select name="category_id" required>

            <option value="">
                Select Category
            </option>

            <?php while ($category = mysqli_fetch_assoc($category_result)): ?>

                <option value="<?php echo $category["id"]; ?>">

                    <?php echo htmlspecialchars(
                        $category["category_name"]
                    ); ?>

                </option>

            <?php endwhile; ?>

        </select>

        <br><br>


        <label>Quantity</label><br>

        <input
            type="number"
            name="quantity"
            step="0.01"
            min="0"
            required
        >

        <br><br>


        <label>Unit</label><br>

        <select name="unit" required>

            <option value="">
                Select Unit
            </option>

            <option value="kg">
                Kilogram
            </option>

            <option value="gram">
                Gram
            </option>

            <option value="liter">
                Liter
            </option>

            <option value="piece">
                Piece
            </option>

            <option value="pack">
                Pack
            </option>

        </select>

        <br><br>


        <label>Purchase Date</label><br>

        <input
            type="date"
            name="purchase_date"
            required
        >

        <br><br>


        <label>Expiry Date</label><br>

        <input
            type="date"
            name="expiry_date"
            required
        >

        <br><br>


        <label>Storage Location</label><br>

        <input
            type="text"
            name="storage_location"
            placeholder="e.g. Refrigerator"
        >

        <br><br>


        <label>Food Image</label><br>

        <input
            type="file"
            name="image"
            accept=".jpg,.jpeg,.png,.webp"
        >

        <br><br>


        <label>Notes</label><br>

        <textarea
            name="notes"
            rows="5"
        ></textarea>

        <br><br>


        <button type="submit">
            Add Food
        </button>

    </form>


    <br>

    <a href="../dashboard/index.php">
        Back to Dashboard
    </a>

</body>

</html>