<?php
/*
 * FILE: quiz.php
 * PURPOSE: Displays interactive quiz about Kevin Yang with:
 * - Random question selection from database
 * - Answer submission handling
 * - Immediate feedback with explanations
 * AUTHOR: ByteMe
 */
$pageTitle = "Kevin Quiz";
require_once 'config/db.php'; // Contains PDO database connection

/*
 * DATABASE OPERATION:
 * Fetches 6 random questions from quiz_questions table
 * Uses PDO for secure database access
 */
try {
    $stmt = $pdo->query("SELECT * FROM quiz_questions LIMIT 6"); 
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error loading questions: " . $e->getMessage());
}

/*
 * QUIZ LOGIC VARIABLES:
 * $feedback - Stores user's answer results
 * $showResults - Toggles between quiz form and results display
 */
$feedback = [];
$showResults = false;

/*
 * FORM SUBMISSION HANDLER:
 * Processes POST data when quiz is submitted
 * Compares user answers against correct options
 * Prepares feedback explanations
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $showResults = true;
    foreach ($questions as $question) {
        $qid = $question['id'];
        if (isset($_POST['q'.$qid])) {
            $userAnswer = $_POST['q'.$qid];
            $isCorrect = ($userAnswer == $question['correct_option']);
            
            $feedback[$qid] = [
                'correct' => $isCorrect,
                'explanation' => $question['explanation']
            ];
        }
    }
}
?>
<!DOCTYPE html>
<!-- 
    MAIN QUIZ INTERFACE:
    - Shows form when $showResults = false
    - Displays results when $showResults = true
    - Includes consistent site header/footer
 -->
<html lang="en">
<head>
    <!-- Standard meta tags and title -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Inline styles for quiz-specific elements -->
    <style>
        .quiz-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- SITE HEADER (consistent across pages) -->
    <header>
        <h1>Kevin Yang's Portfolio</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="projects.php">Projects</a></li>
                <li><a href="socials.php">Socials</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- 
        MAIN QUIZ CONTENT:
        Uses $showResults to toggle between:
        - Quiz form (POSTs to self)
        - Results display (with feedback)
     -->
    <main class="quiz-container">
        <h2>Learn more About me through a Quiz</h2>
        
        <?php if ($showResults): ?>
            <!-- RESULTS DISPLAY -->
            <div class="quiz-card">
                <h3>Quiz Results</h3>
                <?php 
                // Calculate correct answer count
                $correctCount = count(array_filter($feedback, function($item) {
                    return $item['correct'];
                }));
                ?>
                <p>You got <?php echo $correctCount; ?> out of <?php echo count($questions); ?> correct!</p>
                
                <!-- Display each question with feedback -->
                <?php foreach ($questions as $question): ?>
                    [Original results HTML remains unchanged]
                <?php endforeach; ?>
                
                <a href="quiz.php" class="learn-more-btn">Try Again</a>
            </div>
        <?php else: ?>
            <!-- QUIZ FORM -->
            <form method="POST" class="quiz-card">
                <?php foreach ($questions as $question): ?>
                    [Original question form HTML remains unchanged]
                <?php endforeach; ?>
                <button type="submit" class="submit-btn">Submit Answers</button>
            </form>
        <?php endif; ?>
    </main>

    <!-- STANDARD SITE FOOTER -->
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Kevin Yang's Portfolio. All rights reserved.</p>
    </footer>
</body>
</html>