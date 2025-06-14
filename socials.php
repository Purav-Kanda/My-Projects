<?php
/*
Author:Purav Kanda
 * socials.php - Social Media Display Page
 * Fetches and displays Instagram and GitHub activity from database
 * Falls back to default data if database unavailable
 */
require_once 'config/db.php'; // Database configuration

try {
    // Fetch social media settings from database
    $stmt = $pdo->prepare("SELECT settings FROM social_settings LIMIT 1");
    $stmt->execute();
    $settings = $stmt->fetch();
    
    // Use database settings or fallback defaults
    $socialData = $settings ? json_decode($settings['settings'], true) : [
        'instagram_image' => 'images/instagram.jpg',
        'instagram_date' => date('F j, Y'),
        'github_repo' => 'portfolio-site',
        'github_activity' => "No recent activity",
        'github_date' => date('F j, Y')
    ];
} catch (PDOException $e) {
    // Fallback data if database error occurs
    $socialData = [
        'instagram_image' => 'images/instagram.jpg',
        'instagram_date' => date('F j, Y'),
        'github_repo' => 'portfolio-site',
        'github_activity' => "Content loading failed",
        'github_date' => date('F j, Y')
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Standard meta tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Socials</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Social media card styling */
        .social-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* Additional styles remain unchanged */
        .social-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        /* ... (rest of original CSS) ... */
    </style>
</head>
<body>
  
    <!-- Main content area -->
    <main class="container mt-4">
    <h1 style="font-size: 3em;">Socials</h1>
      
        <!-- GitHub card -->
        <div class="social-card">
            <h2><i class="fab fa-github"></i> GitHub Activity</h2>
            <h5><?= $socialData['github_repo'] ?></h5>
            <pre><?= $socialData['github_activity'] ?></pre>
            <p class="text-muted">Updated: <?= $socialData['github_date'] ?></p>
        </div>
    </main>

    <!-- Site footer -->
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Kevin Yang's Portfolio. All rights reserved.</p>
    </footer>
</body>
</html>