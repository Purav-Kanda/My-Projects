<!DOCTYPE html>
<!-- 
Author:Purav Kanda
projects.php - Project Portfolio Page
Displays Kevin's projects with filtering capabilities
Uses JavaScript for dynamic filtering and rendering
-->
<html lang="en">
<head>
    <!-- Standard meta tags and title -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purav's Projects</title>
      <!-- CSS imports -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Main site header with navigation -->
    
    <!-- Main content area -->    
    <main>
        <!-- Project filters section -->
        <section class="projects-intro">
        <h1 style="font-size: 3em;">Projects</h1>
        <p>Explore my collection of interactive web applications and coding projects</p>
            <!-- Filter buttons for project categories -->
            <div class="project-filters">
                <button class="filter-btn active" data-filter="all">All Projects</button>
                <button class="filter-btn" data-filter="game">Games</button>
                <button class="filter-btn" data-filter="web">Web Apps</button>
                <button class="filter-btn" data-filter="canvas">Canvas</button>
            </div>
        </section>
        <!-- Projects container (populated via JavaScript) -->
        <section class="projects-container" id="projects-container">
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i> Loading projects...
            </div>
        </section>
    </main>
    <!-- Project display and filtering logic -->
    <script>
        // Project data array - contains all project information
        const projects = [
            {
                title: "Memory Puzzle Game",
                folder: "memory puzzle/Assignment2.html",
                description: "The memory tile match game",
                category: "game",
                skills: [ "random pattern","flexible design"],
                icon: "chess-board",
                image: "images/memory.jpg"
            },
            {
                title: "Lottery & Quiz",
                folder: "lab 4.1",
                description: "lottery functionality and arithmetic",
                category: "web",
                skills: ["JavaScript", "DOM"],
                icon: "ticket-alt",
                image: "images/lab4.jpg"
            },
            {
                title: "Mini Games",
                folder: "lab 4.2/changestyle.html",
                description: "Quizzes with style and user controls",
                category: "web",
                skills: ["JavaScript", "CSS"],
                icon: "question-circle",
                image: "images/mini.jpg"
            },
            {
                title: "Game Collection",
                folder: "lab 5.1/catchthrabbit.html",
                description: "rabbit catching, calculator",
                category: "game",
                skills: ["JavaScript", "Game Logic"],
                icon: "gamepad",
                image: "images/lab51.jpg"
            },
            {
                title: "Canvas Experiments",
                folder: "lab 6.1/animatedlogo.html",
                description: "graffiti, games, and image tools",
                category: "canvas",
                skills: ["HTML5 Canvas", "JavaScript"],
                icon: "paint-brush",
                image: "images/lab61.jpg"
            },
            {
                title: "Canvas Animations",
                folder: "lab 6.2/animation.html",
                description: "Canvas animations and game improvements",
                category: "canvas",
                skills: ["Animation", "Game Physics"],
                icon: "running",
                image: "images/lab62.jpg"
            },
            {
                title: "Guessing Game",
                folder: "lab 7.1/guessing.html",
                description: "Interactive guessing game ",
                category: "game",
                skills: ["Game Logic", "Canvas"],
                icon: "dice",
                image: "images/guess.webp"
            },
            {
                title: "Wumpus Game",
                folder: "wumpus/index.php",
                description: "Classic wumpus game ",
                category: "game",
                skills: ["Game Logic", "HTML"],
                icon: "dice",
                image: "images/wumpus.webp"
            },
             {
                title: "Drawing App",
                folder: "draw/index.html",
                description: "Drawing canvas",
                category: "canvas",
                skills: ["Canvas", "HTML"],
                icon: "running",
                image: "images/draw.webp"
            },
            {
                title: "Quizz App",
                folder: "Quizz/quiz.php",
                description: "Editable Quizz App",
                category: "game",
                skills: ["PhP","AJAX", "HTML"],
                icon: "running",
                image: "images/quizz.jpg"
            }
        ];
        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', async function() {
            const projectsContainer = document.getElementById('projects-container');
            
            try {
                 // Simulate loading delay (for UX purposes)
                await new Promise(resolve => setTimeout(resolve, 500));
                
                 // Initialize project display and interactions
                renderProjects(projects);
                setupFilters();
                setupProjectInteractions();
                
            } catch (error) {
                showError();
            }
        });

         /* Renders all projects to the page */
        function renderProjects(projects) {
            const container = document.getElementById('projects-container');
            
            if (projects.length === 0) {
                container.innerHTML = '<div class="no-projects">No projects found</div>';
                return;
            }
            
            container.innerHTML = projects.map(project => `
                <div class="project-card" data-category="${project.category}">
                    <div class="project-image">
                        <img src="${project.image}" alt="${project.title}">
                        <div class="project-icon">
                            <i class="fas fa-${project.icon}"></i>
                        </div>
                    </div>
                    <div class="project-info">
                        <h3>${project.title}</h3>
                        <p>${project.description}</p>
                        <div class="project-skills">
                            ${project.skills.map(skill => `<span>${skill}</span>`).join('')}
                        </div>
                        <a href="${project.folder}" class="project-link">
                            <i class="fas fa-folder-open"></i> Open Project
                        </a>
                    </div>
                </div>
            `).join('');
        }

         /* Sets up filter button event handlers */
        function setupFilters() {
            document.querySelectorAll('.filter-btn').forEach(button => {
                button.addEventListener('click', function() {
                    document.querySelectorAll('.filter-btn').forEach(btn => 
                        btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    const filter = this.dataset.filter;
                    document.querySelectorAll('.project-card').forEach(card => {
                        card.style.display = (filter === 'all' || card.dataset.category === filter) ? 
                            'block' : 'none';
                    });
                });
            });
        }

        /* Adds hover and click interactions to project cards */
        function setupProjectInteractions() {
            document.querySelectorAll('.project-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    if (!e.target.closest('.project-link')) {
                        const link = this.querySelector('.project-link');
                        if (link) window.location.href = link.href;
                    }
                });
                
                card.addEventListener('mouseenter', function() {
                    this.querySelector('.project-info').style.opacity = '1';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.querySelector('.project-info').style.opacity = '0';
                });
            });
        }

         /* Displays error message if project loading fails */
        function showError() {
            document.getElementById('projects-container').innerHTML = `
                <div class="error">
                    <i class="fas fa-exclamation-triangle"></i> Error loading projects.
                    <button onclick="window.location.reload()">Refresh</button>
                </div>
            `;
        }
    </script>
</body>
</html>