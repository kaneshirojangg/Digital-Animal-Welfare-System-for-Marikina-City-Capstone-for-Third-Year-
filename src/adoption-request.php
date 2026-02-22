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

$animalId = isset($_GET['animal_id']) ? intval($_GET['animal_id']) : 0;

if ($animalId <= 0) {
    header("Location: adopt-animal.php");
    exit();
}

$stmt = $conn->prepare("SELECT id, name, type, age, gender FROM animals WHERE id = ? AND status = 'Available for Adoption'");
$stmt->bind_param("i", $animalId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header("Location: adopt-animal.php");
    exit();
}

$animal = $result->fetch_assoc();
$stmt->close();

$submitted = false;
$error = false;
$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $applicantName = trim($_POST['applicant_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postalCode = trim($_POST['postal_code'] ?? '');
    $employment = trim($_POST['employment'] ?? '');
    $homeType = trim($_POST['home_type'] ?? '');
    $homeOwnership = trim($_POST['home_ownership'] ?? '');
    $rentalPermission = isset($_POST['rental_permission']) ? 1 : 0;
    $haveYard = isset($_POST['have_yard']) && $_POST['have_yard'] !== '' ? $_POST['have_yard'] : null;
    $otherPets = trim($_POST['other_pets'] ?? '');
    $children = isset($_POST['children']) ? 1 : 0;
    $childrenAges = trim($_POST['children_ages'] ?? '');
    $whyAdopt = trim($_POST['why_adopt'] ?? '');
    $reference1Name = trim($_POST['reference1_name'] ?? '');
    $reference1Phone = trim($_POST['reference1_phone'] ?? '');
    $reference2Name = trim($_POST['reference2_name'] ?? '');
    $reference2Phone = trim($_POST['reference2_phone'] ?? '');

    if (empty($applicantName) || empty($email) || empty($phone) || empty($address) || 
        empty($employment) || empty($homeType) || empty($whyAdopt)) {
        $error = true;
        $errorMsg = "Please fill in all required fields.";
    } else {

        $insertStmt = $conn->prepare("INSERT INTO adoptions (animal_id, applicant_name, email, phone, address, city, postal_code, employment, home_type, home_ownership, rental_permission, have_yard, other_pets_info, has_children, children_ages, adoption_reason, reference1_name, reference1_phone, reference2_name, reference2_phone, status, request_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())");
        
        $insertStmt->bind_param("isssssssssisssssss", 
            $animalId, $applicantName, $email, $phone, $address, $city, $postalCode, 
            $employment, $homeType, $homeOwnership, $rentalPermission, $haveYard, 
            $otherPets, $children, $childrenAges, $whyAdopt, $reference1Name, 
            $reference1Phone, $reference2Name, $reference2Phone);
        
        if ($insertStmt->execute()) {
            $submitted = true;
        } else {
            $error = true;
            $errorMsg = "Failed to submit adoption request. Please try again.";
        }
        $insertStmt->close();
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
    <title>Adoption Application - Marikina A&W</title>
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
            margin-left: 80px;
            padding: 40px;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #2c7d4e;
            text-decoration: none;
            margin-bottom: 24px;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: #1e5c38;
        }
        
        .form-header {
            background: linear-gradient(135deg, #2c7d4e 0%, #1e5c38 100%);
            color: white;
            padding: 40px;
            border-radius: 12px;
            margin-bottom: 40px;
        }
        
        .form-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            margin-bottom: 8px;
        }
        
        .animal-info {
            background: rgba(255,255,255,0.15);
            padding: 16px;
            border-radius: 8px;
            margin-top: 20px;
            backdrop-filter: blur(10px);
        }
        
        .animal-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-top: 12px;
        }
        
        .animal-info-item {
            font-size: 0.95rem;
        }
        
        .animal-info-label {
            opacity: 0.9;
            font-size: 0.85rem;
        }
        
        .success-message {
            background: #dcfce7;
            color: #166534;
            padding: 24px;
            border-radius: 12px;
            border-left: 4px solid #16a34a;
            margin-bottom: 24px;
        }
        
        .success-message h2 {
            margin-bottom: 8px;
        }
        
        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 16px;
            border-radius: 12px;
            border-left: 4px solid #dc2626;
            margin-bottom: 24px;
        }
        
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }
        
        .form-section {
            margin-bottom: 32px;
        }
        
        .form-section:last-child {
            margin-bottom: 0;
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e5c38;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .form-grid.full {
            grid-template-columns: 1fr;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #2d3748;
            font-size: 0.95rem;
        }
        
        .required {
            color: #dc2626;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select,
        textarea {
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            transition: border-color 0.3s ease;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #2c7d4e;
            box-shadow: 0 0 0 3px rgba(44, 125, 78, 0.1);
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        
        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #2c7d4e;
        }
        
        .checkbox-group label {
            margin: 0;
            cursor: pointer;
            font-weight: 400;
        }
        
        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 8px;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        input[type="radio"] {
            cursor: pointer;
            accent-color: #2c7d4e;
        }
        
        .form-actions {
            display: flex;
            gap: 16px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }
        
        button {
            padding: 14px 32px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .btn-submit {
            background: #2c7d4e;
            color: white;
            flex: 1;
        }
        
        .btn-submit:hover {
            background: #1e5c38;
        }
        
        .btn-cancel {
            background: #e2e8f0;
            color: #2d3748;
        }
        
        .btn-cancel:hover {
            background: #cbd5e1;
        }
        
        .helper-text {
            font-size: 0.85rem;
            color: #4b5563;
            margin-top: 4px;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .form-header {
                padding: 24px;
            }
            
            .form-header h1 {
                font-size: 1.6rem;
            }
            
            .animal-info-grid {
                grid-template-columns: 1fr;
            }
            
            .form-container {
                padding: 20px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include 'nav-menu.php'; ?>
    
    <div class="main-content">
        <a href="animal-detail.php?id=<?php echo $animal['id']; ?>" class="back-link">← Back to <?php echo htmlspecialchars($animal['name']); ?></a>
        
        <?php if ($submitted): ?>
            <div class="success-message">
                <h2>✓ Application Submitted Successfully!</h2>
                <p>Thank you for your adoption application for <strong><?php echo htmlspecialchars($animal['name']); ?></strong>. Our staff will review your application and contact you shortly. Please check your email for updates.</p>
            </div>
            <div style="text-align: center; margin-top: 32px;">
                <a href="adopt-animal.php" style="color: #2c7d4e; text-decoration: none; font-weight: 600;">← Back to Animals</a>
            </div>
        <?php else: ?>
            <div class="form-header">
                <h1>Adoption Application</h1>
                <p>Let's find <?php echo htmlspecialchars($animal['name']); ?> the perfect home!</p>
                <div class="animal-info">
                    <div style="font-size: 0.9rem; opacity: 0.9;">Animal Information</div>
                    <div class="animal-info-grid">
                        <div class="animal-info-item">
                            <div class="animal-info-label">Name</div>
                            <strong><?php echo htmlspecialchars($animal['name']); ?></strong>
                        </div>
                        <div class="animal-info-item">
                            <div class="animal-info-label">Type</div>
                            <strong><?php echo htmlspecialchars($animal['type']); ?></strong>
                        </div>
                        <div class="animal-info-item">
                            <div class="animal-info-label">Age</div>
                            <strong><?php echo htmlspecialchars($animal['age']); ?></strong>
                        </div>
                        <div class="animal-info-item">
                            <div class="animal-info-label">Gender</div>
                            <strong><?php echo htmlspecialchars($animal['gender']); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message">
                    ⚠️ <?php echo htmlspecialchars($errorMsg); ?>
                </div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="POST" action="">
                    <!-- Personal Information -->
                    <div class="form-section">
                        <div class="section-title">👤 Personal Information</div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Full Name <span class="required">*</span></label>
                                <input type="text" name="applicant_name" required value="<?php echo htmlspecialchars($_POST['applicant_name'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Email <span class="required">*</span></label>
                                <input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Phone Number <span class="required">*</span></label>
                                <input type="tel" name="phone" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>City <span class="required">*</span></label>
                                <input type="text" name="city" required value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>">
                            </div>
                            <div class="form-group full">
                                <label>Address <span class="required">*</span></label>
                                <input type="text" name="address" required value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Postal Code <span class="required">*</span></label>
                                <input type="text" name="postal_code" value="<?php echo htmlspecialchars($_POST['postal_code'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Employment -->
                    <div class="form-section">
                        <div class="section-title">💼 Employment</div>
                        <div class="form-grid full">
                            <div class="form-group">
                                <label>Employment Status <span class="required">*</span></label>
                                <input type="text" name="employment" required placeholder="e.g., Employed, Self-employed, Retired" value="<?php echo htmlspecialchars($_POST['employment'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Housing -->
                    <div class="form-section">
                        <div class="section-title">🏠 Housing Information</div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Home Type <span class="required">*</span></label>
                                <select name="home_type" required>
                                    <option value="">-- Select --</option>
                                    <option value="House" <?php echo ($_POST['home_type'] ?? '') === 'House' ? 'selected' : ''; ?>>House</option>
                                    <option value="Apartment" <?php echo ($_POST['home_type'] ?? '') === 'Apartment' ? 'selected' : ''; ?>>Apartment</option>
                                    <option value="Condo" <?php echo ($_POST['home_type'] ?? '') === 'Condo' ? 'selected' : ''; ?>>Condo</option>
                                    <option value="Townhouse" <?php echo ($_POST['home_type'] ?? '') === 'Townhouse' ? 'selected' : ''; ?>>Townhouse</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Home Ownership <span class="required">*</span></label>
                                <select name="home_ownership" required>
                                    <option value="">-- Select --</option>
                                    <option value="Own" <?php echo ($_POST['home_ownership'] ?? '') === 'Own' ? 'selected' : ''; ?>>Own</option>
                                    <option value="Rent" <?php echo ($_POST['home_ownership'] ?? '') === 'Rent' ? 'selected' : ''; ?>>Rent</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-grid full">
                            <div class="checkbox-group">
                                <input type="checkbox" id="rental_permission" name="rental_permission" <?php echo isset($_POST['rental_permission']) ? 'checked' : ''; ?>>
                                <label for="rental_permission">Landlord/Owner permits pets (if renting)</label>
                            </div>
                            <div class="checkbox-group">
                                <input type="checkbox" id="have_yard" name="have_yard" value="yes" <?php echo ($_POST['have_yard'] ?? '') === 'yes' ? 'checked' : ''; ?>>
                                <label for="have_yard">Do you have a yard?</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Household -->
                    <div class="form-section">
                        <div class="section-title">👨‍👩‍👧‍👦 Household</div>
                        <div class="form-grid full">
                            <div class="form-group">
                                <label>Do you have children?</label>
                                <div class="radio-group">
                                    <div class="radio-option">
                                        <input type="radio" id="children_yes" name="children" value="1" <?php echo isset($_POST['children']) && $_POST['children'] === '1' ? 'checked' : ''; ?>>
                                        <label for="children_yes" style="margin: 0;">Yes</label>
                                    </div>
                                    <div class="radio-option">
                                        <input type="radio" id="children_no" name="children" value="0" <?php echo !isset($_POST['children']) || $_POST['children'] === '0' ? 'checked' : ''; ?>>
                                        <label for="children_no" style="margin: 0;">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group full">
                                <label>If yes, please provide ages</label>
                                <input type="text" name="children_ages" placeholder="e.g., 5, 8, 12" value="<?php echo htmlspecialchars($_POST['children_ages'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pets -->
                    <div class="form-section">
                        <div class="section-title">Current Pets</div>
                        <div class="form-grid full">
                            <div class="form-group">
                                <label>Do you have other pets? If yes, please describe</label>
                                <textarea name="other_pets" placeholder="Type, breed, age, temperament..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Motivation -->
                    <div class="form-section">
                        <div class="section-title">💭 Adoption Motivation</div>
                        <div class="form-grid full">
                            <div class="form-group">
                                <label>Why do you want to adopt <?php echo htmlspecialchars($animal['name']); ?>? <span class="required">*</span></label>
                                <textarea name="why_adopt" required placeholder="Tell us about your reasons and what you can offer this animal..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- References -->
                    <div class="form-section">
                        <div class="section-title">📞 References</div>
                        <p class="helper-text" style="margin-bottom: 20px;">Please provide at least one reference (friend, family, or veterinarian)</p>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Reference 1 Name</label>
                                <input type="text" name="reference1_name" value="<?php echo htmlspecialchars($_POST['reference1_name'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Reference 1 Phone</label>
                                <input type="tel" name="reference1_phone" value="<?php echo htmlspecialchars($_POST['reference1_phone'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Reference 2 Name</label>
                                <input type="text" name="reference2_name" value="<?php echo htmlspecialchars($_POST['reference2_name'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Reference 2 Phone</label>
                                <input type="tel" name="reference2_phone" value="<?php echo htmlspecialchars($_POST['reference2_phone'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="window.location.href='animal-detail.php?id=<?php echo $animal['id']; ?>'">Cancel</button>
                        <button type="submit" class="btn-submit">✓ Submit Application</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
