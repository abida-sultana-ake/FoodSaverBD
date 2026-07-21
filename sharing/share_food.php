<?php

session_start();

if (!isset($_SESSION['user_id'])) {

    header("Location: ../auth/login.php");
    exit();

}


include("../config/db.php");


$user_id = $_SESSION['user_id'];

$page_title = "Share Food";


/* Get User Name */

$user_name = "User";

$userQuery = mysqli_query($conn,
"SELECT full_name FROM users WHERE id='$user_id'");


if($userQuery && mysqli_num_rows($userQuery)>0){

    $user = mysqli_fetch_assoc($userQuery);

    $user_name = $user['full_name'];

}


/* Insert Shared Food */

if(isset($_POST['share_food'])){


    $food_id = $_POST['food_id'];

    $quantity = $_POST['quantity'];

    $description = $_POST['description'];

    $pickup_location = $_POST['pickup_location'];

    $contact_number = $_POST['contact_number'];

    $status = "Available";


    if(
        empty($food_id) ||
        empty($quantity) ||
        empty($pickup_location) ||
        empty($contact_number)
    ){

        $error = "Please fill all required fields.";

    }

    else{


        $sql = "INSERT INTO shared_food

        (
            food_id,
            user_id,
            quantity,
            description,
            pickup_location,
            contact_number,
            status
        )

        VALUES

        (?,?,?,?,?,?,?)";


        $stmt = mysqli_prepare($conn,$sql);


        mysqli_stmt_bind_param(
            $stmt,
            "iidssss",
            $food_id,
            $user_id,
            $quantity,
            $description,
            $pickup_location,
            $contact_number,
            $status
        );


        if(mysqli_stmt_execute($stmt)){


            $success = "Food shared successfully!";


        }

        else{


            $error = "Something went wrong!";


        }


    }

}


/* Load Available Foods */

$foods = mysqli_query($conn,

"SELECT id, food_name, quantity, unit, expiry_date

FROM food_items

WHERE user_id='$user_id'

AND expiry_date >= CURDATE()

ORDER BY food_name ASC"

);



include("../includes/header.php");

include("../includes/sidebar.php");

include("../includes/topbar.php");


?>


<div class="main-content">


<div class="page-title">


<h2>

<i class="fa-solid fa-hand-holding-heart"></i>

Share Food

</h2>


<a href="index.php" class="btn">

<i class="fa-solid fa-arrow-left"></i>

Back

</a>


</div>
<?php

if(isset($success)){

?>

<div class="alert success">

    <?php echo $success; ?>

</div>

<?php

}


if(isset($error)){

?>

<div class="alert error">

    <?php echo $error; ?>

</div>

<?php

}

?>


<div class="form-card">


<form method="POST">


<div class="form-group">


<label>Select Food</label>


<select name="food_id" required>


<option value="">-- Select Food --</option>


<?php


if(mysqli_num_rows($foods)>0){


while($food=mysqli_fetch_assoc($foods)){


?>


<option value="<?php echo $food['id']; ?>">


<?php

echo htmlspecialchars($food['food_name'])
." (".$food['quantity']." ".$food['unit'].")";

?>


- Expiry:

<?php echo $food['expiry_date']; ?>


</option>


<?php


}


}


?>


</select>


</div>




<div class="form-group">


<label>Quantity</label>


<input

type="number"

step="0.01"

name="quantity"

placeholder="Enter sharing quantity"

required>


</div>





<div class="form-group">


<label>Pickup Location</label>


<input

type="text"

name="pickup_location"

placeholder="Enter pickup location"

required>


</div>





<div class="form-group">


<label>Contact Number</label>


<input

type="text"

name="contact_number"

placeholder="Enter contact number"

required>


</div>





<div class="form-group">


<label>Description</label>


<textarea

name="description"

rows="4"

placeholder="Write food details...">

</textarea>


</div>





<button

type="submit"

name="share_food"

class="btn">


<i class="fa-solid fa-share"></i>


Share Food


</button>



</form>


</div>


</div>


<?php


include("../includes/footer.php");


?>
