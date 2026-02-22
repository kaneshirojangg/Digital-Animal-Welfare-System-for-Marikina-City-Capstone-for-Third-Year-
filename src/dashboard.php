<?php
include 'session-handler.php';
session_start();

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
	header("Location: login.php");
	exit();
}

$conn = new mysqli('localhost', 'marikina_user', 'marikina_password', 'marikina_db');
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

$pending = $conn->query("SELECT COUNT(*) as count FROM adoptions WHERE applicant_name = '$full_name' AND status = 'Pending'")->fetch_assoc()['count'];
$approved = $conn->query("SELECT COUNT(*) as count FROM adoptions WHERE applicant_name = '$full_name' AND status = 'Approved'")->fetch_assoc()['count'];
$completed = $conn->query("SELECT COUNT(*) as count FROM adoptions WHERE applicant_name = '$full_name' AND status = 'Completed'")->fetch_assoc()['count'];

$user_requests = $conn->query("SELECT animal_name, animal_type, status, request_date FROM adoptions WHERE applicant_name = '$full_name' ORDER BY request_date DESC LIMIT 5");

$featured_animals = $conn->query("SELECT id, name, type, age, gender, description FROM animals WHERE status = 'Available for Adoption' ORDER BY intake_date DESC LIMIT 6");

$vaccination_schedules = $conn->query("
  SELECT 
    v.id, 
    v.animal_name, 
    v.vaccine_type, 
    v.schedule_date, 
    v.vet_staff, 
    v.status
  FROM vaccinations v
  INNER JOIN adoptions a ON v.animal_name = a.animal_name
  WHERE a.applicant_name = '$full_name' 
    AND a.status = 'Completed'
    AND v.schedule_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
  ORDER BY v.schedule_date ASC
  LIMIT 8
");

$conn->close();
$activePage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard – Marikina Animal & Welfare</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: #2c7d4e;
      --primary-dark: #1e5c38;
      --danger: #dc2626;
      --warning: #f59e0b;
      --success: #10b981;
      --info: #3b82f6;
      --text: #2d3748;
      --text-light: #4b5563;
      --bg: #f8fafc;
      --card-bg: #ffffff;
      --border: #e2e8f0;
    }

    * { margin:0; padding:0; box-sizing:border-box; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      margin-left: 280px;
    }

    .layout { display: flex; }

    /* Sidebar styles hidden since nav-menu.php is used */
    .sidebar {
      display: none;
    }

    .main-content {
      flex: 1;
      padding: 40px;
      max-width: 1300px;
      margin-right: auto;
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

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
      gap: 24px;
      margin-bottom: 40px;
    }

    .stat-card {
      background: var(--card-bg);
      padding: 28px;
      border-radius: 12px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
    }

    .stat-card:hover {
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      transform: translateY(-2px);
    }

    .stat-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }

    .stat-card h3 {
      font-size: 0.95rem;
      color: var(--text-light);
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .big-number {
      font-size: 2.8rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 8px 0;
    }

    .stat-card-footer {
      font-size: 0.9rem;
      color: var(--text-light);
      margin-top: 12px;
      padding-top: 12px;
      border-top: 1px solid var(--border);
    }

    .dashboard-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 24px;
      margin-bottom: 40px;
    }

    .card {
      background: var(--card-bg);
      padding: 28px;
      border-radius: 12px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .card h2 {
      font-size: 1.3rem;
      color: var(--primary-dark);
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .quick-actions {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .action-btn {
      display: block;
      padding: 14px 20px;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 0.95rem;
      font-weight: 600;
      cursor: pointer;
      text-align: center;
      transition: all 0.3s ease;
    }

    .action-btn:hover {
      background: var(--primary-dark);
      transform: translateX(4px);
      box-shadow: 0 4px 12px rgba(44, 125, 78, 0.2);
    }

    .recent-list {
      list-style: none;
    }

    .recent-item {
      padding: 12px 0;
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.95rem;
    }

    .recent-item:last-child {
      border-bottom: none;
    }

    .recent-item strong {
      color: var(--primary-dark);
    }

    .recent-time {
      color: var(--text-light);
      font-size: 0.85rem;
    }

    .status-badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 12px;
      font-size: 0.8rem;
      font-weight: 600;
    }

    .status-badge.urgent {
      background: #fee2e2;
      color: var(--danger);
    }

    .status-badge.pending {
      background: #fef3c7;
      color: #92400e;
    }

    .status-badge.approved {
      background: #d1fae5;
      color: #065f46;
    }

    .status-badge.completed {
      background: #dbeafe;
      color: #0c4a6e;
    }

    .animal-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }

    .animal-card {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: 12px;
      overflow: hidden;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .animal-card:hover {
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      transform: translateY(-4px);
    }

    .animal-card-image {
      width: 100%;
      height: 180px;
      background: linear-gradient(135deg, #e0e7ff, #f0f4ff);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3rem;
      border-bottom: 1px solid var(--border);
    }

    .animal-card-info {
      padding: 16px;
    }

    .animal-card-name {
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin-bottom: 4px;
    }

    .animal-card-type {
      font-size: 0.85rem;
      color: var(--text-light);
      margin-bottom: 12px;
    }

    .animal-card-details {
      display: flex;
      gap: 8px;
      font-size: 0.8rem;
      color: var(--text-light);
      margin-bottom: 12px;
    }

    .animal-card-btn {
      width: 100%;
      padding: 10px;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 0.9rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .animal-card-btn:hover {
      background: var(--primary-dark);
    }

    .empty-state {
      text-align: center;
      padding: 40px 20px;
      color: var(--text-light);
    }

    .empty-state p {
      margin-bottom: 20px;
      font-size: 1rem;
    }

    .empty-state-btn {
      display: inline-block;
      padding: 12px 24px;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 0.95rem;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .empty-state-btn:hover {
      background: var(--primary-dark);
    }

    .vaccination-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 16px;
      margin-top: 20px;
    }

    .vaccination-card {
      background: linear-gradient(135deg, #f0fdf4 0%, #dbeafe 100%);
      border: 1px solid #bbf7d0;
      border-radius: 12px;
      padding: 18px;
      position: relative;
      overflow: hidden;
    }

    .vaccination-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 4px;
      height: 100%;
      background: var(--primary);
    }

    .vaccination-card-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 12px;
    }

    .vaccination-card-header h4 {
      font-size: 1rem;
      color: var(--primary-dark);
      font-weight: 700;
      flex: 1;
    }

    .vaccination-status {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .vaccination-status.scheduled {
      background: #fef3c7;
      color: #92400e;
    }

    .vaccination-status.done {
      background: #d1fae5;
      color: #065f46;
    }

    .vaccination-status.pending {
      background: #fee2e2;
      color: #991b1b;
    }

    .vaccination-info {
      display: flex;
      flex-direction: column;
      gap: 8px;
      font-size: 0.9rem;
      color: var(--text);
    }

    .vaccination-info-row {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .vaccination-info-label {
      color: var(--text-light);
      font-weight: 500;
      min-width: 70px;
    }

    .vaccination-info-value {
      color: var(--primary-dark);
      font-weight: 600;
    }

    .vaccination-schedule-icon {
      font-size: 1.2rem;
      margin-right: 4px;
    }

    .no-vaccination-message {
      text-align: center;
      padding: 30px;
      color: var(--text-light);
      background: #f9fafb;
      border-radius: 8px;
    }

    @media (max-width: 968px) {
      .dashboard-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .main-content { 
        margin-left: 0; 
        padding: 20px;
      }
      .page-header h1 { font-size: 1.8rem; }
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
  </style>
</head>
<body>

<?php include 'nav-menu.php'; ?>

<div class="layout">
    <main class="main-content">

      <div class="page-header">
        <h1>Welcome, <?php echo htmlspecialchars($full_name); ?>!</h1>
        <p>Here's an overview of your adoption journey</p>
      </div>

      <div class="stats-grid">
        <div class="stat-card info">
          <div class="stat-card-header">
            <h3>Pending Requests</h3>
          </div>
          <div class="big-number"><?php echo $pending; ?></div>
          <div class="stat-card-footer">Awaiting shelter review</div>
        </div>

        <div class="stat-card warning">
          <div class="stat-card-header">
            <h3>Approved Requests</h3>
          </div>
          <div class="big-number"><?php echo $approved; ?></div>
          <div class="stat-card-footer">Ready to proceed</div>
        </div>

        <div class="stat-card success">
          <div class="stat-card-header">
            <h3>Completed Adoptions</h3>
          </div>
          <div class="big-number"><?php echo $completed; ?></div>
          <div class="stat-card-footer">New family members</div>
        </div>
      </div>

      <div class="dashboard-grid">
        <div class="card">
          <h2>My Adoption Requests</h2>
          <?php if ($user_requests && $user_requests->num_rows > 0): ?>
            <ul class="recent-list">
              <?php while ($request = $user_requests->fetch_assoc()): ?>
                <li class="recent-item">
                  <div>
                    <strong><?php echo htmlspecialchars($request['animal_name']); ?></strong>
                    <div style="font-size: 0.85rem; color: var(--text-light); margin-top: 4px;">
                      <?php echo htmlspecialchars($request['animal_type']); ?> • 
                      <?php echo date('M d, Y', strtotime($request['request_date'])); ?>
                    </div>
                  </div>
                  <span class="status-badge <?php echo strtolower($request['status']); ?>">
                    <?php echo htmlspecialchars($request['status']); ?>
                  </span>
                </li>
              <?php endwhile; ?>
            </ul>
          <?php else: ?>
            <div class="empty-state">
              <p>You haven't submitted any adoption requests yet</p>
              <a href="adopt-animal.php" class="empty-state-btn">Browse Animals</a>
            </div>
          <?php endif; ?>
        </div>

        <div class="card">
          <h2>Quick Links</h2>
          <div class="quick-actions">
            <button class="action-btn" onclick="window.location.href='incidents.php'" title="Report any incidents or welfare concerns">
              <span>Report Incident</span>
            </button>
            <button class="action-btn" onclick="window.location.href='adopt-animal.php'" title="Browse and discover animals available for adoption">
              <span>Adopt Animals</span>
            </button>
            <button class="action-btn" onclick="window.location.href='vaccinations.php'" title="View vaccination schedules for your adopted animals">
              <span>Vaccinations</span>
            </button>
            <button class="action-btn" onclick="window.location.href='reports.php'" title="View reports and analytics">
              <span>Reports</span>
            </button>
          </div>
        </div>
      </div>

      <div class="card" style="margin-bottom: 40px;">
        <h2>Featured Animals Available for Adoption</h2>
        <?php if ($featured_animals && $featured_animals->num_rows > 0): ?>
          <div class="animal-grid">
            <?php while ($animal = $featured_animals->fetch_assoc()): 
              $animal_emoji = strtolower($animal['type']) === 'dog' ? '🐕' : '🐈';
            ?>
              <div class="animal-card">
                <div class="animal-card-image">
                  <?php echo $animal_emoji; ?>
                </div>
                <div class="animal-card-info">
                  <div class="animal-card-name"><?php echo htmlspecialchars($animal['name']); ?></div>
                  <div class="animal-card-type"><?php echo htmlspecialchars($animal['type']); ?></div>
                  <div class="animal-card-details">
                    <?php if ($animal['age']): ?>
                      <span><?php echo htmlspecialchars($animal['age']); ?> yrs old</span>
                    <?php endif; ?>
                    <?php if ($animal['gender']): ?>
                      <span>•</span>
                      <span><?php echo htmlspecialchars($animal['gender']); ?></span>
                    <?php endif; ?>
                  </div>
                  <a href="animal-detail.php?id=<?php echo $animal['id']; ?>" class="animal-card-btn">
                    View Details
                  </a>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
        <?php else: ?>
          <div class="empty-state">
            <p>No animals available for adoption at the moment.</p>
            <p>Check back soon!</p>
          </div>
        <?php endif; ?>
      </div>

      <div class="card" style="margin-bottom: 40px;">
        <h2>Upcoming Vaccination Schedules</h2>
        <?php if ($vaccination_schedules && $vaccination_schedules->num_rows > 0): ?>
          <div class="vaccination-grid">
            <?php while ($vac = $vaccination_schedules->fetch_assoc()): 
              $vac_date = new DateTime($vac['schedule_date']);
              $today = new DateTime();
              $is_upcoming = $vac_date > $today;
              $status_class = strtolower($vac['status']);
            ?>
              <div class="vaccination-card">
                <div class="vaccination-card-header">
                  <h4><?php echo htmlspecialchars($vac['animal_name']); ?></h4>
                  <span class="vaccination-status <?php echo $status_class === 'done' ? 'done' : ($status_class === 'pending' ? 'pending' : 'scheduled'); ?>">
                    <?php echo htmlspecialchars($vac['status'] ?? 'Scheduled'); ?>
                  </span>
                </div>
                <div class="vaccination-info">
                  <div class="vaccination-info-row">
                    <span class="vaccination-info-label">Vaccine:</span>
                    <span class="vaccination-info-value"><?php echo htmlspecialchars($vac['vaccine_type']); ?></span>
                  </div>
                  <div class="vaccination-info-row">
                    <span class="vaccination-info-label">Date:</span>
                    <span class="vaccination-info-value"><?php echo date('M d, Y', strtotime($vac['schedule_date'])); ?></span>
                  </div>
                  <div class="vaccination-info-row">
                    <span class="vaccination-info-label">Time:</span>
                    <span class="vaccination-info-value"><?php echo date('h:i A', strtotime($vac['schedule_date'])); ?></span>
                  </div>
                  <?php if ($vac['vet_staff']): ?>
                    <div class="vaccination-info-row">
                      <span class="vaccination-info-label">Vet:</span>
                      <span class="vaccination-info-value"><?php echo htmlspecialchars($vac['vet_staff']); ?></span>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
          <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border); text-align: center;">
            <a href="vaccinations.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">
              View All Vaccination Schedules →
            </a>
          </div>
        <?php else: ?>
          <div class="no-vaccination-message">
            <p style="font-size: 1rem; margin-bottom: 16px;">
              No upcoming vaccination schedules yet
            </p>
            <p style="font-size: 0.9rem; color: var(--text-light);">
              Once you complete an adoption, vaccination schedules for your pet will appear here.
            </p>
            <div style="margin-top: 16px;">
              <a href="vaccinations.php" style="display: inline-block; padding: 10px 20px; background: var(--primary); color: white; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;" 
                onmouseover="this.style.background='var(--primary-dark)'" 
                onmouseout="this.style.background='var(--primary)'">
                Visit Vaccination Center
              </a>
            </div>
          </div>
        <?php endif; ?>
      </div>

    </main>

  </div>

</body>
</html>