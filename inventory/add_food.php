<?php

session_start();


/* Login Check */

if (!isset($_SESSION['user_id'])) {

    header("Location: ../auth/login.php");

    exit();

}


include("../config/db.php");


$user_id = $_SESSION['user_id'];

$page_title = "Add Food";


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


/* Load Categories */

$categories = mysqli_query(

    $conn,

    "SELECT id, category_name

     FROM categories

     ORDER BY category_name ASC"

);


/* Add Food */

if (isset($_POST['add_food'])) {


    $category_id = $_POST['category_id'];

    $food_name = trim($_POST['food_name']);

    $quantity = $_POST['quantity'];

    $unit = $_POST['unit'];

    $purchase_date = $_POST['purchase_date'];

    $expiry_date = $_POST['expiry_date'];

    $storage_location = trim(

        $_POST['storage_location']

    );

    $notes = trim($_POST['notes']);


    /* Validation */

    if (

        empty($category_id) ||

        empty($food_name) ||

        empty($quantity) ||

        empty($unit) ||

        empty($purchase_date) ||

        empty($expiry_date) ||

        empty($storage_location)

    ) {

        $error = "Please fill all required fields.";

    }

    elseif (

        $expiry_date < $purchase_date

    ) {

        $error =

        "Expiry date cannot be before purchase date.";

    }

    else {


        /* Image Upload */

        $image = NULL;


        if (

            isset($_FILES['image']) &&

            $_FILES['image']['error']

            == 0

        ) {


            $uploadDir = "../uploads/";


            if (

                !is_dir($uploadDir)

            ) {

                mkdir(

                    $uploadDir,

                    0777,

                    true

                );

            }


            $fileName = time()

                . "_"

                . basename(

                    $_FILES['image']['name']

                );


            $targetFile =

                $uploadDir . $fileName;


            $fileType = strtolower(

                pathinfo(

                    $targetFile,

                    PATHINFO_EXTENSION

                )

            );


            $allowedTypes = [

                "jpg",

                "jpeg",

                "png",

                "webp"

            ];


            if (

                in_array(

                    $fileType,

                    $allowedTypes

                )

            ) {


                if (

                    move_uploaded_file(

                        $_FILES['image']['tmp_name'],

                        $targetFile

                    )

                ) {

                    $image = $fileName;

                }

            }

        }


        /* Insert Food */

        $sql = "

        INSERT INTO food_items

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

        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)

        ";


        $stmt = mysqli_prepare(

            $conn,

            $sql

        );


        mysqli_stmt_bind_param(

            $stmt,

            "iis dssssss",

            $user_id,

            $category_id,

            $food_name,

            $quantity,

            $unit,

            $purchase_date,

            $expiry_date,

            $storage_location,

            $image,

            $notes

        );


        if (

            mysqli_stmt_execute($stmt)

        ) {


            header(

                "Location: index.php?added=1"

            );

            exit();


        }

        else {


            $error =

            "Failed to add food.";

        }

    }

}


/* Header */

include("../includes/header.php");

include("../includes/sidebar.php");

include("../includes/topbar.php");

?>
<div class="main-content">

    <div class="page-title">

        <h2>

            <i class="fa-solid fa-plus-circle"></i>

            Add Food

        </h2>


        <a

            href="index.php"

            class="btn"

        >

            <i class="fa-solid fa-arrow-left"></i>

            Back

        </a>

    </div>


    <?php if (isset($error)) { ?>

        <div class="alert error">

            <?php

            echo htmlspecialchars($error);

            ?>

        </div>

    <?php } ?>


    <div class="form-card">

        <form

            method="POST"

            enctype="multipart/form-data"

        >


            <div class="form-row">


                <div class="form-group">

                    <label>

                        Food Name

                    </label>


                    <input

                        type="text"

                        name="food_name"

                        placeholder="Enter food name"

                        required

                    >

                </div>


                <div class="form-group">

                    <label>

                        Category

                    </label>


                    <select

                        name="category_id"

                        required

                    >

                        <option value="">

                            Select Category

                        </option>


                        <?php

                        if (

                            $categories &&

                            mysqli_num_rows(

                                $categories

                            ) > 0

                        ) {


                            while (

                                $cat =

                                mysqli_fetch_assoc(

                                    $categories

                                )

                            ) {

                        ?>

                            <option

                                value="<?php

                                echo $cat['id'];

                                ?>"

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


            </div>


            <div class="form-row">


                <div class="form-group">

                    <label>

                        Quantity

                    </label>


                    <input

                        type="number"

                        name="quantity"

                        step="0.01"

                        min="0.01"

                        placeholder="e.g. 2.5"

                        required

                    >

                </div>


                <div class="form-group">

                    <label>

                        Unit

                    </label>


                    <select

                        name="unit"

                        required

                    >

                        <option value="">

                            Select Unit

                        </option>


                        <option value="kg">

                            Kilogram (kg)

                        </option>


                        <option value="gram">

                            Gram

                        </option>


                        <option value="liter">

                            Liter

                        </option>


                        <option value="ml">

                            Milliliter (ml)

                        </option>


                        <option value="piece">

                            Piece

                        </option>


                        <option value="pack">

                            Pack

                        </option>


                    </select>

                </div>


            </div>


            <div class="form-row">


                <div class="form-group">

                    <label>

                        Purchase Date

                    </label>


                    <input

                        type="date"

                        name="purchase_date"

                        required

                    >

                </div>


                <div class="form-group">

                    <label>

                        Expiry Date

                    </label>


                    <input

                        type="date"

                        name="expiry_date"

                        required

                    >

                </div>


            </div>


            <div class="form-group">

                <label>

                    Storage Location

                </label>


                <input

                    type="text"

                    name="storage_location"

                    placeholder="e.g. Refrigerator"

                    required

                >

            </div>


            <div class="form-group">

                <label>

                    Food Image

                </label>


                <input

                    type="file"

                    name="image"

                    accept="image/jpeg,image/png,image/webp"

                >

            </div>


            <div class="form-group">

                <label>

                    Notes

                </label>


                <textarea

                    name="notes"

                    rows="5"

                    placeholder="Add additional notes..."

                ></textarea>

            </div>


            <button

                type="submit"

                name="add_food"

                class="btn"

            >

                <i class="fa-solid fa-plus"></i>

                Add Food

            </button>


        </form>

    </div>

</div>


<?php

mysqli_close($conn);

include("../includes/footer.php");

?>