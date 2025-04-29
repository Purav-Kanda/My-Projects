<?php
/*
 * contact.php - Contact Form Page
 * Handles message submissions and displays form status
 * Uses PDO for secure database operations
 * Author:ByteME
 */
require_once 'config/db.php'; // Database connection

// Initialize variables
$success = false;
$error = null;
$name = $email = $message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user inputs
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validate inputs
    if (empty($name) || empty($email) || empty($message)) {
        $error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";
    } else {
        // Save to database if validation passes
        try {
            $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message]);
            $success = true;
            $name = $email = $message = ''; // Clear form on success
        } catch(PDOException $e) {
            $error = "Failed to send message. Error: " . $e->getMessage();
            error_log($e->getMessage()); // Log to server error log
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Standard meta tags and title -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Kevin Yang</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js" defer></script>
</head>
<body>
    <!-- Site header with navigation -->
    <header>
        <h1>Kevin Yang's Portfolio</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="projects.php">Projects</a></li>
                <li><a href="socials.php" class="active">Socials</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>
    
    <!-- Main contact form section -->
    <main>
        <section class="contact-section">
            <h2>Get in Touch</h2>
            <p>Reach out to Kevin for collaborations, inquiries, or networking.</p>

            <!-- Form status messages -->
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <p>Thank you! Your message has been sent successfully.</p>
                </div>
            <?php elseif ($error): ?>
                <div class="alert alert-error">
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>

            <!-- Contact form -->
            <form method="POST" class="contact-form">
                <label for="name">Your Name:</label>
                <input type="text" id="name" name="name" required 
                       value="<?php echo htmlspecialchars($name); ?>">

                <label for="email">Your Email:</label>
                <input type="email" id="email" name="email" required
                       value="<?php echo htmlspecialchars($email); ?>">

                <label for="message">Your Message:</label>
                <textarea id="message" name="message" required><?php 
                    echo htmlspecialchars($message); 
                ?></textarea>

                <button type="submit" class="submit-btn">Send Message</button>
            </form>
        </section>
    </main>
    
    <!-- Site footer -->
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Kevin Yang's Portfolio. All rights reserved.</p>
    </footer>
</body>
</html>