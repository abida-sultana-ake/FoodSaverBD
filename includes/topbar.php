<div class="topbar">

    <div class="top-left">

        <h2>Dashboard</h2>

        <p>Welcome back,
            <strong><?php echo htmlspecialchars($user_name); ?></strong>
        </p>

    </div>

    <div class="top-right">

        <span class="today">

            <?php echo date("l, d F Y"); ?>

        </span>

    </div>

</div>
<div class="stats">

    <div class="stat-card">

        <h3>Total Foods</h3>

        <h1><?php echo $total; ?></h1>

    </div>

    <div class="stat-card">

        <h3>Fresh Foods</h3>

        <h1><?php echo $fresh; ?></h1>

    </div>

    <div class="stat-card">

        <h3>Expiring Soon</h3>

        <h1><?php echo $expiring; ?></h1>

    </div>

    <div class="stat-card">

        <h3>Expired Foods</h3>

        <h1><?php echo $expired; ?></h1>

    </div>

    <div class="stat-card">

        <h3>Shared Foods</h3>

        <h1><?php echo $shared; ?></h1>

    </div>

</div>