<?php

session_start();


/* Login Check */

if(!isset($_SESSION['user_id'])){

    header("Location: ../auth/login.php");

    exit();

}


include("../config/db.php");


$user_id = $_SESSION['user_id'];


/* Check Share ID */

if(!isset($_GET['id']) || empty($_GET['id'])){

    header("Location: index.php");

    exit();

}


$share_id = intval($_GET['id']);


/* Get Shared Food */

$sql = "SELECT

            sf.id,

            sf.food_id,

            sf.quantity,

            sf.description,

            sf.pickup_location,

            sf.contact_number,

            sf.status,

            fi.food_name,

            fi.unit,

            fi.expiry_date

        FROM shared_food sf

        INNER JOIN food_items fi

        ON sf.food_id = fi.id

        WHERE sf.id = ?

        AND sf.user_id = ?";


$stmt = mysqli_prepare($conn,$sql);


mysqli_stmt_bind_param(

    $stmt,

    "ii",

    $share_id,

    $user_id

);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);


if(mysqli_num_rows($result) == 0){

    header("Location: index.php");

    exit();

}


$share = mysqli_fetch_assoc($result);


/* Update Shared Food */

if(isset($_POST['update_share'])){


    $quantity = $_POST['quantity'];

    $description = $_POST['description'];

    $pickup_location = $_POST['pickup_location'];

    $contact_number = $_POST['contact_number'];

    $status = $_POST['status'];


    if(

        empty($quantity) ||

        empty($pickup_location) ||

        empty($contact_number)

    ){

        $error = "Please fill all required fields.";

    }


    else{


        $update_sql = "UPDATE shared_food

        SET

            quantity = ?,

            description = ?,

            pickup_location = ?,

            contact_number = ?,

            status = ?

        WHERE id = ?

        AND user_id = ?";


        $update_stmt = mysqli_prepare(

            $conn,

            $update_sql

        );


        mysqli_stmt_bind_param(

            $update_stmt,

            "dssssii",

            $quantity,

            $description,

            $pickup_location,

            $contact_number,

            $status,

            $share_id,

            $user_id

        );


        if(mysqli_stmt_execute($update_stmt)){

            header(

                "Location: index.php?updated=1"

            );

            exit();

        }

        else{

            $error = "Update failed.";

        }

    }

}


$page_title = "Edit Shared Food";


include("../includes/header.php");

include("../includes/sidebar.php");

include("../includes/topbar.php");

?>
<div class="main-content">

    <div class="page-title">

        <h2>

            <i class="fa-solid fa-pen-to-square"></i>

            Edit Shared Food

        </h2>

        <a href="index.php" class="btn">

            <i class="fa-solid fa-arrow-left"></i>

            Back

        </a>

    </div>


    <?php if(isset($error)){ ?>

        <div class="alert error">

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php } ?>


    <div class="form-card">

        <div class="food-info">

            <h3>

                <?php echo htmlspecialchars($share['food_name']); ?>

            </h3>

            <p>

                Available Inventory:

                <?php

                echo htmlspecialchars($share['unit']);

                ?>

            </p>

            <p>

                Expiry Date:

                <?php echo htmlspecialchars($share['expiry_date']); ?>

            </p>

        </div>


        <form method="POST">


            <div class="form-group">

                <label>Sharing Quantity</label>

                <input

                    type="number"

                    name="quantity"

                    step="0.01"

                    min="0.01"

                    value="<?php echo htmlspecialchars($share['quantity']); ?>"

                    required

                >

            </div>


            <div class="form-group">

                <label>Pickup Location</label>

                <input

                    type="text"

                    name="pickup_location"

                    value="<?php echo htmlspecialchars($share['pickup_location']); ?>"

                    required

                >

            </div>


            <div class="form-group">

                <label>Contact Number</label>

                <input

                    type="text"

                    name="contact_number"

                    value="<?php echo htmlspecialchars($share['contact_number']); ?>"

                    required

                >

            </div>


            <div class="form-group">

                <label>Status</label>

                <select name="status" required>

                    <option

                        value="Available"

                        <?php

                        if($share['status']=="Available")

                            echo "selected";

                        ?>

                    >

                        Available

                    </option>


                    <option

                        value="Collected"

                        <?php

                        if($share['status']=="Collected")

                            echo "selected";

                        ?>

                    >

                        Collected

                    </option>


                    <option

                        value="Expired"

                        <?php

                        if($share['status']=="Expired")

                            echo "selected";

                        ?>

                    >

                        Expired

                    </option>

                </select>

            </div>


            <div class="form-group">

                <label>Description</label>

                <textarea

                    name="description"

                    rows="5"

                    placeholder="Write food details..."

                ><?php echo htmlspecialchars($share['description']); ?></textarea>

            </div>


            <button

                type="submit"

                name="update_share"

                class="btn"

            >

                <i class="fa-solid fa-save"></i>

                Update Shared Food

            </button>


        </form>

    </div>

</div>


<?php

mysqli_stmt_close($stmt);

mysqli_close($conn);

include("../includes/footer.php");

?>