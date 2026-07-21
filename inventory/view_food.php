<?php

session_start();

include("../config/db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];


// Get search value
$search = "";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}


// Get category filter
$category_filter = "";

if (isset($_GET["category"])) {
    $category_filter = $_GET["category"];
}


// Get status filter
$status_filter = "";

if (isset($_GET["status"])) {
    $status_filter = $_GET["status"];
}


// Get all categories
$category_sql = "SELECT id, category_name
                 FROM categories
                 ORDER BY category_name ASC";

$category_result = mysqli_query($conn, $category_sql);


// Get food items
$sql = "SELECT
            food_items.*,
            categories.category_name

        FROM food_items

        INNER JOIN categories
            ON food_items.category_id = categories.id

        WHERE food_items.user_id = ?";


// Add search condition
if (!empty($search)) {

    $sql .= " AND food_items.food_name LIKE ?";
}


// Add category condition
if (!empty($category_filter)) {

    $sql .= " AND food_items.category_id = ?";
}


$sql .= " ORDER BY food_items.expiry_date ASC";


// Prepare statement
$stmt = mysqli_prepare($conn, $sql);


// Bind parameters dynamically
if (!empty($search) && !empty($category_filter)) {

    $search_param = "%" . $search . "%";

    mysqli_stmt_bind_param(
        $stmt,
        "isi",
        $user_id,
        $search_param,
        $category_filter
    );

} elseif (!empty($search)) {

    $search_param = "%" . $search . "%";

    mysqli_stmt_bind_param(
        $stmt,
        "is",
        $user_id,
        $search_param
    );

} elseif (!empty($category_filter)) {

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $user_id,
        $category_filter
    );

} else {

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $user_id
    );
}


mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>My Inventory - FoodSaver BD</title>

</head>

<body>

    <h1>My Food Inventory</h1>


    <a href="add_food.php">
        Add New Food
    </a>

    <br><br>


    <!-- =========================
         SEARCH AND FILTER FORM
    ========================== -->

    <form method="GET">

        <!-- Search -->

        <input
            type="text"
            name="search"
            placeholder="Search food..."
            value="<?php echo htmlspecialchars($search); ?>"
        >


        <!-- Category -->

        <select name="category">

            <option value="">
                All Categories
            </option>


            <?php while ($category = mysqli_fetch_assoc($category_result)): ?>

                <option
                    value="<?php echo $category["id"]; ?>"

                    <?php

                    if (
                        $category_filter
                        == $category["id"]
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


        <!-- Status -->

        <select name="status">

            <option value="">
                All Status
            </option>

            <option
                value="fresh"

                <?php

                if ($status_filter == "fresh") {
                    echo "selected";
                }

                ?>
            >

                Fresh

            </option>


            <option
                value="expiring"

                <?php

                if ($status_filter == "expiring") {
                    echo "selected";
                }

                ?>
            >

                Expiring Soon

            </option>


            <option
                value="expired"

                <?php

                if ($status_filter == "expired") {
                    echo "selected";
                }

                ?>
            >

                Expired

            </option>

        </select>


        <button type="submit">
            Search
        </button>


        <a href="view_food.php">
            Clear
        </a>

    </form>


    <br><br>


    <!-- =========================
         FOOD TABLE
    ========================== -->

    <table border="1" cellpadding="10">

        <tr>

            <th>Food Name</th>

            <th>Category</th>

            <th>Quantity</th>

            <th>Purchase Date</th>

            <th>Expiry Date</th>

            <th>Status</th>

            <th>Storage</th>

            <th>Actions</th>

        </tr>


        <?php if (mysqli_num_rows($result) > 0): ?>


            <?php while ($food = mysqli_fetch_assoc($result)): ?>


                <?php

                $today = new DateTime();

                $expiry_date = new DateTime(
                    $food["expiry_date"]
                );


                $difference = $today->diff(
                    $expiry_date
                );


                $days_remaining = (int)
                    $difference->format("%r%a");


                if ($days_remaining < 0) {

                    $status = "expired";

                } elseif ($days_remaining <= 3) {

                    $status = "expiring";

                } else {

                    $status = "fresh";

                }


                // Apply status filter

                if (
                    !empty($status_filter)
                    &&
                    $status_filter != $status
                ) {

                    continue;

                }

                ?>


                <tr>


                    <td>

                        <?php echo htmlspecialchars(
                            $food["food_name"]
                        ); ?>

                    </td>


                    <td>

                        <?php echo htmlspecialchars(
                            $food["category_name"]
                        ); ?>

                    </td>


                    <td>

                        <?php echo htmlspecialchars(
                            $food["quantity"]
                        ); ?>

                        <?php echo htmlspecialchars(
                            $food["unit"]
                        ); ?>

                    </td>


                    <td>

                        <?php echo $food["purchase_date"]; ?>

                    </td>


                    <td>

                        <?php echo $food["expiry_date"]; ?>

                    </td>


                    <td>


                        <?php if ($status == "expired"): ?>

                            <strong
                                style="color:red;"
                            >

                                Expired

                            </strong>


                            <br>

                            <?php echo abs(
                                $days_remaining
                            ); ?>

                            day(s) ago


                        <?php elseif (
                            $status == "expiring"
                        ): ?>

                            <strong
                                style="color:orange;"
                            >

                                Expiring Soon

                            </strong>


                            <br>

                            <?php echo $days_remaining; ?>

                            day(s) remaining


                        <?php else: ?>

                            <strong
                                style="color:green;"
                            >

                                Fresh

                            </strong>


                            <br>

                            <?php echo $days_remaining; ?>

                            day(s) remaining

                        <?php endif; ?>


                    </td>


                    <td>

                        <?php echo htmlspecialchars(
                            $food["storage_location"]
                        ); ?>

                    </td>


                    <td>

                        <a
                            href="edit_food.php?id=<?php echo $food["id"]; ?>"
                        >

                            Edit

                        </a>


                        |


                        <a
                            href="delete_food.php?id=<?php echo $food["id"]; ?>"

                            onclick="return confirm(
                                'Are you sure you want to delete this food?'
                            );"
                        >

                            Delete

                        </a>

                    </td>


                </tr>


            <?php endwhile; ?>


        <?php else: ?>


            <tr>

                <td colspan="8">

                    No food items found.

                </td>

            </tr>


        <?php endif; ?>


    </table>


    <br>


    <a href="../dashboard/index.php">

        Back to Dashboard

    </a>


</body>

</html>