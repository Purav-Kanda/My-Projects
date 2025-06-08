<?php
session_start();
require_once '/config/db.php';
//Author:ByteME

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_question'])) {
            $stmt = $pdo->prepare("INSERT INTO quiz_questions 
                                  (question, option1, option2, option3, option4, correct_option, explanation) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['question'],
                $_POST['option1'],
                $_POST['option2'],
                $_POST['option3'],
                $_POST['option4'],
                $_POST['correct_option'],
                $_POST['explanation']
            ]);
            $success = "Question added successfully!";
        } 
        elseif (isset($_POST['update_question'])) {
            $stmt = $pdo->prepare("UPDATE quiz_questions SET 
                                  question = ?, option1 = ?, option2 = ?, option3 = ?, 
                                  option4 = ?, correct_option = ?, explanation = ? 
                                  WHERE id = ?");
            $stmt->execute([
                $_POST['question'],
                $_POST['option1'],
                $_POST['option2'],
                $_POST['option3'],
                $_POST['option4'],
                $_POST['correct_option'],
                $_POST['explanation'],
                $_POST['question_id']
            ]);
            $success = "Question updated successfully!";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
} 
elseif (isset($_GET['delete'])) {
    try {
        $pdo->prepare("DELETE FROM quiz_questions WHERE id = ?")
           ->execute([$_GET['delete']]);
        $success = "Question deleted successfully!";
    } catch (PDOException $e) {
        $error = "Could not delete question: " . $e->getMessage();
    }
}

$questions = $pdo->query("SELECT * FROM quiz_questions ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Quiz Questions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f5f7;
        }
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #2c3e50;
            color: white;
        }
        .logout-btn {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            background: #e74c3c;
            border-radius: 3px;
        }
        .quiz-form {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea.form-control {
            min-height: 100px;
        }
        .correct-option {
            background: #e8f5e9;
            padding: 10px;
            border-left: 3px solid #2e7d32;
        }
        .question-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        .question-table th, 
        .question-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .question-table th {
            background: #f2f2f2;
        }
        .action-link {
            color: #3498db;
            text-decoration: none;
            margin-right: 10px;
        }
        .action-link.delete {
            color: #e74c3c;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
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
    <div class="admin-header">
        <h2>Quiz Management Panel</h2>
        <a href="?logout" class="logout-btn">Logout</a>
    </div>

    <div class="admin-container">
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <h3><?php echo isset($_GET['edit']) ? 'Edit' : 'Add New'; ?> Question</h3>
        <form method="POST" class="quiz-form">
            <?php if (isset($_GET['edit'])): 
                $editQuestion = $pdo->prepare("SELECT * FROM quiz_questions WHERE id = ?");
                $editQuestion->execute([$_GET['edit']]);
                $question = $editQuestion->fetch();
            ?>
                <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="question">Question:</label>
                <textarea id="question" name="question" class="form-control" required><?php 
                    echo isset($question) ? htmlspecialchars($question['question']) : ''; 
                ?></textarea>
            </div>

            <?php for ($i = 1; $i <= 4; $i++): ?>
            <div class="form-group <?php echo (isset($question) && $question['correct_option'] == $i) ? 'correct-option' : ''; ?>">
                <label for="option<?php echo $i; ?>">Option <?php echo $i; ?>:</label>
                <input type="text" id="option<?php echo $i; ?>" name="option<?php echo $i; ?>" 
                       class="form-control" required
                       value="<?php echo isset($question) ? htmlspecialchars($question['option'.$i]) : ''; ?>">
                <label>
                    <input type="radio" name="correct_option" value="<?php echo $i; ?>" 
                        <?php echo (isset($question) && $question['correct_option'] == $i) ? 'checked' : ''; ?> required>
                    Correct Answer
                </label>
            </div>
            <?php endfor; ?>

            <div class="form-group">
                <label for="explanation">Explanation:</label>
                <textarea id="explanation" name="explanation" class="form-control" required><?php 
                    echo isset($question) ? htmlspecialchars($question['explanation']) : ''; 
                ?></textarea>
            </div>

            <div class="form-group">
                <button type="submit" name="<?php echo isset($_GET['edit']) ? 'update_question' : 'add_question'; ?>" 
                        style="padding: 10px 20px; background: #2c3e50; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    <?php echo isset($_GET['edit']) ? 'Update' : 'Save'; ?> Question
                </button>
                <?php if (isset($_GET['edit'])): ?>
                    <a href="quiz.php" style="margin-left: 10px;">Cancel</a>
                <?php endif; ?>
            </div>
        </form>

        <h3>Existing Questions</h3>
        <?php if (count($questions) > 0): ?>
            <table class="question-table">
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
                        <td><?php echo htmlspecialchars(substr($q['question'], 0, 50)); ?>...</td>
                        <td>Option <?php echo $q['correct_option']; ?></td>
                        <td>
                            <a href="quiz.php?edit=<?php echo $q['id']; ?>" class="action-link">Edit</a>
                            <a href="quiz.php?delete=<?php echo $q['id']; ?>" 
                               class="action-link delete"
                               onclick="return confirm('Are you sure you want to delete this question?');">
                               Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No questions found. Add your first question above.</p>
        <?php endif; ?>
    </div>
</body>
</html>