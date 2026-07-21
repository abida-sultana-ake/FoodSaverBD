<?php

session_start();

if (!isset($_SESSION['user_id'])) {

    header("Location: ../auth/login.php");

    exit();

}

include("../config/db.php");

$user_id = $_SESSION['user_id'];

$page_title = "Dashboard";


/* Logged In User */

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

    $user = mysqli_fetch_assoc($userQuery);

    $user_name = $user['full_name'];

}


/* Total Foods */

$total = mysqli_fetch_assoc(

    mysqli_query(

        $conn,

        "SELECT COUNT(*) AS total

         FROM food_items

         WHERE user_id='$user_id'"

    )

)['total'];


/* Fresh Foods */

$fresh = mysqli_fetch_assoc(

    mysqli_query(

        $conn,

        "SELECT COUNT(*) AS total

         FROM food_items

         WHERE user_id='$user_id'

         AND expiry_date >

         DATE_ADD(CURDATE(), INTERVAL 3 DAY)"

    )

)['total'];


/* Expiring Soon */

$expiring = mysqli_fetch_assoc(

    mysqli_query(

        $conn,

        "SELECT COUNT(*) AS total

         FROM food_items

         WHERE user_id='$user_id'

         AND expiry_date BETWEEN CURDATE()

         AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)"

    )

)['total'];


/* Expired Foods */

$expired = mysqli_fetch_assoc(

    mysqli_query(

        $conn,

        "SELECT COUNT(*) AS total

         FROM food_items

         WHERE user_id='$user_id'

         AND expiry_date < CURDATE()"

    )

)['total'];


/* Shared Foods */

$shared = mysqli_fetch_assoc(

    mysqli_query(

        $conn,

        "SELECT COUNT(*) AS total

         FROM shared_food

         WHERE user_id='$user_id'"

    )

)['total'];


/* Recent Foods */

$recentFoods = mysqli_query(

    $conn,

    "SELECT

        food_name,

        quantity,

        unit,

        expiry_date

     FROM food_items

     WHERE user_id='$user_id'

     ORDER BY created_at DESC

     LIMIT 5"

);


/* Expiring Soon Foods */

$expiringFoods = mysqli_query(

    $conn,

    "SELECT

        food_name,

        quantity,

        unit,

        expiry_date

     FROM food_items

     WHERE user_id='$user_id'

     AND expiry_date >= CURDATE()

     AND expiry_date <=

     DATE_ADD(CURDATE(), INTERVAL 3 DAY)

     ORDER BY expiry_date ASC

     LIMIT 5"

);
/* Expiry Alerts */

$expiryAlerts = mysqli_query(

    $conn,

    "SELECT

        id,

        food_name,

        quantity,

        unit,

        expiry_date

     FROM food_items

     WHERE user_id='$user_id'

     AND expiry_date <=

     DATE_ADD(CURDATE(), INTERVAL 3 DAY)

     ORDER BY expiry_date ASC

     LIMIT 10"

);

/* Recently Shared Foods */

$recentShared = mysqli_query(

    $conn,

    "SELECT

        sf.quantity,

        sf.status,

        sf.created_at,

        fi.food_name,

        fi.unit

     FROM shared_food sf

     INNER JOIN food_items fi

     ON sf.food_id = fi.id

     WHERE sf.user_id='$user_id'

     ORDER BY sf.created_at DESC

     LIMIT 5"

);


include("../includes/header.php");

include("../includes/sidebar.php");

include("../includes/topbar.php");

?>


