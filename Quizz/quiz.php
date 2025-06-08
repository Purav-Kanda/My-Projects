<?php
/*
 * FILE: quiz.php
 * PURPOSE: Displays interactive quiz about Kevin Yang with:
 * - Random question selection from database
 * - Answer submission handling
 * - Immediate feedback with explanations
 * AUTHOR: Purav Kanda
 */
$pageTitle = "Quiz App";
require_once 'config/db.php'; // Contains PDO database connection

// DATABASE OPERATION: Fetch questions
try {
    $stmt = $pdo->query("SELECT * FROM quiz_questions ORDER BY RAND() LIMIT 6"); 
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error loading questions: " . $e->getMessage());
}

$feedback = [];
$showResults = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $showResults = true;
    foreach ($questions as $question) {
        $qid = $question['id'];
        $userAnswer = $_POST['q'.$qid] ?? null;
        $isCorrect = ($userAnswer == $question['correct_option']);

        $feedback[$qid] = [
            'correct' => $isCorrect,
            'selected' => $userAnswer,
            'correct_option' => $question['correct_option'],
            'explanation' => $question['explanation']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .quiz-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .question-block {
            margin-bottom: 20px;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .correct { color: green; }
        .incorrect { color: red; }
    </style>
</head>
<body>

<main class="quiz-container">
    <h2>Learn more About me through a Quiz</h2>

    <?php if ($showResults): ?>
        <!-- RESULTS DISPLAY -->
        <div class="quiz-card">
            <h3>Quiz Results</h3>
            <?php 
            $correctCount = count(array_filter($feedback, fn($f) => $f['correct']));
            ?>
            <p>You got <?= $correctCount ?> out of <?= count($questions) ?> correct!</p>

            <?php foreach ($questions as $question): 
                $qid = $question['id'];
                $userAnswer = $feedback[$qid]['selected'];
                $correctIndex = $feedback[$qid]['correct_option'];
                $correctText = $question['option' . $correctIndex];
                $userText = is_numeric($userAnswer) ? $question['option' . $userAnswer] : 'No answer';
                $isCorrect = $feedback[$qid]['correct'];
            ?>
                <div class="question-block">
                    <p><strong><?= htmlspecialchars($question['question']) ?></strong></p>
                    <p>Your answer: <em><?= htmlspecialchars($userText) ?></em></p>
                    <?php if ($isCorrect): ?>
                        <p class="correct">✅ Correct!</p>
                    <?php else: ?>
                        <p class="incorrect">❌ Incorrect. Correct answer: <strong><?= htmlspecialchars($correctText) ?></strong></p>
                    <?php endif; ?>
                    <p><em>Explanation: <?= htmlspecialchars($feedback[$qid]['explanation']) ?></em></p>
                </div>
            <?php endforeach; ?>

            <a href="quiz.php" class="learn-more-btn">Try Again</a>
        </div>

    <?php else: ?>
        <!-- QUIZ FORM -->
        <form method="POST" class="quiz-card">
            <?php foreach ($questions as $question): ?>
                <div class="question-block">
                    <p><strong><?= htmlspecialchars($question['question']) ?></strong></p>
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <label>
                            <input type="radio" name="q<?= $question['id'] ?>" value="<?= $i ?>">
                            <?= htmlspecialchars($question['option' . $i]) ?>
                        </label><br>
                    <?php endfor; ?>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="submit-btn">Submit Answers</button>
        </form>
    <?php endif; ?>
</main>


</body>
</html>
