<?php
session_start();
require_once 'config/db.php';
//Author:ByteME

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['message'] = "Message deleted successfully!";
    header('Location: readcontact.php');
    exit;
}

$messages = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Messages</title>
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
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            border-top: none;
        }
        
        .table td, .table th {
            vertical-align: middle;
            padding: 1rem;
        }
        
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
        
        .action-btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 4px;
        }
        
        .btn-view {
            background: var(--accent-color);
            color: white;
        }
        
        .btn-delete {
            background: var(--danger-color);
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #dee2e6;
        }
        
        .message-preview {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .message-modal .modal-body {
            white-space: pre-wrap;
        }
        
        .status-badge {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .status-read {
            background-color: #28a745;
        }
        
        .status-unread {
            background-color: #dc3545;
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
                            <a class="nav-link" href="adminsocials.php">
                                <i class="fas fa-share-alt"></i> Social Media
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="readcontact.php">
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
                    <h1 class="h2">Contact Messages</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                       
                    </div>
                </div>
                
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-envelope-open-text me-2"></i>All Messages</span>
                        <span class="badge bg-primary"><?= count($messages) ?> total</span>
                    </div>
                    
                    <div class="card-body">
                        <?php if (count($messages) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="5%">ID</th>
                                            <th width="15%">Name</th>
                                            <th width="20%">Email</th>
                                            <th width="40%">Message</th>
                                            <th width="15%">Date</th>
                                            <th width="5%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($messages as $message): ?>
                                        <tr>
                                            <td><?= $message['id'] ?></td>
                                            <td><?= htmlspecialchars($message['name']) ?></td>
                                            <td><a href="mailto:<?= $message['email'] ?>"><?= $message['email'] ?></a></td>
                                            <td class="message-preview" title="<?= htmlspecialchars($message['message']) ?>">
                                                <?= htmlspecialchars(substr($message['message'], 0, 50)) ?>
                                                <?php if (strlen($message['message']) > 50): ?>...<?php endif; ?>
                                            </td>
                                            <td><?= date('M j, Y g:i a', strtotime($message['created_at'])) ?></td>
                                            <td>
                                                <div class="d-flex">
                                                   
                                                    <a href="readcontact.php?delete=<?= $message['id'] ?>" 
                                                       class="btn btn-sm btn-delete action-btn"
                                                       onclick="return confirm('Are you sure you want to delete this message?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-envelope-open-text fa-4x"></i>
                                <h3>No messages yet</h3>
                                <p class="text-muted">All contact messages will appear here when users contact you.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
   

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var messageModal = document.getElementById('messageModal');
            messageModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                
                document.getElementById('modal-name').textContent = button.getAttribute('data-name');
                document.getElementById('modal-email').textContent = button.getAttribute('data-email');
                document.getElementById('modal-date').textContent = button.getAttribute('data-date');
                document.getElementById('modal-message').textContent = button.getAttribute('data-message');
                
                var replyBtn = document.getElementById('modal-reply-btn');
                replyBtn.setAttribute('href', 'mailto:' + button.getAttribute('data-email'));
            });
        });
    </script>
</body>
</html>