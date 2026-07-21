<?php

if (!isset($page_title)) {
    $page_title = "Dashboard";
}

if (!isset($user_name)) {

    if (isset($_SESSION['user_id'])) {

        $uid = $_SESSION['user_id'];

        $query = mysqli_query($conn, "SELECT full_name FROM users WHERE id='$uid'");

        if ($query && mysqli_num_rows($query) > 0) {

            $user = mysqli_fetch_assoc($query);
            $user_name = $user['full_name'];

        } else {

            $user_name = "User";
        }

    } else {

        $user_name = "Guest";
    }
}

?>

<div class="topbar">

    <div class="top-left">

        <h2><?php echo $page_title; ?></h2>

        <p>
            Welcome back,
            <strong><?php echo htmlspecialchars($user_name); ?></strong>
        </p>

    </div>

    <div class="top-right">

        <span class="today">

            <i class="fa-solid fa-calendar-days"></i>

            <?php echo date("l, d F Y"); ?>

        </span>

    </div>

</div>