<div class="main-content">


    <!-- Statistics -->

    <div class="stats">


        <div class="stat-card">

            <h3>Total Foods</h3>

            <h1>

                <?php echo $total; ?>

            </h1>

        </div>


        <div class="stat-card">

            <h3>Fresh Foods</h3>

            <h1>

                <?php echo $fresh; ?>

            </h1>

        </div>


        <div class="stat-card">

            <h3>Expiring Soon</h3>

            <h1>

                <?php echo $expiring; ?>

            </h1>

        </div>


        <div class="stat-card">

            <h3>Expired Foods</h3>

            <h1>

                <?php echo $expired; ?>

            </h1>

        </div>


        <div class="stat-card">

            <h3>Shared Foods</h3>

            <h1>

                <?php echo $shared; ?>

            </h1>

        </div>


    </div>


    <!-- Dashboard Grid -->

    <div class="dashboard-grid">


        <!-- Recent Foods -->

        <div class="dashboard-card">


            <div class="card-header">

                <h3>

                    <i class="fa-solid fa-clock-rotate-left"></i>

                    Recent Foods

                </h3>


    

            </div>


            <table class="table">


                <thead>

                    <tr>

                        <th>Food</th>

                        <th>Quantity</th>

                        <th>Expiry</th>

                    </tr>

                </thead>


                <tbody>


                <?php


                if (

                    $recentFoods &&

                    mysqli_num_rows(

                        $recentFoods

                    ) > 0

                ) {


                    while (

                        $food =

                        mysqli_fetch_assoc(

                            $recentFoods

                        )

                    ) {


                ?>


                    <tr>


                        <td>

                            <?php

                            echo htmlspecialchars(

                                $food['food_name']

                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo $food['quantity']

                                . " "

                                . $food['unit'];

                            ?>

                        </td>


                        <td>

                            <?php

                            echo $food['expiry_date'];

                            ?>

                        </td>


                    </tr>


                <?php


                    }


                }

                else {


                ?>


                    <tr>

                        <td colspan="3">

                            No food found.

                        </td>

                    </tr>


                <?php

                }

                ?>


                </tbody>


            </table>


        </div>


        <!-- Expiring Soon -->

        <div class="dashboard-card">


            <div class="card-header">

                <h3>

                    <i class="fa-solid fa-triangle-exclamation"></i>

                    Expiring Soon

                </h3>


               

            </div>


            <ul class="expiry-list">


            <?php


            if (

                $expiringFoods &&

                mysqli_num_rows(

                    $expiringFoods

                ) > 0

            ) {


                while (

                    $food =

                    mysqli_fetch_assoc(

                        $expiringFoods

                    )

                ) {


            ?>


                <li>


                    <strong>

                        <?php

                        echo htmlspecialchars(

                            $food['food_name']

                        );

                        ?>

                    </strong>


                    <span>

                        Expires:

                        <?php

                        echo $food['expiry_date'];

                        ?>

                    </span>


                </li>


            <?php


                }


            }

            else {


            ?>


                <li>

                    No foods expiring soon.

                </li>


            <?php

            }

            ?>


            </ul>


        </div>


        <!-- Recently Shared -->

        <div class="dashboard-card">


            <div class="card-header">

                <h3>

                    <i class="fa-solid fa-hand-holding-heart"></i>

                    Recently Shared

                </h3>


               

            </div>


            <ul class="shared-list">


            <?php


            if (

                $recentShared &&

                mysqli_num_rows(

                    $recentShared

                ) > 0

            ) {


                while (

                    $share =

                    mysqli_fetch_assoc(

                        $recentShared

                    )

                ) {


            ?>


                <li>


                    <strong>

                        <?php

                        echo htmlspecialchars(

                            $share['food_name']

                        );

                        ?>

                    </strong>


                    <span>

                        <?php

                        echo $share['quantity']

                            . " "

                            . $share['unit'];

                        ?>

                        -

                        <?php

                        echo htmlspecialchars(

                            $share['status']

                        );

                        ?>

                    </span>


                </li>


            <?php


                }


            }

            else {


            ?>


                <li>

                    No shared food yet.

                </li>


            <?php

            }

            ?>


            </ul>


        </div>


    </div>


</div>


<?php


mysqli_close($conn);


include("../includes/footer.php");


?>