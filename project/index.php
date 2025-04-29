<!-- 
index.php - Kevin Yang's portfolio homepage
Author: ByteME
Main landing page with navigation and skills overview
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kevin Yang | Professional Portfolio</title>
    <meta name="description" content="Professional portfolio showcasing projects and skills">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="js/script.js" defer></script>
</head>
<body>
    <!-- Header with name and main navigation -->
    <header>
        <div class="header-content">
            <h1>Kevin Yang</h1>
            <p class="tagline">Software Developer & Creative Problem Solver</p>
        </div>
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

    <main>
        <!-- Hero section with primary CTAs -->
        <section class="hero">
            <div class="hero-content">
                <h2>Innovative Solutions Through Code</h2>
                <p class="hero-description">I build efficient, scalable applications.</p>
                <div class="cta-buttons">
                    <a href="projects.php" class="primary-btn">
                        <i class="fas fa-code"></i> View My Work
                    </a>
                    <a href="quiz.php" class="secondary-btn">
                        <i class="fas fa-gamepad"></i> Take My Quiz
                    </a>
                </div>
            </div>
        </section>
        
        <!-- Skills grid with 3 core competencies -->
        <section class="featured-skills">
            <h3 class="section-title">Core Competencies</h3>
            <div class="skills-grid">
                <div class="skill-card">
                    <i class="fas fa-laptop-code"></i>
                    <h4>Web Development</h4>
                </div>
                <!-- Additional skill cards... -->
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <span id="current-year"></span> Kevin Yang</p>
    </footer>
</body>
</html>