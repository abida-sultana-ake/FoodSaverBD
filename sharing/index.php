<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

if (!isset($conn)) {
    if (isset($connection)) {
        $conn = $connection;
    } elseif (isset($db)) {
        $conn = $db;
    } elseif (isset($link)) {
        $conn = $link;
    }
}

$user_id = $_SESSION['user_id'];

/* Logged In User */
$user_name = "User";

$userQuery = mysqli_query($conn, "SELECT full_name FROM users WHERE id='$user_id'");

if($userQuery && mysqli_num_rows($userQuery)>0){

    $user=mysqli_fetch_assoc($userQuery);

    $user_name=$user['full_name'];

}

$page_title="Community Sharing";

include("../includes/header.php");
include("../includes/sidebar.php");
include("../includes/topbar.php");


$sql="SELECT

        sf.id,
        sf.food_id,
        sf.quantity AS shared_quantity,
        sf.description,
        sf.pickup_location,
        sf.contact_number,
        sf.status,
        sf.created_at,

        fi.food_name,
        fi.quantity AS inventory_quantity,
        fi.unit,
        fi.expiry_date,
        fi.image

FROM shared_food sf

INNER JOIN food_items fi

ON sf.food_id=fi.id

WHERE sf.user_id=?

ORDER BY sf.created_at DESC";

$stmt=mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param($stmt,"i",$user_id);

mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

?>

<div class="main-content">

<div class="page-title">

<h2>

<i class="fa-solid fa-hand-holding-heart"></i>

Community Sharing

</h2>

<a href="share_food.php" class="btn">

<i class="fa-solid fa-plus"></i>

Share Food

</a>

</div>

<div class="sharing-grid">

<?php

if(mysqli_num_rows($result)>0){

while($row=mysqli_fetch_assoc($result)){

?>
<div class="share-card">
    <?php if (!empty($row['image']) && file_exists("../uploads/" . $row['image'])) { ?>

    <div class="share-image">

        <img
            src="../uploads/<?php echo htmlspecialchars($row['image']); ?>"
            alt="<?php echo htmlspecialchars($row['food_name']); ?>"
        >

    </div>

<?php } else { ?>

    <div class="share-image no-share-image">

        <i class="fa-solid fa-utensils"></i>

        <span>No Image</span>

    </div>

<?php } ?>
<div class="share-header">

    <div>

        <h3><?php echo htmlspecialchars($row['food_name']); ?></h3>

        <small>
            Shared on
            <?php echo date("d M Y", strtotime($row['created_at'])); ?>
        </small>

    </div>

    <span class="badge
    <?php

    if($row['status']=="Available")
        echo " badge-success";

    elseif($row['status']=="Collected")
        echo " badge-warning";

    else
        echo " badge-danger";

    ?>">
        <?php echo htmlspecialchars($row['status']); ?>
    </span>

</div>

<div class="share-body">

<p>

<strong>Shared Quantity :</strong>

<?php echo $row['shared_quantity']." ".$row['unit']; ?>

</p>

<p>

<strong>Inventory Quantity :</strong>

<?php echo $row['inventory_quantity']." ".$row['unit']; ?>

</p>

<p>

<strong>Expiry Date :</strong>

<?php echo date("d M Y",strtotime($row['expiry_date'])); ?>

</p>

<p>

<strong>Pickup Location :</strong>

<?php echo htmlspecialchars($row['pickup_location']); ?>

</p>

<p>

<strong>Contact Number :</strong>

<?php echo htmlspecialchars($row['contact_number']); ?>

</p>

<p>

<strong>Description :</strong>

<br>

<?php echo nl2br(htmlspecialchars($row['description'])); ?>

</p>

</div>


<div class="share-actions">

<a href="edit_share.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">

<i class="fa-solid fa-pen"></i>

Edit

</a>

<a href="delete_share.php?id=<?php echo $row['id']; ?>"

class="btn btn-delete"

onclick="return confirm('Delete this shared food?');">

<i class="fa-solid fa-trash"></i>

Delete

</a>

</div>

</div>

<?php

}

}else{

?>

<div class="empty-card">

<i class="fa-solid fa-box-open empty-icon"></i>

<h2>No Shared Foods</h2>

<p>

You haven't shared any food yet.

</p>

<a href="share_food.php" class="btn">

<i class="fa-solid fa-plus"></i>

Share Food

</a>

</div>

<?php

}

?>

</div>

<?php

mysqli_stmt_close($stmt);

mysqli_close($conn);

include("../includes/footer.php");

?>