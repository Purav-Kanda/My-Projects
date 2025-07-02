<?php
session_start();
require_once 'config/db.php';
//Author:ByteME

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        
        .dashboard-menu {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .dashboard-item {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .dashboard-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        
        .dashboard-item i {
            font-size: 2rem;
            color: var(--accent-color);
            margin-bottom: 1rem;
        }
        
        .dashboard-item h3 {
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }
        
        .dashboard-item p {
            color: #6c757d;
            margin-bottom: 1rem;
        }
        
        .dashboard-item a {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: var(--accent-color);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }
        
        .dashboard-item a:hover {
            background: #2980b9;
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
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                      
                        <li class="nav-item">
                            <a class="nav-link" href="adminsocials.php">
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
            
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard Overview</h1>
                </div>
                
                <div class="dashboard-menu">
                   
                    
                    <div class="dashboard-item">
                        <i class="fas fa-envelope"></i>
                        <h3>Contact Messages</h3>
                        <p>View and manage messages from your website visitors</p>
                        <a href="readcontact.php">View Messages</a>
                    </div>
                    
                    <div class="dashboard-item">
                        <i class="fas fa-info-circle"></i>
                        <h3>About Page</h3>
                        <p>Edit the content of your website's about page</p>
                        <a href="aboutedit.php">Edit About Page</a>
                    </div>

                     <div class="dashboard-item">
                        <i class="fas fa-info-circle"></i>
                        <h3>Manage Socials</h3>
                        <p>Edit the content of your website's Social page</p>
                        <a href="adminsocials.php">Edit Social Page</a>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>