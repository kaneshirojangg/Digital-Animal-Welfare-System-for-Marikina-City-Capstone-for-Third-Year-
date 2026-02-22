<?php
include 'session-handler.php';
session_start();

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: login.php");
    exit();
}

$activePage = 'vaccinations';

$conn = new mysqli('localhost', 'marikina_user', 'marikina_password', 'marikina_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$full_name = $_SESSION['full_name'];

$vaccinations = [];
$result = $conn->query("
    SELECT v.id, v.animal_name, v.vaccine_type, v.schedule_date, v.vet_staff, v.status 
    FROM vaccinations v
    INNER JOIN adoptions a ON v.animal_name = a.animal_name
    WHERE a.applicant_name = '$full_name' AND a.status = 'Completed'
    ORDER BY v.schedule_date DESC
");

while ($row = $result->fetch_assoc()) {
    $vaccinations[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vaccination Records - Marikina A&W</title>

  <style>
    :root {
      --primary: #2c7d4e;
      --primary-dark: #1e5c38;
      --text: #2d3748;
      --text-light: #4b5563;
      --bg: #f8fafc;
      --border: #e2e8f0;
      --success: #10b981;
      --warning: #f59e0b;
      --danger: #dc2626;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      margin-left: 280px;
      min-height: 100vh;
    }

    .main-content {
      max-width: 1200px;
      margin: 0 auto;
      padding: 40px 24px;
    }

    .page-header {
      margin-bottom: 40px;
    }

    .page-header h1 {
      font-family: 'Playfair Display', serif;
      font-size: 2.4rem;
      color: var(--primary-dark);
      margin-bottom: 8px;
    }

    .page-header p {
      color: var(--text-light);
      font-size: 1.05rem;
    }

    .info-box {
      background: #dbeafe;
      border-left: 4px solid #3b82f6;
      padding: 16px;
      border-radius: 8px;
      margin-bottom: 24px;
      color: #1e40af;
    }

    .info-box strong { color: #1e3a8a; }

    .vaccine-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 20px;
      margin-bottom: 40px;
    }

    .vaccine-card {
      background: white;
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
    }

    .vaccine-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    }

    .vaccine-card h3 {
      color: var(--primary-dark);
      margin-bottom: 12px;
      font-size: 1.1rem;
    }

    .vaccine-info {
      margin-bottom: 12px;
      padding-bottom: 8px;
      border-bottom: 1px solid var(--border);
    }

    .vaccine-info label {
      font-weight: 600;
      color: var(--text-light);
      font-size: 0.85rem;
      text-transform: uppercase;
    }

    .vaccine-info p {
      margin-top: 4px;
      color: var(--text);
    }

    .status-badge {
      display: inline-block;
      padding: 6px 14px;
      border-radius: 999px;
      font-size: 0.85rem;
      font-weight: 600;
      margin-top: 12px;
    }

    .status-scheduled {
      background: #fef3c7;
      color: #92400e;
    }

    .status-done {
      background: #d1fae5;
      color: #065f46;
    }

    .status-pending {
      background: #fee2e2;
      color: #991b1b;
    }

    .empty-state {
      text-align: center;
      padding: 60px 20px;
      background: white;
      border-radius: 12px;
      border: 2px dashed var(--border);
    }

    .empty-state p {
      color: var(--text-light);
      font-size: 1.1rem;
      margin-bottom: 12px;
    }

    .empty-state small {
      color: var(--text-light);
    }

    @media (max-width: 768px) {
      body { margin-left: 0; }
      .main-content { padding: 20px; }
      .page-header h1 { font-size: 1.8rem; }
      .vaccine-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<?php include 'nav-menu.php'; ?>

<div class="main-content">

  <div class="page-header">
    <h1>Vaccination Records</h1>
    <p>View vaccination schedules for your adopted animals</p>
  </div>

  <div class="info-box">
    <strong>ℹ️ Staff-Created Schedules:</strong> Vaccination schedules are created by our staff and veterinarians. Contact us if you have questions about your animal's health.
  </div>

  <?php if (count($vaccinations) > 0): ?>
    <div class="vaccine-grid">
      <?php foreach ($vaccinations as $vax): ?>
        <div class="vaccine-card">
          <h3><?php echo htmlspecialchars($vax['animal_name']); ?></h3>
          
          <div class="vaccine-info">
            <label>Vaccine Type</label>
            <p><?php echo htmlspecialchars($vax['vaccine_type']); ?></p>
          </div>

          <div class="vaccine-info">
            <label>Scheduled Date</label>
            <p><?php echo date('M d, Y @ h:i A', strtotime($vax['schedule_date'])); ?></p>
          </div>

          <div class="vaccine-info">
            <label>Veterinarian / Staff</label>
            <p><?php echo htmlspecialchars($vax['vet_staff'] ?? 'TBA'); ?></p>
          </div>

          <strong>Status:</strong>
          <div>
            <?php
              $statusClass = 'status-pending';
              if ($vax['status'] == 'Done') $statusClass = 'status-done';
              elseif ($vax['status'] == 'Scheduled') $statusClass = 'status-scheduled';
            ?>
            <span class="status-badge <?php echo $statusClass; ?>">
              <?php echo htmlspecialchars($vax['status']); ?>
            </span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="empty-state">
      <p>No Vaccination Records Yet</p>
      <small>Vaccination schedules will appear here once you adopt an animal and the staff creates a schedule for their wellness.</small>
    </div>
  <?php endif; ?>

</div>

</body>
</html>

<?php $conn->close(); ?>