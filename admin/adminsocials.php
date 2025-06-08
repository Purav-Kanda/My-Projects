<?php
session_start();
require_once 'config/db.php';
//Author:ByteME

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Load or initialize settings
$stmt = $pdo->prepare("SELECT * FROM social_settings LIMIT 1");
$stmt->execute();
$settings = $stmt->fetch();

$socialData = $settings ? json_decode($settings['settings'], true) : [
    'instagram_image' => 'images/instagram.jpg',
    'instagram_date' => date('F j, Y'),
    'github_repo' => 'portfolio-site',
    'github_activity' => "Updated contact form\nImproved mobile layout",
    'github_date' => date('F j, Y')
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $newData = [
            'instagram_image' => trim($_POST['instagram_image']),
            'instagram_date' => trim($_POST['instagram_date']),
            'github_repo' => trim($_POST['github_repo']),
            'github_activity' => trim($_POST['github_activity']),
            'github_date' => trim($_POST['github_date'])
        ];

        $jsonData = json_encode($newData);

        if ($settings) {
            $stmt = $pdo->prepare("UPDATE social_settings SET settings = ? WHERE id = ?");
            $stmt->execute([$jsonData, $settings['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO social_settings (settings) VALUES (?)");
            $stmt->execute([$jsonData]);
        }

        $_SESSION['success'] = "Social media updated successfully!";
        header('Location: adminsocials.php');
        exit;
    } catch (Exception $e) {
        $error = "Error saving changes: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Socials</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --danger-color: #e74c3c;
            --light-color: #ecf0f1;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .admin-header {
            background: var(--primary-color);
            color: white;
            padding: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar {
            background: var(--secondary-color);
            color: white;
            min-height: calc(100vh - 56px);
            padding: 0;
        }
        
        .sidebar .nav-link {
            color: var(--light-color);
            padding: 0.75rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            border-left: 3px solid var(--accent-color);
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            padding: 2rem;
        }
        
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
            padding: 1.25rem 1.5rem;
        }
        
        .social-form {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }
        
        .social-form label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .social-form textarea,
        .social-form input[type="text"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        
        .social-form .btn-primary {
            background-color: var(--accent-color);
            border: none;
            padding: 0.75rem 1.5rem;
        }
        
        .social-form .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .img-preview {
            max-width: 200px;
            margin-top: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .alert-success {
            padding: 1rem;
            background: #d4edda;
            color: #155724;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }
        
        .alert-error {
            padding: 1rem;
            background: #f8d7da;
            color: #721c24;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Admin Dashboard</h4>
            <div>
                <a href="login.php" class="btn btn-sm btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </header>
    
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block sidebar">
                <div class="sidebar-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="adminquiz.php">
                                <i class="fas fa-question-circle"></i> Manage Quiz
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="adminsocials.php">
                                <i class="fas fa-share-alt"></i> Social Media
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="readcontact.php">
                                <i class="fas fa-envelope"></i> Messages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="aboutedit.php">
                                <i class="fas fa-info-circle"></i> About Page
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Social Media Settings</h1>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="POST">
           
            <div class="mb-3">
                <label class="form-label">GitHub Repository</label>
                <input type="text" class="form-control" name="github_repo" 
                       value="<?= htmlspecialchars($socialData['github_repo']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">GitHub Activity</label>
                <textarea class="form-control" name="github_activity" rows="3" required><?= 
                    htmlspecialchars($socialData['github_activity']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">GitHub Update Date</label>
                <input type="text" class="form-control" name="github_date" 
                       value="<?= htmlspecialchars($socialData['github_date']) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple image preview update
        document.getElementById('instagram_image').addEventListener('change', function() {
            const preview = document.querySelector('.img-preview');
            if (preview) {
                preview.src = this.value;
            }
        });
    </script>
</body>
</html>

socials.php