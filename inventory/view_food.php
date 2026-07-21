<?php

session_start();

if (!isset($_SESSION['user_id'])) {

    header("Location: ../auth/login.php");

    exit();

}

include("../config/db.php");

$user_id = $_SESSION['user_id'];

$page_title = "Food Details";


/* Check Food ID */

if (!isset($_GET['id']) || empty($_GET['id'])) {

    header("Location: index.php");

    exit();

}

$food_id = intval($_GET['id']);


/* Get Food Details */

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

    fi.notes,

    fi.created_at,

    fi.updated_at,

    c.category_name

FROM food_items fi

LEFT JOIN categories c

ON fi.category_id = c.id

WHERE fi.id = ?

AND fi.user_id = ?

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


/* Food Not Found */

if (mysqli_num_rows($result) == 0) {

    header("Location: index.php");

    exit();

}


$food = mysqli_fetch_assoc($result);


/* Expiry Status */

$today = date("Y-m-d");

$threeDaysLater = date(

    "Y-m-d",

    strtotime("+3 days")

);


if ($food['expiry_date'] < $today) {

    $expiryClass = "expired";

    $expiryText = "Expired";

}

elseif (

    $food['expiry_date'] <= $threeDaysLater

) {

    $expiryClass = "expiring";

    $expiryText = "Expiring Soon";

}

else {

    $expiryClass = "fresh";

    $expiryText = "Fresh";

}


/* User Name */

$user_name = "User";

$userQuery = mysqli_query(

    $conn,

    "SELECT full_name

     FROM users

     WHERE id='$user_id'"

);

if (

    $userQuery &&

    mysqli_num_rows($userQuery) > 0

) {

    $user = mysqli_fetch_assoc(

        $userQuery

    );

    $user_name = $user['full_name'];

}


include("../includes/header.php");

include("../includes/sidebar.php");

include("../includes/topbar.php");

?>
<div class="main-content">

    <div class="page-title">

        <h2>

            <i class="fa-solid fa-circle-info"></i>

            Food Details

        </h2>


        <a

            href="index.php"

            class="btn"

        >

            <i class="fa-solid fa-arrow-left"></i>

            Back to Inventory

        </a>

    </div>


    <div class="food-details-card">


        <div class="food-details-image">


            <?php

            if (

                !empty($food['image']) &&

                file_exists(

                    "../uploads/" .

                    $food['image']

                )

            ) {

            ?>

                <img

                    src="../uploads/<?php

                    echo htmlspecialchars(

                        $food['image']

                    );

                    ?>"

                    alt="<?php

                    echo htmlspecialchars(

                        $food['food_name']

                    );

                    ?>"

                >

            <?php

            }

            else {

            ?>

                <div class="no-image">

                    <i

                        class="fa-solid fa-image"

                    ></i>


                    <p>

                        No Image Available

                    </p>

                </div>

            <?php

            }

            ?>

        </div>


        <div class="food-details-content">


            <div class="food-details-header">


                <h1>

                    <?php

                    echo htmlspecialchars(

                        $food['food_name']

                    );

                    ?>

                </h1>


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


            <div class="details-grid">


                <div class="detail-item">

                    <span>

                        <i

                            class="fa-solid fa-layer-group"

                        ></i>

                        Category

                    </span>


                    <strong>

                        <?php

                        echo htmlspecialchars(

                            $food['category_name']

                            ?? "Uncategorized"

                        );

                        ?>

                    </strong>

                </div>


                <div class="detail-item">

                    <span>

                        <i

                            class="fa-solid fa-weight-hanging"

                        ></i>

                        Quantity

                    </span>


                    <strong>

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

                    </strong>

                </div>


                <div class="detail-item">

                    <span>

                        <i

                            class="fa-solid fa-cart-shopping"

                        ></i>

                        Purchase Date

                    </span>


                    <strong>

                        <?php

                        echo htmlspecialchars(

                            $food['purchase_date']

                        );

                        ?>

                    </strong>

                </div>


                <div class="detail-item">

                    <span>

                        <i

                            class="fa-solid fa-calendar-xmark"

                        ></i>

                        Expiry Date

                    </span>


                    <strong>

                        <?php

                        echo htmlspecialchars(

                            $food['expiry_date']

                        );

                        ?>

                    </strong>

                </div>


                <div class="detail-item">

                    <span>

                        <i

                            class="fa-solid fa-location-dot"

                        ></i>

                        Storage Location

                    </span>


                    <strong>

                        <?php

                        echo htmlspecialchars(

                            $food['storage_location']

                        );

                        ?>

                    </strong>

                </div>


            </div>


            <div class="notes-section">


                <h3>

                    <i

                        class="fa-solid fa-note-sticky"

                    ></i>

                    Notes

                </h3>


                <p>

                    <?php

                    if (

                        !empty($food['notes'])

                    ) {

                        echo nl2br(

                            htmlspecialchars(

                                $food['notes']

                            )

                        );

                    }

                    else {

                        echo "No notes available.";

                    }

                    ?>

                </p>

            </div>


            <div class="food-details-actions">


                <a

                    href="edit_food.php?id=<?php

                    echo $food['id'];

                    ?>"

                    class="btn btn-edit"

                >

                    <i

                        class="fa-solid fa-pen"

                    ></i>

                    Edit Food

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

                    <i

                        class="fa-solid fa-trash"

                    ></i>

                    Delete Food

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