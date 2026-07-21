<?php

session_start();

if (!isset($_SESSION['user_id'])) {

    header("Location: ../auth/login.php");

    exit();

}


include("../config/db.php");


$user_id = $_SESSION['user_id'];

$page_title = "Food Inventory";


/* Logged In User */

$user_name = "User";


$userQuery = mysqli_query(

    $conn,

    "SELECT full_name

     FROM users

     WHERE id='$user_id'"

);


if ($userQuery && mysqli_num_rows($userQuery) > 0) {

    $user = mysqli_fetch_assoc($userQuery);

    $user_name = $user['full_name'];

}


/* Search */

$search = "";

if (isset($_GET['search'])) {

    $search = trim($_GET['search']);

}


/* Category Filter */

$category = "";

if (isset($_GET['category'])) {

    $category = $_GET['category'];

}


/* Base Query */

$sql = "

SELECT

    fi.id,

    fi.food_name,

    fi.quantity,

    fi.unit,

    fi.purchase_date,

    fi.expiry_date,

    fi.storage_location,

    fi.image,

    c.category_name

FROM food_items fi

LEFT JOIN categories c

ON fi.category_id = c.id

WHERE fi.user_id = ?

";


/* Search Condition */

if (!empty($search)) {

    $sql .= "

    AND fi.food_name LIKE ?

    ";

}


/* Category Condition */

if (!empty($category)) {

    $sql .= "

    AND fi.category_id = ?

    ";

}


$sql .= "

ORDER BY fi.expiry_date ASC

";


$stmt = mysqli_prepare($conn, $sql);


if (!empty($search) && !empty($category)) {

    $searchTerm = "%" . $search . "%";

    mysqli_stmt_bind_param(

        $stmt,

        "isi",

        $user_id,

        $searchTerm,

        $category

    );

}

elseif (!empty($search)) {

    $searchTerm = "%" . $search . "%";

    mysqli_stmt_bind_param(

        $stmt,

        "is",

        $user_id,

        $searchTerm

    );

}

elseif (!empty($category)) {

    mysqli_stmt_bind_param(

        $stmt,

        "ii",

        $user_id,

        $category

    );

}

else {

    mysqli_stmt_bind_param(

        $stmt,

        "i",

        $user_id

    );

}


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);


/* Load Categories */

$categories = mysqli_query(

    $conn,

    "SELECT id, category_name

     FROM categories

     ORDER BY category_name ASC"

);


include("../includes/header.php");

include("../includes/sidebar.php");

include("../includes/topbar.php");

?>

<div class="main-content">

    <div class="page-title">

        <h2>

            <i class="fa-solid fa-boxes-stacked"></i>

            Food Inventory

        </h2>

        <a href="add_food.php" class="btn">

            <i class="fa-solid fa-plus"></i>

            Add Food

        </a>

    </div>
        <!-- Search and Filter -->

    <div class="filter-card">

        <form method="GET" class="filter-form">

            <div class="form-group">

                <label>Search Food</label>

                <input

                    type="text"

                    name="search"

                    placeholder="Search food name..."

                    value="<?php echo htmlspecialchars($search); ?>"

                >

            </div>


            <div class="form-group">

                <label>Category</label>

                <select name="category">

                    <option value="">All Categories</option>


                    <?php

                    if (

                        $categories &&

                        mysqli_num_rows($categories) > 0

                    ) {

                        while (

                            $cat = mysqli_fetch_assoc($categories)

                        ) {

                    ?>

                        <option

                            value="<?php echo $cat['id']; ?>"

                            <?php

                            if (

                                $category == $cat['id']

                            ) {

                                echo "selected";

                            }

                            ?>

                        >

                            <?php

                            echo htmlspecialchars(

                                $cat['category_name']

                            );

                            ?>

                        </option>

                    <?php

                        }

                    }

                    ?>

                </select>

            </div>


            <div class="filter-actions">

                <button

                    type="submit"

                    class="btn"

                >

                    <i class="fa-solid fa-filter"></i>

                    Filter

                </button>


                <a

                    href="index.php"

                    class="btn btn-secondary"

                >

                    <i class="fa-solid fa-rotate-left"></i>

                    Reset

                </a>

            </div>

        </form>

    </div>


    <!-- Food Inventory Grid -->

    <div class="inventory-grid">

        <?php

        if (

            $result &&

            mysqli_num_rows($result) > 0

        ) {

            while (

                $food = mysqli_fetch_assoc($result)

            ) {


                $today = date("Y-m-d");

                $threeDaysLater = date(

                    "Y-m-d",

                    strtotime("+3 days")

                );


                if (

                    $food['expiry_date'] < $today

                ) {

                    $expiryClass = "expired";

                    $expiryText = "Expired";

                }

                elseif (

                    $food['expiry_date']

                    <= $threeDaysLater

                ) {

                    $expiryClass = "expiring";

                    $expiryText = "Expiring Soon";

                }

                else {

                    $expiryClass = "fresh";

                    $expiryText = "Fresh";

                }

        ?>

            <div class="food-card">


                <div class="food-card-header">

                    <h3>

                        <?php

                        echo htmlspecialchars(

                            $food['food_name']

                        );

                        ?>

                    </h3>


                    <span

                        class="expiry-badge

                        <?php

                        echo $expiryClass;

                        ?>"

                    >

                        <?php

                        echo $expiryText;

                        ?>

                    </span>

                </div>


                <div class="food-card-body">

                    <p>

                        <strong>Quantity:</strong>

                        <?php

                        echo htmlspecialchars(

                            $food['quantity']

                        );

                        ?>

                        <?php

                        echo htmlspecialchars(

                            $food['unit']

                        );

                        ?>

                    </p>


                    <p>

                        <strong>Category:</strong>

                        <?php

                        echo htmlspecialchars(

                            $food['category_name']

                            ?? "Uncategorized"

                        );

                        ?>

                    </p>


                    <p>

                        <strong>Expiry Date:</strong>

                        <?php

                        echo htmlspecialchars(

                            $food['expiry_date']

                        );

                        ?>

                    </p>


                    <p>

                        <strong>Storage:</strong>

                        <?php

                        echo htmlspecialchars(

                            $food['storage_location']

                        );

                        ?>

                    </p>

                </div>


                <div class="food-card-actions">


                    <a

                        href="view_food.php?id=<?php

                        echo $food['id'];

                        ?>"

                        class="btn btn-view"

                    >

                        <i class="fa-solid fa-eye"></i>

                        View

                    </a>


                    <a

                        href="edit_food.php?id=<?php

                        echo $food['id'];

                        ?>"

                        class="btn btn-edit"

                    >

                        <i class="fa-solid fa-pen"></i>

                        Edit

                    </a>


                    <a

                        href="delete_food.php?id=<?php

                        echo $food['id'];

                        ?>"

                        class="btn btn-delete"

                        onclick="return confirm(

                            'Are you sure you want to delete this food?'

                        );"

                    >

                        <i class="fa-solid fa-trash"></i>

                        Delete

                    </a>


                </div>


            </div>

        <?php

            }

        }

        else {

        ?>

            <div class="empty-card">

                <i

                    class="fa-solid fa-box-open empty-icon"

                ></i>


                <h2>

                    No Food Found

                </h2>


                <p>

                    Your inventory is currently empty.

                </p>


                <a

                    href="add_food.php"

                    class="btn"

                >

                    <i class="fa-solid fa-plus"></i>

                    Add Your First Food

                </a>

            </div>

        <?php

        }

        ?>

    </div>

</div>


<?php

mysqli_stmt_close($stmt);

mysqli_close($conn);


include("../includes/footer.php");

?>