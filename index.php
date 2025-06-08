<!-- 
index.php - Purav Kanda's portfolio homepage
Author: Purav Kanda
Main landing page with navigation and skills overview
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purav Kanda | Professional Portfolio</title>
    <meta name="description" content="Professional portfolio showcasing projects and skills">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="js/script.js" defer></script>
</head>
<style>
 /* Font Awesome (required for icons) */
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

.skills-section {
  font-family: 'Segoe UI', sans-serif;
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.skill-category {
  margin-bottom: 30px;
}

.category-title {
  color: #2c3e50;
  font-size: 1.3rem;
  border-bottom: 2px solid #eee;
  padding-bottom: 8px;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.skills-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 15px;
}

.skill-item {
  background: white;
  border-radius: 8px;
  padding: 15px 10px;
  text-align: center;
  box-shadow: 0 3px 10px rgba(0,0,0,0.08);
  transition: transform 0.2s;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.skill-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.skill-item i {
  font-size: 2rem;
  color: #3498db;
}

.skill-item span {
  font-weight: 600;
  color: #333;
}
.haskell-text-icon {
  background: #3498db;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: grid;
  place-items: center;
  font-weight: bold;
}
</style>
<body>
    <!-- Header with name and main navigation -->
    <header>
        <div class="header-content">
            <h1>Portfolio</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="#skills">About</a></li>
                <li><a href="#skillT">Skills</a></li>
                <li><a href="#proj">Projects</a></li>
                <li><a href="#socials" class="active">Socials</a></li>
                <li><a href="#msg">Contact Me</a></li>

            </ul>
        </nav>
    </header>

    <main><!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1 style="font-size: 3em;">Hi, I'm <span style="color:rgb(255, 234, 0);">Purav Kanda</span></h1>
        <h2>Computer Science Student at McMaster University</h2>
        <p class="hero-description">Passionate about coding, problem-solving, building innovative solutions and web apps.</p>
        <div class="cta-buttons">
            <a href="#proj" class="primary-btn">
                <i class="fas fa-code"></i> View My Work
            </a>
            <a href="#msg" class="secondary-btn">
                <i class="fas fa-comments"></i> Contact Me
            </a>
        </div>
    </div>
</section>

<section id="skills" class="featured-skills">
    <?php include 'about.php'; ?>
</section>

<h1 style="font-size: 3em;">Technical Skills</h1>

<!-- Technical Skills with Icons -->
<section class="skills-section" id="skillT">

  <!-- Programming Languages -->
  <div class="skill-category">
    <h2 class="category-title">
      <i class="fas fa-code"></i> Programming Languages
    </h2>
    <div class="skills-grid">
      <div class="skill-item">
        <i class="fab fa-python"></i>
        <span>Python</span>
      </div>
      <div class="skill-item">
        <i class="fab fa-java"></i>
        <span>Java</span>
      </div>

      <div class="skill-item">
        <i class="fab fa-js-square"></i>
        <span>JavaScript</span>
      </div>
      <div class="skill-item">
        <i class="fas fa-copyright"></i>
        <span>C</span>
      </div>
      <div class="skill-item">
  <div class="haskell-text-icon">Hs</div>
  <span>Haskell</span>
</div>
    </div>
  </div>

  <!-- Web Technologies -->
  <div class="skill-category">
    <h2 class="category-title">
      <i class="fas fa-globe"></i> Web Technologies
    </h2>
    <div class="skills-grid">
      <div class="skill-item">
        <i class="fab fa-html5"></i>
        <span>HTML</span>
      </div>
      <div class="skill-item">
        <i class="fab fa-css3-alt"></i>
        <span>CSS</span>
      </div>
      <div class="skill-item">
        <i class="fas fa-code-branch"></i>
        <span>AJAX</span>
      </div>
      <div class="skill-item">
        <i class="fab fa-php"></i>
        <span>PHP</span>
      </div>
    </div>
  </div>

  <!-- Tools & Technologies -->
  <div class="skill-category">
    <h2 class="category-title">
      <i class="fas fa-tools"></i> Tools & Technologies
    </h2>
    <div class="skills-grid">
      <div class="skill-item">
        <i class="fab fa-git-alt"></i>
        <span>Git</span>
      </div>
      <div class="skill-item">
        <i class="fas fa-database"></i>
        <span>SQL</span>
      </div>

    </div>
  </div>
  <p id="proj"></p>
  <?php include 'projects.php'; ?>

  <p id="socials"></p>
  <?php include 'socials.php' ?>

  <p id="msg"></p>
  <?php include 'contact.php' ?>

</section>



</section>





    </main>

<footer style="background-color: #333; color: white; padding: 10px;">
    <p>&copy; <span id="current-year"></span>2025 Purav Kanda. All rights reserved.</p>
</footer>

</body>
</html>