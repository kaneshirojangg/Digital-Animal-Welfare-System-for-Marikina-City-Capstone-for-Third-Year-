<?php
include 'session-handler.php';
session_start();

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
	header("Location: login.php");
	exit();
}

$activePage = 'reports';

$activePage = 'reports';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Analytics – Marikina Animal & Welfare</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/variables.css">
  <link rel="stylesheet" href="../assets/css/nav.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include 'nav-menu.php'; ?>

  <div class="main-content">
    <h1 class="page-title">Analytics & Statistics</h1>

    <div class="action-bar">
      <button class="btn">Generate PDF Report</button>
      <button class="btn">Export CSV</button>
    </div>

    <div class="card-container">
      <div class="report-card">
        <h3>Bite Incidents This Month</h3>
        <p class="big-number text-primary">38</p>
        <p class="text-success">+12% from last month</p>
      </div>

      <div class="report-card">
        <h3>Adoption Rate</h3>
        <p class="big-number text-success">67%</p>
        <p>25 adopted out of 37 eligible animals</p>
      </div>

      <div class="report-card">
        <h3>Vaccinations Completed</h3>
        <p class="big-number text-primary">112</p>
        <p>This quarter</p>
      </div>

      <div class="report-card">
        <h3>Animals in Shelter</h3>
        <p class="big-number text-warning">42</p>
        <p>28 dogs • 14 cats</p>
      </div>
    </div>
  </div>

</body>
</html>