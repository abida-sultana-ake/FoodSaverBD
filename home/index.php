<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>FoodSaver BD</title>

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

    <!-- Home CSS -->
    <link rel="stylesheet" href="../assets/css/home.css">

</head>

<body>

    <?php include("components/navbar.php"); ?>
    <?php include("components/hero.php"); ?>
    <?php include("components/features.php"); ?>
    <?php include("components/impact.php"); ?>
    <?php include("components/how-it-works.php"); ?>
    <?php include("components/cta.php"); ?>
    <?php include("components/footer.php"); ?>
    
    <script>

        const mobileMenuBtn =
            document.getElementById("mobileMenuBtn");

        const mobileNav =
            document.getElementById("mobileNav");


        mobileMenuBtn.addEventListener("click", function () {

            mobileNav.classList.toggle("active");

        });

    </script>

</body>

</html>