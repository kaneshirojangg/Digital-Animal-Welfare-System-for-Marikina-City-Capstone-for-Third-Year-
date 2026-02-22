<?php
session_start();
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: login.php");
    exit();
}
$page_title = "Adoptions"; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $page_title; ?> - Marikina A&W</title>
  <link rel="stylesheet" href="../assets/css/dashboard-ui.css">
  <style>
    /* yung style mo dito */
  </style>
</head>
<body>
  <div class="content">
    <h1><?php echo $page_title; ?> Management</h1>
    <p>Dito ang content para sa <?php echo strtolower($page_title); ?>...</p>
    <!-- idagdag mo ang table, form, etc. -->
  </div>
</body>
</html>