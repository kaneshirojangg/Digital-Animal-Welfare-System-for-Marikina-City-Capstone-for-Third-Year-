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

$animalId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($animalId <= 0) {
    header("Location: adopt-animal.php");
    exit();
}

$animal = null;
$vaccinations = [];

$stmt = $conn->prepare("SELECT id, name, type, age, gender, status, breed, color, microchip, weight, description, intake_date FROM animals WHERE id = ? AND status = 'Available for Adoption'");
$stmt->bind_param("i", $animalId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $animal = $result->fetch_assoc();

    $vaccStmt = $conn->prepare("SELECT vaccine_name, vaccination_date, veterinarian FROM vaccinations WHERE animal_id = ? ORDER BY vaccination_date DESC");
    $vaccStmt->bind_param("i", $animalId);
    $vaccStmt->execute();
    $vaccResult = $vaccStmt->get_result();
    
    while ($row = $vaccResult->fetch_assoc()) {
        $vaccinations[] = $row;
    }
    $vaccStmt->close();
} else {
    $stmt->close();
    $conn->close();
    header("Location: adopt-animal.php");
    exit();
}

$stmt->close();
$conn->close();
$activePage = 'adopt';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($animal['name']); ?> - Marikina A&W</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/nav.css">
    <link rel="stylesheet" href="../assets/css/animal-detail.css">
</head>
<body>
    <?php include 'nav-menu.php'; ?>
    
    <div class="main-content">
        <a href="adopt-animal.php" class="back-link">← Back to Animals</a>
        
        <div class="animal-hero">
            <div class="animal-hero-icon">
                <?php
                $icon = match(strtolower($animal['type'])) {
                    'dog' => '🐕',
                    'cat' => '🐈',
                    'rabbit' => '🐰',
                    'bird' => '🦜',
                    default => '🐾'
                };
                echo $icon;
                ?>
            </div>
            <div class="animal-hero-content">
                <h1><?php echo htmlspecialchars($animal['name']); ?></h1>
                <div class="hero-details">
                    <div class="hero-detail">
                        <div class="hero-detail-label">Type</div>
                        <div class="hero-detail-value"><?php echo htmlspecialchars($animal['type']); ?></div>
                    </div>
                    <div class="hero-detail">
                        <div class="hero-detail-label">Age</div>
                        <div class="hero-detail-value"><?php echo htmlspecialchars($animal['age']); ?></div>
                    </div>
                    <div class="hero-detail">
                        <div class="hero-detail-label">Gender</div>
                        <div class="hero-detail-value"><?php echo htmlspecialchars($animal['gender']); ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="content-wrapper">
            <div class="main-info">
                <div class="info-section">
                    <div class="section-title">About <?php echo htmlspecialchars($animal['name']); ?></div>
                    <p class="description-text">
                        <?php echo htmlspecialchars($animal['description'] ?? 'No description available yet.'); ?>
                    </p>
                </div>
                
                <div class="info-section">
                    <div class="section-title">Physical Information</div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Breed</div>
                            <div class="info-value"><?php echo htmlspecialchars($animal['breed'] ?? 'Not specified'); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Color</div>
                            <div class="info-value"><?php echo htmlspecialchars($animal['color'] ?? 'Not specified'); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Weight</div>
                            <div class="info-value"><?php echo htmlspecialchars($animal['weight'] ?? 'Not specified'); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Microchip</div>
                            <div class="info-value"><?php echo htmlspecialchars($animal['microchip'] ?? 'Not registered'); ?></div>
                        </div>
                    </div>
                </div>
                
                <?php if (count($vaccinations) > 0): ?>
                <div class="info-section">
                    <div class="section-title">Vaccination Records</div>
                    <ul class="vaccinations-list">
                        <?php foreach ($vaccinations as $vacc): ?>
                        <li class="vaccination-item">
                            <div class="vacc-name"><?php echo htmlspecialchars($vacc['vaccine_name']); ?></div>
                            <div class="vacc-detail">
                                Date: <?php echo date('M d, Y', strtotime($vacc['vaccination_date'])); ?> 
                                | By: <?php echo htmlspecialchars($vacc['veterinarian']); ?>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <div class="info-section">
                    <div class="section-title">Intake Information</div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Intake Date</div>
                            <div class="info-value"><?php echo date('M d, Y', strtotime($animal['intake_date'])); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="adoption-cards">
                <div class="sidebar-card">
                    <div style="text-align: left; margin-bottom: 12px;">
                        <span class="status-badge">Ready for Adoption</span>
                    </div>
                    <button class="adopt-btn" onclick="window.location.href='adoption-request.php?animal_id=<?php echo $animal['id']; ?>'">
                        Start Adoption Process
                    </button>
                    <div class="adoption-info">
                        <strong>Next Steps:</strong><br>
                        Click the button above to fill out an adoption application. Our staff will review your application shortly.
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
