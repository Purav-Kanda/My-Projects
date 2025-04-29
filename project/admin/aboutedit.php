<?php
session_start();
require_once 'config/db.php';
//Author:ByteME

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit; 
}

$about_content = '';
$skills = [];
$education = '';

$stmt = $pdo->query("SELECT * FROM about_page LIMIT 1");
if ($stmt) {
    $about_data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($about_data) {
        $about_content = $about_data['content'];
        $skills = json_decode($about_data['skills'], true) ?: [];
        $education = $about_data['education'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Editor</title>
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
        
        .edit-form {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        
        textarea.form-control {
            min-height: 100px;
        }
        
        .skills-container {
            margin-top: 1.5rem;
        }
        
        .skill-item {
            display: flex;
            margin-bottom: 1rem;
            align-items: center;
        }
        
        .skill-item input {
            flex-grow: 1;
            margin-right: 1rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: var(--accent-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-danger {
            background: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .btn-success {
            background: #2ecc71;
            color: white;
        }
        
        .btn-success:hover {
            background: #27ae60;
        }
        
        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
            display: none;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Admin Panel</h4>
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
                            <a class="nav-link active" href="aboutedit.php">
                                <i class="fas fa-info-circle"></i> About Page
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Edit About Page</h1>
                </div>
                
                <div id="message" class="alert"></div>
                
                <div class="edit-form">
                    <form id="aboutForm">
                        <div class="form-group">
                            <label for="aboutContent">Main Content:</label>
                            <textarea id="aboutContent" name="content" class="form-control" required><?= htmlspecialchars($about_content) ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="education">Education:</label>
                            <textarea id="education" name="education" class="form-control" required><?= htmlspecialchars($education) ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Skills:</label>
                            <div class="skills-container" id="skillsContainer">
                                <?php foreach ($skills as $index => $skill): ?>
                                    <div class="skill-item">
                                        <input type="text" name="skills[]" class="form-control" value="<?= htmlspecialchars($skill) ?>" required>
                                        <button type="button" class="btn btn-danger remove-skill">Remove</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" id="addSkill" class="btn btn-primary">Add Skill</button>
                        </div>
                        
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('addSkill').addEventListener('click', function() {
                const container = document.getElementById('skillsContainer');
                const div = document.createElement('div');
                div.className = 'skill-item';
                div.innerHTML = `
                    <input type="text" name="skills[]" class="form-control" required>
                    <button type="button" class="btn btn-danger remove-skill">Remove</button>
                `;
                container.appendChild(div);
            });

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-skill')) {
                    const skillItem = e.target.closest('.skill-item');
                    if (skillItem && document.querySelectorAll('.skill-item').length > 1) {
                        skillItem.remove();
                    } else {
                        showMessage('You must have at least one skill', 'error');
                    }
                }
            });

            document.getElementById('aboutForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Saving...';
                
                const formData = new FormData();
                const skills = [];
                
                formData.append('content', document.getElementById('aboutContent').value);
                formData.append('education', document.getElementById('education').value);
                
                document.querySelectorAll('input[name="skills[]"]').forEach(input => {
                    skills.push(input.value);
                });
                formData.append('skills', JSON.stringify(skills));
                
                fetch('save_about.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showMessage(data.message, 'success');
                    } else {
                        showMessage(data.message || 'Unknown error occurred', 'error');
                    }
                })
                .catch(error => {
                    showMessage('Error: ' + error.message, 'error');
                    console.error('Error:', error);
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Save Changes';
                });
            });

            function showMessage(message, type) {
                const msgDiv = document.getElementById('message');
                msgDiv.textContent = message;
                msgDiv.className = 'alert alert-' + type;
                msgDiv.style.display = 'block';
                
                setTimeout(() => {
                    msgDiv.style.display = 'none';
                }, 5000);
            }
        });
    </script>
</body>
</html>