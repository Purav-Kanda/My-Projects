<?php
session_start();
require_once 'config/db.php';
//Author:Purav Kanda

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$success = $error = '';
$question_data = [
    'id' => '',
    'question' => '', 
    'option1' => '', 
    'option2' => '', 
    'option3' => '', 
    'option4' => '', 
    'correct_option' => 1, 
    'explanation' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [
            'question' => trim($_POST['question']),
            'option1' => trim($_POST['option1']),
            'option2' => trim($_POST['option2']),
            'option3' => trim($_POST['option3']),
            'option4' => trim($_POST['option4']),
            'correct_option' => (int)$_POST['correct_option'],
            'explanation' => trim($_POST['explanation'])
        ];

        foreach ($data as $key => $value) {
            if (empty($value) && $key !== 'id') {
                throw new Exception("All fields are required!");
            }
        }

        if (isset($_POST['add_question'])) {
            $stmt = $pdo->prepare("INSERT INTO quiz_questions 
                                  (question, option1, option2, option3, option4, correct_option, explanation) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(array_values($data));
            $_SESSION['success'] = "Question added successfully!";
            header('Location: adminquiz.php');
            exit;
        } 
        elseif (isset($_POST['update_question'])) {
            $data['id'] = (int)$_POST['question_id'];
            $stmt = $pdo->prepare("UPDATE quiz_questions SET 
                                  question = ?, option1 = ?, option2 = ?, option3 = ?, 
                                  option4 = ?, correct_option = ?, explanation = ? 
                                  WHERE id = ?");
            $stmt->execute(array_values($data));
            $_SESSION['success'] = "Question updated successfully!";
            header('Location: adminquiz.php');
            exit;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
} 
elseif (isset($_GET['delete'])) {
    try {
        $pdo->prepare("DELETE FROM quiz_questions WHERE id = ?")
           ->execute([(int)$_GET['delete']]);
        $_SESSION['success'] = "Question deleted successfully!";
        header('Location: adminquiz.php');
        exit;
    } catch (PDOException $e) {
        $error = "Could not delete question: " . $e->getMessage();
    }
}

if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $question_data = $stmt->fetch();
    if (!$question_data) {
        header('Location: adminquiz.php');
        exit;
    }
}

$questions = $pdo->query("SELECT * FROM quiz_questions ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Quizz</title>
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
        
        .quiz-form {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }
        
        .quiz-form label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .quiz-form textarea,
        .quiz-form input[type="text"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        
        .quiz-form .option-group {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .quiz-form .option-group.correct {
            background-color: #e8f5e9;
            border-left: 3px solid #2e7d32;
        }
        
        .quiz-form .btn-primary {
            background-color: var(--accent-color);
            border: none;
            padding: 0.75rem 1.5rem;
        }
        
        .quiz-form .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .questions-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .questions-table th,
        .questions-table td {
            padding: 1rem;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        .questions-table th {
            background-color: #f2f2f2;
            font-weight: 600;
        }
        
        .btn-edit {
            background-color: #ffc107;
            color: black;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
        }
        
        .btn-delete {
            background-color: var(--danger-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            margin-left: 0.5rem;
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
            <h4 class="mb-0">Admin Quizz</h4>
            <div>
                <a href="login.php" class="btn btn-sm btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </header>
    
    <div class="container-fluid">
        <div class="row">

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Quiz Management</h1>
                </div>
                
                <?php if ($success): ?>
                    <div class="alert-success">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert-error">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <div class="quiz-form">
                    <h3><?= isset($_GET['edit']) ? 'Edit' : 'Add New' ?> Question</h3>
                    <form method="POST">
                        <?php if (isset($_GET['edit'])): ?>
                            <input type="hidden" name="question_id" value="<?= $question_data['id'] ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="question" class="form-label">Question:</label>
                            <textarea id="question" name="question" class="form-control" required><?= htmlspecialchars($question_data['question']) ?></textarea>
                        </div>

                        <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="option-group <?= ($question_data['correct_option'] == $i) ? 'correct' : '' ?>">
                            <label for="option<?= $i ?>" class="form-label">Option <?= $i ?>:</label>
                            <input type="text" id="option<?= $i ?>" name="option<?= $i ?>" 
                                   class="form-control" required
                                   value="<?= htmlspecialchars($question_data['option'.$i]) ?>">
                            <div class="mt-2">
                                <input type="radio" name="correct_option" 
                                       value="<?= $i ?>" id="correct<?= $i ?>"
                                       <?= ($question_data['correct_option'] == $i) ? 'checked' : '' ?> required>
                                <label for="correct<?= $i ?>">Correct Answer</label>
                            </div>
                        </div>
                        <?php endfor; ?>

                        <div class="mb-3">
                            <label for="explanation" class="form-label">Explanation:</label>
                            <textarea id="explanation" name="explanation" class="form-control" required><?= htmlspecialchars($question_data['explanation']) ?></textarea>
                        </div>

                        <button type="submit" name="<?= isset($_GET['edit']) ? 'update_question' : 'add_question' ?>" 
                                class="btn btn-primary">
                            <?= isset($_GET['edit']) ? 'Update' : 'Save' ?> Question
                        </button>
                        <?php if (isset($_GET['edit'])): ?>
                            <a href="adminquiz.php" class="btn btn-secondary ms-2">Cancel</a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Existing Questions</h3>
                    </div>
                    <div class="card-body">
                        <?php if (count($questions) > 0): ?>
                            <div class="table-responsive">
                                <table class="questions-table">
                                    <thead>
                                        <tr>
                                            <th>Question</th>
                                            <th>Correct Answer</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($questions as $q): ?>
                                        <tr>
                                            <td><?= htmlspecialchars(substr($q['question'], 0, 50)) ?>...</td>
                                            <td>Option <?= $q['correct_option'] ?></td>
                                            <td>
                                                <a href="adminquiz.php?edit=<?= $q['id'] ?>" class="btn-edit">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="adminquiz.php?delete=<?= $q['id'] ?>" 
                                                   class="btn-delete"
                                                   onclick="return confirm('Are you sure you want to delete this question?');">
                                                   <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No questions found. Add your first question above.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('input[type="radio"][name="correct_option"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.option-group').forEach(group => {
                    group.classList.remove('correct');
                });
                this.closest('.option-group').classList.add('correct');
            });
        });
    </script>
</body>
</html>