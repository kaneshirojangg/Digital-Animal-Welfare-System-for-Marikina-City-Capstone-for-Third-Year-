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

$result = $conn->query("SELECT id, name, type, age, gender, status, description, intake_date FROM animals WHERE status IN ('Available for Adoption','Reserved','Adopted') ORDER BY intake_date DESC");

$animals = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $animals[] = $row;
    }
}

$conn->close();
$activePage = 'adopt';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adopt an Animal - Marikina A&W</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/nav.css">
    <link rel="stylesheet" href="../assets/css/adopt-animal.css">
</head>
<body>
    <?php include 'nav-menu.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1>Adopt an Animal</h1>
            <p>Find your perfect companion from our available animals</p>
        </div>
        
        <div class="animals-grid">
            <?php if (count($animals) > 0): ?>
                <?php foreach ($animals as $animal): ?>
                    <?php
                        $icon = match(strtolower($animal['type'])) {
                            'dog' => '🐕',
                            'cat' => '🐈',
                            'rabbit' => '🐰',
                            'bird' => '🦜',
                            default => '🐾'
                        };
                        $animalStatus = $animal['status'];
                        $isAvailable = $animalStatus === 'Available for Adoption';
                        $badgeClass = match($animalStatus) {
                            'Available for Adoption' => 'badge-available',
                            'Reserved'              => 'badge-reserved',
                            'Adopted'               => 'badge-adopted',
                            default                 => 'badge-available'
                        };
                        $badgeLabel = match($animalStatus) {
                            'Available for Adoption' => 'Available',
                            'Reserved'              => 'Reserved',
                            'Adopted'               => 'Adopted',
                            default                 => $animalStatus
                        };
                    ?>
                    <div class="animal-card <?php echo !$isAvailable ? 'card-unavailable' : ''; ?>">
                        <div class="animal-card-image">
                            <?php echo $icon; ?>
                            <span class="availability-badge <?php echo $badgeClass; ?>"><?php echo $badgeLabel; ?></span>
                        </div>
                        <div class="animal-card-content">
                            <div class="animal-name"><?php echo htmlspecialchars($animal['name']); ?></div>
                            <div class="animal-type"><?php echo htmlspecialchars($animal['type']); ?></div>
                            
                            <div class="animal-details">
                                <span class="detail-badge age"><?php echo htmlspecialchars($animal['age']); ?> years</span>
                                <span class="detail-badge gender"><?php echo htmlspecialchars($animal['gender']); ?></span>
                            </div>

                            <?php if ($isAvailable): ?>
                                <button class="adopt-btn" onclick="window.location.href='animal-detail.php?id=<?php echo $animal['id']; ?>'">
                                    View Details
                                </button>
                            <?php elseif ($animalStatus === 'Reserved'): ?>
                                <button class="adopt-btn adopt-btn-reserved" disabled>
                                    Currently Reserved
                                </button>
                            <?php else: ?>
                                <button class="adopt-btn adopt-btn-adopted" disabled>
                                    Already Adopted
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">🐾</div>
                    <h2>No animals available for adoption yet</h2>
                    <p>Please check back soon!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
