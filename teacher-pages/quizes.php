<?php
session_start();
require_once __DIR__ . '/../includes/database.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../index.php");
    exit;
}

$teacher_id = (int) $_SESSION['teacher_id'];

$stmt = $DB->prepare("
    SELECT 
        q.id_quiz,
        q.titre_quiz,
        q.description,
        c.Nom_categorie,
        COUNT(qu.id_question) AS total_questions
    FROM quiz q
    JOIN categories c ON c.id_categories = q.id_categories
    LEFT JOIN questions qu ON qu.id_quiz = q.id_quiz
    WHERE q.id_enseignant = ?
    GROUP BY q.id_quiz
    ORDER BY q.created_at DESC
");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$quizzes = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Quiz - Quiz App</title>
    <link rel="stylesheet" href="../css/quizes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

    <a href="../logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
    </a>
</header>

<aside class="sidebar" id="sidebar">
    <nav>
        <ul class="nav-links">
            <li>
                <a href="./Dashboard.php">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
            </li>
            <li>
                <a href="create_category.php">
                    <i class="fas fa-folder-plus"></i>
                    <span>Create Category</span>
                </a>
            </li>
            <li>
                <a href="quizzes.php" class="active">
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

    <div class="page-header">
        <div class="page-title-section">
            <h2>Gestion des Quiz</h2>
            <p>Gérez et organisez vos quiz existants</p>
        </div>
        <a href="create_quiz.php" class="add-quiz-btn">
            <i class="fas fa-plus"></i>
            Ajouter un Quiz
        </a>
    </div>

    <div class="quiz-container">

        <?php if ($quizzes->num_rows > 0): ?>
            <?php while ($quiz = $quizzes->fetch_assoc()): ?>

                <div class="quiz-card">
                    <div class="quiz-header">
                        <div class="quiz-info">
                            <span class="quiz-category">
                                <?= htmlspecialchars($quiz['Nom_categorie']) ?>
                            </span>

                            <h3><?= htmlspecialchars($quiz['titre_quiz']) ?></h3>

                            <p>
                                <?= htmlspecialchars($quiz['description'] ?? 'Aucune description') ?>
                            </p>
                        </div>

                        <div class="quiz-actions">
                            <a href="edit_quiz.php?id=<?= $quiz['id_quiz'] ?>"
                               class="action-btn edit"
                               title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>

                            <a href="delete_quiz.php?id=<?= $quiz['id_quiz'] ?>"
                               class="action-btn delete"
                               title="Supprimer"
                               onclick="return confirm('Supprimer ce quiz ?');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>

                    <div class="quiz-stats">
                        <div class="stat-item">
                            <i class="fas fa-question-circle"></i>
                            <span><?= (int)$quiz['total_questions'] ?> questions</span>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>

            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <h3>Aucun quiz disponible</h3>
                <p>Commencez par créer votre premier quiz</p>
                <a href="create_quiz.php" class="add-quiz-btn">
                    <i class="fas fa-plus"></i>
                    Créer un Quiz
                </a>
            </div>

        <?php endif; ?>

    </div>
</main>

</body>
</html>


    <script>
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const mainContent = document.getElementById('mainContent');

        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            
            if (window.innerWidth > 768) {
                sidebar.classList.toggle('hidden');
                mainContent.classList.toggle('expanded');
            }
        });

        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });

        const navLinks = document.querySelectorAll('.nav-links a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                }
            });
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                overlay.classList.remove('active');
                sidebar.classList.remove('active');
            }
        });

        function handleLogout() {
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                window.location.href = 'logout.php';
            }
        }

        
    </script>
</body>
</html>
