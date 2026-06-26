<?php
session_start();
require_once 'config/db.php';
//Author:Purav Kanda

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username)) {
        $error = 'Username is required';
    } elseif (empty($password)) {
        $error = 'Password is required';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user) {
                if (password_verify($password, $user['password_hash'])) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_id'] = $user['id'];
                    
                    $pdo->prepare("UPDATE admin_users SET login_attempts = 0, last_login = NOW() WHERE id = ?")
                       ->execute([$user['id']]);
                    
                    session_regenerate_id(true);
                    
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $pdo->prepare("UPDATE admin_users SET login_attempts = login_attempts + 1, last_attempt = NOW() WHERE id = ?")
                       ->execute([$user['id']]);
                    $error = 'Invalid username or password';
                }
            } else {
                $error = 'Invalid username or password';
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $error = 'A system error occurred';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        
        h2 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 24px;
            font-size: 24px;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }
        
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .login-btn:hover {
            background-color: #2980b9;
        }
        .back-to-home-container {
    text-align: center;
    margin: 20px 0;
}

.back-to-home-btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: #2c3e50; /* Dark blue-gray */
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.back-to-home-btn:hover {
    background-color: #3498db; /* Bright blue on hover */
    border-color: #2980b9;
    transform: translateY(-2px);
}

.back-to-home-btn i {
    margin-right: 8px;
}
    </style>
</head>
<body>
    <div class="back-to-home-container">
    <a href="../index.php" class="back-to-home-btn">
        <i class="fas fa-arrow-left"></i> Back to Portfolio
    </a>
</div>
    <div class="login-container">
        <h2>Admin Login</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required
                       value="<?php echo htmlspecialchars($username); ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>
</body>
</html>