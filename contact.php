<?php
/*
 * contact.php - Contact Form Page
 * Handles message submissions and displays form status
 * Uses PDO for secure database operations
 * Author:Purav Kanda
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
    <title>Contact Purav Kanda</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js" defer></script>
</head>
<style>
    .contact-block {
  text-align: center;
  font-family: Arial, sans-serif;
  margin: 50px 0;
}

.contact-block .icon {
  color: #2962ff;
  margin-right: 8px;
}

.contact-block p {
  font-size: 18px;
  margin: 15px 0;
}

.social-icons {
  margin-top: 30px;
}

.circle-icon {
  display: inline-block;
  background-color: #2962ff;
  color: white;
  width: 48px;
  height: 48px;
  line-height: 48px;
  margin: 0 10px;
  border-radius: 50%;
  font-size: 20px;
  transition: background-color 0.3s;
}

.circle-icon:hover {
  background-color: #0039cb;
}

</style>
<body>
    <!-- Site header with navigation -->
   
    
    <!-- Main contact form section -->
    <main>
    <h1 style="font-size: 3em;">Contact Me</h1>

        <section class="contact-section">
            <p>Reach out to me for collaborations, inquiries, or networking.</p>
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

            <br>
            <br>

            <p><i class="fas fa-envelope icon"></i> kandap1@mcmaster.ca</p>
  <p><i class="fas fa-map-marker-alt icon"></i> Hamilton, Ontario, Canada</p>
  <p><i class="fas fa-university icon"></i> McMaster University</p>

  <div class="social-icons">
    <a href="https://github.com/Purav-Kanda/" class="circle-icon"><i class="fab fa-github"></i></a>
    <a href="https://www.linkedin.com/in/purav-kanda-971357296/" class="circle-icon"><i class="fab fa-linkedin-in"></i></a>
    <a href="https://www.instagram.com/purav__kanda/" class="circle-icon"><i class="fab fa-instagram"></i></a>
    <a href="mailto:kandap1@mcmaster.ca" class="circle-icon"><i class="fas fa-envelope"></i></a>
  </div>
  <br>
  <br>
      
        </section>
    </main>
    
    <!-- Site footer -->
</body>
</html>