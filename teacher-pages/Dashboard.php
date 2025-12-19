
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz App - Teacher Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
    <header class="header">
        <div class="header-left">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h1>
                <i class="fas fa-graduation-cap"></i>
                <span>Quiz App</span>
            </h1>
        </div>
        
    </header>
    

<aside class="sidebar" id="sidebar">
        <nav>
            <ul class="nav-links">
                <li>
                    <a href="./Dashboard.php" class="active">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li>
                    <a href="create_category.php" >
                        <i class="fas fa-folder-plus"></i>
                        <span>Create Category</span>
                    </a>
                </li>
                
                <li>
                <a href="quizes.php" >
                    <i class="fas fa-edit"></i>
                    <span>Manage Quizzes</span>
                </a>
                </li>
                <li>
                    <a href="results.php">
                        <i class="fas fa-chart-bar"></i>
                        <span>Results</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
    <div class="overlay" id="overlay"></div>

    <main class="main-content" id="mainContent">
        <div class="welcome-container">
            <div class="welcome-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <h2>Welcome, Teacher!</h2>
            <p>Select an action from the sidebar to get started. You can create categories, design quizzes, manage existing quizzes, or view student results.</p>
            
            <div class="quick-actions">
                <a href="create_category.php" class="action-card">
                    <i class="fas fa-folder-plus"></i>
                    <span>New Category</span>
                </a>
                <a href="create_quiz.php" class="action-card">
                    <i class="fas fa-plus-circle"></i>
                    <span>New Quiz</span>
                </a>
                <a href="quizes.php" class="action-card">
                    <i class="fas fa-edit"></i>
                    <span>Manage Quizzes</span>
                </a>
                <a href="results.php" class="action-card">
                    <i class="fas fa-chart-bar"></i>
                    <span>View Results</span>
                </a>
            </div>
        </div>
    </main>

    <script src="../js/dashboard.js">
    </script>
</body>
</html>
