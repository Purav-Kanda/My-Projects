<?php
session_start();
require_once 'config/db.php';
//Author:ByteME
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

ini_set('display_errors', 0);
error_reporting(0);

try {
    $content = $_POST['content'] ?? '';
    $education = $_POST['education'] ?? '';
    $skills = $_POST['skills'] ?? '[]';
    
    if (empty($content) || empty($education) || $skills === '[]') {
        throw new Exception('All fields are required');
    }

    $skillsArray = json_decode($skills, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid skills format');
    }
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM about_page");
    $stmt->execute();
    $exists = $stmt->fetchColumn();
    
    if ($exists) {
        $stmt = $pdo->prepare("UPDATE about_page SET content = ?, skills = ?, education = ?, updated_at = NOW()");
        $stmt->execute([$content, $skills, $education]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO about_page (content, skills, education) VALUES (?, ?, ?)");
        $stmt->execute([$content, $skills, $education]);
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'About page updated successfully'
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}