<?php

session_start();


if (!isset($_SESSION['user_id'])) {

    header("Location: ../auth/login.php");

    exit();

}


include("../config/db.php");


$user_id = $_SESSION['user_id'];

$page_title = "Edit Food";


/* Check Food ID */

if (!isset($_GET['id']) || empty($_GET['id'])) {

    header("Location: index.php");

    exit();

}


$food_id = intval($_GET['id']);


/* Get Existing Food */

$sql = "

SELECT *

FROM food_items

WHERE id = ?

AND user_id = ?

";


$stmt = mysqli_prepare(

    $conn,

    $sql

);


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


/* Load Categories */

$categories = mysqli_query(

    $conn,

    "SELECT id, category_name

     FROM categories

     ORDER BY category_name ASC"

);


/* Update Food */

if (isset($_POST['update_food'])) {


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


        $image = $food['image'];


        /* New Image Upload */

        if (

            isset($_FILES['image']) &&

            $_FILES['image']['error'] == 0

        ) {


            $uploadDir = "../uploads/";


            if (!is_dir($uploadDir)) {

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


        /* Update Query */

        $updateSql = "

        UPDATE food_items

        SET

            category_id = ?,

            food_name = ?,

            quantity = ?,

            unit = ?,

            purchase_date = ?,

            expiry_date = ?,

            storage_location = ?,

            image = ?,

            notes = ?

        WHERE id = ?

        AND user_id = ?

        ";


        $updateStmt = mysqli_prepare(

            $conn,

            $updateSql

        );


        mysqli_stmt_bind_param(

            $updateStmt,

            "isdssssssii",

            $category_id,

            $food_name,

            $quantity,

            $unit,

            $purchase_date,

            $expiry_date,

            $storage_location,

            $image,

            $notes,

            $food_id,

            $user_id

        );


        if (

            mysqli_stmt_execute(

                $updateStmt

            )

        ) {

            header(

                "Location: view_food.php?id="

                . $food_id

                . "&updated=1"

            );

            exit();

        }

        else {

            $error =

            "Failed to update food.";

        }

    }

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

            <i class="fa-solid fa-pen-to-square"></i>

            Edit Food

        </h2>


        <a

            href="view_food.php?id=<?php

            echo $food['id'];

            ?>"

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

                        value="<?php

                        echo htmlspecialchars(

                            $food['food_name']

                        );

                        ?>"

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

                                <?php

                                if (

                                    $cat['id']

                                    == $food['category_id']

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

                        value="<?php

                        echo htmlspecialchars(

                            $food['quantity']

                        );

                        ?>"

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

                        <option value="kg"

                            <?php

                            echo (

                                $food['unit']

                                == "kg"

                            )

                            ? "selected"

                            : "";

                            ?>

                        >

                            Kilogram (kg)

                        </option>


                        <option value="gram"

                            <?php

                            echo (

                                $food['unit']

                                == "gram"

                            )

                            ? "selected"

                            : "";

                            ?>

                        >

                            Gram

                        </option>


                        <option value="liter"

                            <?php

                            echo (

                                $food['unit']

                                == "liter"

                            )

                            ? "selected"

                            : "";

                            ?>

                        >

                            Liter

                        </option>


                        <option value="ml"

                            <?php

                            echo (

                                $food['unit']

                                == "ml"

                            )

                            ? "selected"

                            : "";

                            ?>

                        >

                            Milliliter (ml)

                        </option>


                        <option value="piece"

                            <?php

                            echo (

                                $food['unit']

                                == "piece"

                            )

                            ? "selected"

                            : "";

                            ?>

                        >

                            Piece

                        </option>


                        <option value="pack"

                            <?php

                            echo (

                                $food['unit']

                                == "pack"

                            )

                            ? "selected"

                            : "";

                            ?>

                        >

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

                        value="<?php

                        echo $food['purchase_date'];

                        ?>"

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

                        value="<?php

                        echo $food['expiry_date'];

                        ?>"

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

                    value="<?php

                    echo htmlspecialchars(

                        $food['storage_location']

                    );

                    ?>"

                    required

                >

            </div>


            <?php

            if (

                !empty($food['image']) &&

                file_exists(

                    "../uploads/"

                    . $food['image']

                )

            ) {

            ?>

                <div class="current-image">

                    <p>

                        Current Image:

                    </p>


                    <img

                        src="../uploads/<?php

                        echo htmlspecialchars(

                            $food['image']

                        );

                        ?>"

                        alt="Current Food Image"

                    >

                </div>

            <?php

            }

            ?>


            <div class="form-group">

                <label>

                    Replace Image

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

                ><?php

                echo htmlspecialchars(

                    $food['notes']

                );

                ?></textarea>

            </div>


            <button

                type="submit"

                name="update_food"

                class="btn"

            >

                <i

                    class="fa-solid fa-save"

                ></i>

                Update Food

            </button>


        </form>

    </div>

</div>


<?php

mysqli_stmt_close($stmt);

mysqli_close($conn);

include("../includes/footer.php");

?>