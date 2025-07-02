<?php
require_once 'config/db.php';
//Author:purav kanda
// Default values
$about_content = "I am a web developer passionate about creating innovative solutions, you can checkout some of my projects in the projects tab";
$skills = ["PHP", "JavaScript", "HTML/CSS", "Database Management", "Python", "Java", "C", "Haskell","Ajax"];
$education = "Second year Honours Computer Science student at McMaster University. Proficient in Python, Java, C, Haskell and HTML/CSS";

// Try to fetch from database
try {
    $stmt = $pdo->query("SELECT * FROM about_page LIMIT 1");
    if ($stmt && $row = $stmt->fetch()) {
        $about_content = $row['content'];
        $skills = json_decode($row['skills'], true) ?: $skills;
        $education = $row['education'];
    }
} catch (PDOException $e) {
    // Use defaults if database fails
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
        <!-- Standard meta tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Kevin Yang</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Page-specific styles -->
    <style>
    .stats-bar {
        display: flex;
        justify-content: center;
        gap: 4rem;
        padding: 2rem 1rem;
        background-color: #f9f9f9;
        text-align: center;
        flex-wrap: wrap;
    }

.stats-bar .stat h3 {
  font-size: 2.5em;
  color: #2962ff; /* Blue tone */
  margin: 0;
}

.stats-bar .stat p {
  margin-top: 0.4rem;
  font-size: 1em;
  color: #666;
}

        .about-container {
            display: flex;
            gap: 40px;
            align-items: flex-start;
            margin-top: 30px;
        }
        .about-content {
            flex: 1;
            text-align: left;
        }
        .about-image {
            flex: 1;
            max-width: 400px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            height: auto;
            max-height: 500px;
            object-fit: cover;
        }
        .section-heading {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 15px;
            color: #333;
        }
        .section-heading::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 80px;
            height: 4px;
            background-color: #0071e3;
        }
        .skills-list {
            columns: 2;
            column-gap: 40px;
            margin: 20px 0;
        }
        .skills-list li {
            margin-bottom: 8px;
            position: relative;
            padding-left: 20px;
        }
        .download-resume {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background-color: #0071e3;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: all 0.3s ease;
            border: 2px solid #0071e3;
        }
        .download-resume:hover {
            background-color: transparent;
            color: #0071e3;
        }
        @media (max-width: 768px) {
            .about-container {
                flex-direction: column;
            }
            .about-image {
                max-width: 100%;
                order: -1;
            }
            .skills-list {
                columns: 1;
            }
        }
    </style>
</head>
<body>
    <!-- Site header with navigation -->
    <!-- Main content section -->
    <main>
  <section>
    <h1 style="font-size: 3em;">About Me</h1>
    
    <!-- Two-column layout: text + image -->
    <div class="about-container">
      
      <!-- Left Column: Text content -->
      <div class="about-content">
        <p><?= htmlspecialchars($about_content) ?></p>

        <!-- Skills -->
        <div class="about-details">
          <h3>Skills</h3>
          <ul class="skills-list">
            <?php foreach ($skills as $skill): ?>
              <li><?= htmlspecialchars($skill) ?></li>
            <?php endforeach; ?>
          </ul>

          <!-- Education -->
          <h3>Education</h3>
          <p><?= htmlspecialchars($education) ?></p>
        </div>
      </div>

      <!-- Right Column: Profile Image -->
      <div class="image-column">
        <img src="images/purav.jpg" alt="Purav Kanda" class="about-image" id="profile-image">
      </div>
    </div>

    <!-- Stats bar BELOW the two-column section -->
    <section class="stats-bar">
      <div class="stat">
        <h3>2nd</h3>
        <p>Year Student</p>
      </div>
     <!-- <div class="stat">
        <h3>3.6</h3>
        <p>GPA</p>
      </div> -->
      <div class="stat">
        <h3>10+</h3>
        <p>Projects</p>
      </div>
      <div class="stat">
        <a href="resume/resume.pdf" class="download-resume" download>Review &amp; Download Resume</a>
      </div>
    </section>
    
  </section>
</main>

</body>
</html>