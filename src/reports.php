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
  <title>Reports – Marikina Animal & Welfare</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: #2c7d4e;
      --primary-dark: #1e5c38;
      --accent: #e67e22;
      --danger: #dc2626;
      --warning: #f59e0b;
      --success: #10b981;
      --text: #2d3748;
      --text-light: #4b5563;
      --bg: #f8fafc;
      --card: #ffffff;
      --border: #e2e8f0;
      --table-header-bg: rgba(44,125,78,0.08);
      --hover-bg: rgba(44,125,78,0.04);
    }

    * { margin:0; padding:0; box-sizing:border-box; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      line-height: 1.6;
      min-height: 100vh;
      margin-left: 280px;
    }

    header {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: white;
      text-align: center;
      padding: 60px 20px 40px;
    }

    header h1 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(2.2rem, 5.5vw, 3.8rem);
      margin-bottom: 12px;
    }

    header .subtitle {
      font-size: clamp(1rem, 2.8vw, 1.3rem);
      opacity: 0.95;
      max-width: 800px;
      margin: 0 auto;
    }

    .main-content {
      max-width: 1200px;
      margin: 0 auto;
      padding: 40px 24px;
    }

    .page-title {
      font-family: 'Playfair Display', serif;
      color: var(--primary-dark);
      font-size: 2.4rem;
      margin-bottom: 24px;
      text-align: center;
    }

    .action-bar {
      margin: 24px 0;
      text-align: right;
      display: flex;
      gap: 12px;
      justify-content: flex-end;
      flex-wrap: wrap;
    }

    .btn {
      background: var(--primary);
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
    }

    .btn:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(44,125,78,0.25);
    }

    .card-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
      gap: 24px;
      margin: 32px 0;
    }

    .report-card {
      background: white;
      border-radius: 12px;
      padding: 24px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
      text-align: center;
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .report-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }

    .report-card h3 {
      color: var(--primary-dark);
      margin-bottom: 12px;
    }

    .big-number {
      font-size: 3.5rem;
      font-weight: bold;
      margin: 16px 0;
    }

    @media (max-width: 768px) {
      header { padding: 50px 16px 30px; }
      .main-content { padding: 20px; }
      .page-title { font-size: 2rem; }
      .action-bar { justify-content: center; }
      .card-container { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<?php include 'nav-menu.php'; ?>

  <div class="main-content">
    <h1 class="page-title">Reports & Analytics</h1>

    <div class="action-bar">
      <button class="btn">Generate PDF Report</button>
      <button class="btn">Export CSV</button>
    </div>

    <div class="card-container">
      <div class="report-card">
        <h3>Bite Incidents This Month</h3>
        <p class="big-number" style="color: var(--primary);">38</p>
        <p style="color: var(--success);">+12% from last month</p>
      </div>

      <div class="report-card">
        <h3>Adoption Rate</h3>
        <p class="big-number" style="color: var(--success);">67%</p>
        <p>25 adopted out of 37 eligible animals</p>
      </div>

      <div class="report-card">
        <h3>Vaccinations Completed</h3>
        <p class="big-number" style="color: var(--primary);">112</p>
        <p>This quarter</p>
      </div>

      <div class="report-card">
        <h3>Animals in Shelter</h3>
        <p class="big-number" style="color: var(--warning);">42</p>
        <p>28 dogs • 14 cats</p>
      </div>
    </div>
  </div>

</body>
</html>