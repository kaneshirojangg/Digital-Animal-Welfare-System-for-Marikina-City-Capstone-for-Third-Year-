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

$result = $conn->query("SELECT id, name, type, age, gender, status, description, intake_date FROM animals WHERE status = 'Available for Adoption' ORDER BY intake_date DESC");

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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #2d3748;
        }
        
        .main-content {
            margin-left: 180px;
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
            color: #1e5c38;
            margin-bottom: 8px;
        }
        
        .page-header p {
            color: #4b5563;
            font-size: 1.05rem;
        }
        
        .animals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 28px;
            margin-top: 32px;
        }
        
        .animal-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }
        
        .animal-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 32px rgba(0,0,0,0.12);
        }
        
        .animal-card-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #2c7d4e 0%, #1e5c38 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: white;
        }
        
        .animal-card-content {
            padding: 20px;
        }
        
        .animal-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1e5c38;
            margin-bottom: 4px;
        }
        
        .animal-type {
            color: #4b5563;
            font-size: 0.9rem;
            margin-bottom: 12px;
        }
        
        .animal-details {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        
        .detail-badge {
            background: #f0f4f8;
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 0.85rem;
            color: #2d3748;
        }
        
        .detail-badge.gender {
            background: #fef3c7;
            color: #92400e;
        }
        
        .detail-badge.age {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .adopt-btn {
            width: 100%;
            padding: 12px;
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
        
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            color: #4b5563;
        }
        
        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 16px;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .page-header h1 {
                font-size: 1.8rem;
            }
            
            .animals-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
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
                    <div class="animal-card">
                        <div class="animal-card-image">
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
                        <div class="animal-card-content">
                            <div class="animal-name"><?php echo htmlspecialchars($animal['name']); ?></div>
                            <div class="animal-type"><?php echo htmlspecialchars($animal['type']); ?></div>
                            
                            <div class="animal-details">
                                <span class="detail-badge age">Age: <?php echo htmlspecialchars($animal['age']); ?></span>
                                <span class="detail-badge gender"><?php echo htmlspecialchars($animal['gender']); ?></span>
                            </div>
                            
                            <button class="adopt-btn" onclick="window.location.href='animal-detail.php?id=<?php echo $animal['id']; ?>'">
                                Adopt Me
                            </button>
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
