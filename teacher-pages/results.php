<?php 
session_start();
require_once __DIR__ . '/../includes/database.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../index.php");
    exit;
}

$teacher_id = (int) $_SESSION['teacher_id'];

/*
 Fetch results ONLY for quizzes created by this teacher
*/
$stmt = $DB->prepare("
    SELECT
        u.Nom AS student_name,
        q.titre_quiz,
        r.score,
        r.total_questions,
        r.datepassage
    FROM resultat r
    JOIN quiz q ON q.id_quiz = r.id_quiz
    JOIN utilisateurs u ON u.id_utilisateur = r.id_etudiants
    WHERE q.id_enseignant = ?
    ORDER BY r.datepassage DESC
");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$results = $stmt->get_result();?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats des Étudiants - Quiz App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/resultats.css">
</head>
<body>
    <!-- Header -->
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
        <button class="logout-btn" onclick="handleLogout()">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </button>
    </header>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <nav>
            <ul class="nav-links">
                <li>
                    <a href="create_category.php">
                        <i class="fas fa-folder-plus"></i>
                        <span>Create Category</span>
                    </a>
                </li>
                <li>
                    <a href="create_quiz.php">
                        <i class="fas fa-plus-circle"></i>
                        <span>Create Quiz</span>
                    </a>
                </li>
                <li>
                    <a href="edit_quiz.php">
                        <i class="fas fa-edit"></i>
                        <span>Edit / Delete Quiz</span>
                    </a>
                </li>
                <li>
                    <a href="results.php" class="active">
                        <i class="fas fa-chart-bar"></i>
                        <span>Results</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Page Header -->
        <div class="page-header">
            <h2>Résultats des Étudiants</h2>
        </div>

        <!-- Results Table -->
        <div class="results-container">
            <div class="table-wrapper">
                <table class="results-table">
                    <thead>
                        <tr>
                            <th>Étudiant</th>
                            <th>Quiz</th>
                            <th>Score</th>
                            <th>Date</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                   <tbody>
<?php if ($results->num_rows > 0): ?>
    <?php while ($row = $results->fetch_assoc()): ?>
        <tr>
            <td>
                <div class="student-cell">
                    <div class="student-avatar">
                        <?= strtoupper(substr($row['student_name'], 0, 2)) ?>
                    </div>
                    <span class="student-name">
                        <?= htmlspecialchars($row['student_name']) ?>
                    </span>
                </div>
            </td>

            <td>
                <span class="quiz-link">
                    <?= htmlspecialchars($row['titre_quiz']) ?>
                </span>
            </td>

            <td>
                <span class="score">
                    <?= (int)$row['score'] ?>/<?= (int)$row['total_questions'] ?>
                </span>
            </td>

            <td>
                <span class="date">
                    <?= date('d M Y', strtotime($row['datepassage'])) ?>
                </span>
            </td>

            <td>
                <span class="status-badge">
                    <?= ($row['score'] >= ($row['total_questions'] / 2)) ? 'Réussi' : 'Échoué' ?>
                </span>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="5" style="text-align:center; padding:20px;">
            Aucun résultat disponible
        </td>
    </tr>
<?php endif; ?>
</tbody>

                </table>
            </div>
        </div>
    </main>

    <script>
        // Mobile menu toggle
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

        // Close sidebar on mobile when clicking links
        const navLinks = document.querySelectorAll('.nav-links a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                overlay.classList.remove('active');
                sidebar.classList.remove('active');
            }
        });

        // Logout function
        function handleLogout() {
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                window.location.href = 'logout.php';
            }
        }
    </script>
</body>
</html>