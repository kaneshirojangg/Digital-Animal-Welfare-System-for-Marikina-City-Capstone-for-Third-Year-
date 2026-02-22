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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #2d3748;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 32px;
            min-height: 100vh;
            max-width: 1100px;
            margin-right: auto;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #2c7d4e;
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: 500;
            transition: color 0.3s ease;
            font-size: 0.95rem;
        }
        
        .back-link:hover {
            color: #1e5c38;
        }
        
        .animal-hero {
            background: linear-gradient(135deg, #2c7d4e 0%, #1e5c38 100%);
            color: white;
            padding: 48px;
            border-radius: 12px;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            gap: 32px;
        }
        
        .animal-hero-icon {
            font-size: 5rem;
            min-width: 5rem;
        }
        
        .animal-hero-content h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            margin-bottom: 16px;
        }
        
        .hero-details {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }
        
        .hero-detail {
            background: rgba(255,255,255,0.15);
            padding: 10px 16px;
            border-radius: 8px;
            backdrop-filter: blur(10px);
            font-size: 0.9rem;
        }
        
        .hero-detail-label {
            font-size: 0.85rem;
            opacity: 0.9;
        }
        
        .hero-detail-value {
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .content-wrapper {
            display: block;
            width: 100%;
        }
        
        .main-info {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }
        
        .info-section {
            margin-bottom: 24px;
        }
        
        .info-section:last-child {
            margin-bottom: 0;
        }
        
        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1e5c38;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .description-text {
            line-height: 1.6;
            color: #4b5563;
            font-size: 1rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        
        .info-item {
            background: #f8fafc;
            padding: 12px;
            border-radius: 8px;
            border-left: 3px solid #2c7d4e;
            font-size: 0.9rem;
        }
        
        .info-label {
            font-size: 0.85rem;
            color: #4b5563;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .info-value {
            font-size: 1.05rem;
            color: #1e5c38;
            font-weight: 600;
        }
        
        .vaccinations-list {
            list-style: none;
        }
        
        .vaccination-item {
            background: #f8fafc;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 12px;
            border-left: 3px solid #3b82f6;
        }
        
        .vaccination-item:last-child {
            margin-bottom: 0;
        }
        
        .vacc-name {
            font-weight: 600;
            color: #1e5c38;
            margin-bottom: 4px;
        }
        
        .vacc-detail {
            font-size: 0.9rem;
            color: #4b5563;
        }
        
        .adoption-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin: 32px auto 0;
            width: 100%;
            max-width: 800px;
        }
        
        .sidebar-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }
        
        .status-badge {
            display: inline-block;
            background: #dcfce7;
            color: #16a34a;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-align: center;
        }
        
        .adopt-cta {
            margin-top: 16px;
        }
        
        .adopt-btn {
            width: 100%;
            padding: 16px;
            background: #2c7d4e;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .adopt-btn:hover {
            background: #1e5c38;
            transform: scale(1.02);
        }
        
        .adoption-info {
            background: #f0fdf4;
            padding: 16px;
            border-left: 3px solid #16a34a;
            border-radius: 4px;
            font-size: 0.9rem;
            color: #166534;
            line-height: 1.5;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .animal-hero {
                flex-direction: column;
                align-items: flex-start;
                padding: 30px;
            }
            
            .animal-hero-icon {
                font-size: 3rem;
            }
            
            .animal-hero-content h1 {
                font-size: 2rem;
            }
            
            .content-wrapper {
                grid-template-columns: 1fr;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
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
            </div>
            
            <div class="adoption-cards">
                <div class="sidebar-card">
                    <div style="text-align: center; margin-bottom: 16px;">
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
                
                <div class="sidebar-card">
                    <div class="section-title" style="margin-bottom: 12px;">Intake Info</div>
                    <div class="info-item">
                        <div class="info-label">Intake Date</div>
                        <div class="info-value"><?php echo date('M d, Y', strtotime($animal['intake_date'])); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
