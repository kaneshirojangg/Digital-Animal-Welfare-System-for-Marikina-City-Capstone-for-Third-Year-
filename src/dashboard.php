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

$pending  = $conn->query("SELECT COUNT(*) as count FROM adoptions WHERE (user_id = $user_id OR applicant_name = '$full_name') AND status = 'Pending'")->fetch_assoc()['count'];
$approved = $conn->query("SELECT COUNT(*) as count FROM adoptions WHERE (user_id = $user_id OR applicant_name = '$full_name') AND status = 'Approved'")->fetch_assoc()['count'];
$completed = $conn->query("SELECT COUNT(*) as count FROM adoptions WHERE (user_id = $user_id OR applicant_name = '$full_name') AND status = 'Completed'")->fetch_assoc()['count'];



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

$incidents_result = $conn->query("SELECT id, incident_date, location, status, severity_level FROM incidents WHERE user_id = $user_id ORDER BY incident_date DESC LIMIT 5");
$user_requests = $conn->query("SELECT animal_name, animal_type, status, request_date FROM adoptions WHERE user_id = $user_id OR applicant_name = '$full_name' ORDER BY request_date DESC LIMIT 5");

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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/variables.css">
  <link rel="stylesheet" href="../assets/css/nav.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include 'nav-menu.php'; ?>

<div class="main-content">

  <div class="page-header">
    <h1>Welcome, <?php echo htmlspecialchars($full_name); ?>!</h1>
    <p>Here's an overview of your adoption journey</p>
  </div>

  <!-- Quick Links -->
  <div class="quick-links-grid">
    <a href="incidents.php" class="quick-link-card primary">
      <div class="quick-link-text">Report Incident</div>
    </a>
    <a href="my-incidents.php" class="quick-link-card secondary">
      <div class="quick-link-text">My Reports</div>
    </a>
    <a href="adopt-animal.php" class="quick-link-card primary">
      <div class="quick-link-text">Adopt Animal</div>
    </a>
    <a href="vaccinations.php" class="quick-link-card secondary">
      <div class="quick-link-text">Vaccination</div>
    </a>
  </div>

  <!-- 3-Column Dashboard Grid: My Reports | My Adoption Requests | Vaccinations -->
  <div class="dashboard-3col-grid">
    <!-- My Reports Column -->
    <div class="card">
      <h2>My Reports</h2>
      <?php if ($incidents_result && $incidents_result->num_rows > 0): ?>
        <ul class="recent-list">
          <?php while ($incident = $incidents_result->fetch_assoc()): ?>
            <li class="recent-item">
              <div>
                <strong><?php echo date('M d, Y', strtotime($incident['incident_date'])); ?></strong>
                <div class="recent-item-subtitle">
                  <?php echo htmlspecialchars($incident['location']); ?> &middot; <?php echo htmlspecialchars($incident['severity_level']); ?> Severity
                </div>
              </div>
              <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $incident['status'])); ?>">
                <?php echo htmlspecialchars($incident['status']); ?>
              </span>
            </li>
          <?php endwhile; ?>
        </ul>
        <div class="card-divider">
          <a href="my-incidents.php" class="card-link">View All Reports →</a>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <p>No incident reports yet</p>
          <a href="incidents.php" class="empty-state-btn">Report Incident</a>
        </div>
      <?php endif; ?>
    </div>

    <!-- My Adoption Requests Column -->
    <div class="card">
      <h2>My Adoption Requests</h2>
      <?php if ($user_requests && $user_requests->num_rows > 0): ?>
        <ul class="recent-list">
          <?php while ($request = $user_requests->fetch_assoc()): ?>
            <li class="recent-item">
              <div>
                <strong><?php echo htmlspecialchars($request['animal_name']); ?></strong>
                <div class="recent-item-subtitle">
                  <?php echo htmlspecialchars($request['animal_type']); ?> • 
                  <?php echo date('M d, Y', strtotime($request['request_date'])); ?>
                </div>
              </div>
              <span class="status-badge status-<?php echo strtolower($request['status']); ?>">
                <?php echo htmlspecialchars($request['status']); ?>
              </span>
            </li>
          <?php endwhile; ?>
        </ul>
        <div class="card-divider">
          <a href="adoptions.php" class="card-link">View All →</a>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <p>No adoption requests yet</p>
          <a href="adopt-animal.php" class="empty-state-btn">Browse Animals</a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Vaccination Schedule Column -->
    <div class="card">
      <h2>Vaccination Schedule</h2>
      <?php if ($vaccination_schedules && $vaccination_schedules->num_rows > 0): ?>
        <ul class="recent-list">
          <?php while ($vac = $vaccination_schedules->fetch_assoc()): ?>
            <li class="recent-item">
              <div>
                <strong><?php echo htmlspecialchars($vac['animal_name']); ?></strong>
                <div class="recent-item-subtitle">
                  <?php echo htmlspecialchars($vac['vaccine_type']); ?> • 
                  <?php echo date('M d, Y', strtotime($vac['schedule_date'])); ?>
                </div>
              </div>
              <span class="status-badge <?php echo strtolower($vac['status']); ?>">
                <?php echo htmlspecialchars($vac['status']); ?>
              </span>
            </li>
          <?php endwhile; ?>
        </ul>
        <div class="card-divider">
          <a href="vaccinations.php" class="card-link">View All →</a>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <p>No vaccination schedules yet</p>
          <a href="vaccinations.php" class="empty-state-btn">Check Schedules</a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Featured Animals Carousel -->
  <div class="card card-spaced">
    <h2>Featured Animals Available for Adoption</h2>
    <?php if ($featured_animals && $featured_animals->num_rows > 0): ?>
      <div class="carousel-container">
        <button class="carousel-btn carousel-btn-prev" onclick="scrollCarousel('left')">
          <span>&lt;</span>
        </button>
        <div class="animals-carousel" id="animalsCarousel">
          <?php while ($animal = $featured_animals->fetch_assoc()): 
            $animal_emoji = strtolower($animal['type']) === 'dog' ? '🐕' : '🐈';
          ?>
            <div class="animal-card-carousel">
              <div class="animal-card-image">
                <?php echo $animal_emoji; ?>
              </div>
              <div class="animal-card-info">
                <div class="animal-card-name"><?php echo htmlspecialchars($animal['name']); ?></div>
                <div class="animal-card-type"><?php echo htmlspecialchars($animal['type']); ?></div>
                <div class="animal-card-details">
                  <?php if ($animal['age']): ?>
                    <span><?php echo htmlspecialchars($animal['age']); ?> yrs</span>
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
        <button class="carousel-btn carousel-btn-next" onclick="scrollCarousel('right')">
          <span>&gt;</span>
        </button>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <p>No animals available for adoption at the moment.</p>
        <p>Check back soon!</p>
      </div>
    <?php endif; ?>
  </div>

</div>

<script>
function scrollCarousel(direction) {
  const carousel = document.getElementById('animalsCarousel');
  const scrollAmount = 220; // card width + gap
  
  if (direction === 'left') {
    carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
  } else if (direction === 'right') {
    carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
  }
}
</script>

</body>
</html>

</div>

<script>
function scrollCarousel(direction) {
  const carousel = document.getElementById('animalsCarousel');
  const scrollAmount = 220; // card width + gap
  
  if (direction === 'left') {
    carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
  } else if (direction === 'right') {
    carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
  }
}
</script>

</body>
</html>