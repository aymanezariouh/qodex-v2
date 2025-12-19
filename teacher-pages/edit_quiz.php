<?php
session_start();
require_once __DIR__ . '/../includes/database.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../index.php");
    exit;
}

$teacher_id = (int) $_SESSION['teacher_id'];
$quiz_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($quiz_id <= 0) {
    header("Location: quizes.php");
    exit;
}

$stmt = $DB->prepare(
    "SELECT * FROM quiz 
     WHERE id_quiz = ? AND id_enseignant = ?"
);
$stmt->bind_param("ii", $quiz_id, $teacher_id);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();

if (!$quiz) {
    die("Quiz introuvable ou accès refusé.");
}

$stmt = $DB->prepare(
    "SELECT id_categories, Nom_categorie
     FROM categories
     WHERE teacher_id = ?"
);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$categories = $stmt->get_result();

$stmt = $DB->prepare(
    "SELECT * FROM questions
     WHERE id_quiz = ?
     ORDER BY id_question ASC"
);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$questionsDB = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre = trim($_POST['quizTitle'] ?? '');
    $description = trim($_POST['quizDescription'] ?? '');
    $category_id = (int) ($_POST['quizCategory'] ?? 0);

    if ($titre !== '' && $category_id > 0) {

        $stmt = $DB->prepare(
            "UPDATE quiz
             SET titre_quiz = ?, description = ?, id_categories = ?
             WHERE id_quiz = ? AND id_enseignant = ?"
        );
        $stmt->bind_param(
            "ssiii",
            $titre,
            $description,
            $category_id,
            $quiz_id,
            $teacher_id
        );
        $stmt->execute();

        $stmt = $DB->prepare("DELETE FROM questions WHERE id_quiz = ?");
        $stmt->bind_param("i", $quiz_id);
        $stmt->execute();

        $questions = $_POST['question'] ?? [];
        $answers   = $_POST['answer'] ?? [];

        for ($i = 0; $i < count($questions); $i++) {

            $qText = trim($questions[$i] ?? '');
            $aText = trim($answers[$i] ?? '');

            if ($qText === '' || $aText === '') continue;

            $points = 1;

            $stmtQ = $DB->prepare(
                "INSERT INTO questions
                (text_question, reponse_correct, points, id_quiz)
                VALUES (?, ?, ?, ?)"
            );
            $stmtQ->bind_param("ssii", $qText, $aText, $points, $quiz_id);
            $stmtQ->execute();
        }

        header("Location: quizes.php?updated=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Quiz - Quiz App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/edit_quiz.css">
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
        <button class="logout-btn" onclick="handleLogout()">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </button>
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
                    <a href="create_category.php">
                        <i class="fas fa-folder-plus"></i>
                        <span>Create Category</span>
                    </a>
                </li>

                <li>
                    <a href="quizes.php">
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
                <h2>Modifier le Quiz</h2>
                <p>Mettez à jour les informations et questions du quiz</p>
            </div>
            <a href="quizes.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Retour aux quiz
            </a>
        </div>

        <div class="form-container">
            <div class="info-alert">
                <i class="fas fa-info-circle"></i>
                <p>Modifiez les informations de votre quiz. Les modifications seront immédiatement appliquées.</p>
            </div>

            <form id="editQuizForm" method="POST">
                <div class="form-section">
                    <h3>Informations du Quiz</h3>

                    <div class="form-group">
                        <label for="quizTitle">
                            Titre du quiz <span style="color: #dc3545;">*</span>
                        </label>
                        <input
                            type="text"
                            id="quizTitle"
                            name="quizTitle"
                            value="<?= htmlspecialchars($quiz['titre_quiz']) ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="quizCategory">
                            Catégorie <span style="color: #dc3545;">*</span>
                        </label>
                        <select id="quizCategory" name="quizCategory" required>
                            <?php while ($cat = $categories->fetch_assoc()): ?>
                                <option value="<?= $cat['id_categories'] ?>"
                                    <?= $cat['id_categories'] == $quiz['id_categories'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['Nom_categorie']) ?>
                                </option>
                            <?php endwhile; ?>

                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quizDescription">
                            Description du quiz
                        </label>
                        <textarea
                            id="quizDescription"
                            name="quizDescription">Apprenez les bases du langage HTML et la structure d'une page web</textarea>
                        <small>Optionnel - Une description aide les étudiants</small>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Questions du quiz</h3>

                    <div class="questions-container" id="questionsContainer">
                        <?php $i = 1;
                        while ($q = $questionsDB->fetch_assoc()): ?>
                            <div class="question-block">
                                <div class="question-header">
                                    <span class="question-number">Question <?= $i++ ?></span>
                                    <button type="button" class="delete-question-btn" onclick="deleteQuestion(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="question-inputs">
                                    <input type="text" name="question[]" value="<?= htmlspecialchars($q['text_question']) ?>" required>
                                    <input type="text" name="answer[]" value="<?= htmlspecialchars($q['reponse_correct']) ?>" required>
                                </div>
                            </div>
                        <?php endwhile; ?>

                    </div>

                    <button type="button" class="add-question-btn" onclick="addQuestion()">
                        <i class="fas fa-plus"></i>
                        Ajouter une question
                    </button>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Enregistrer les modifications
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="handleCancel()">
                        <i class="fas fa-times"></i>
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        let questionCount = 3;

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

        function addQuestion() {
            questionCount++;
            const container = document.getElementById('questionsContainer');
            const div = document.createElement('div');
            div.className = 'question-block';
            div.innerHTML = `
                <div class="question-header">
                    <span class="question-number">Question ${questionCount}</span>
                    <button type="button" class="delete-question-btn" onclick="deleteQuestion(this)" title="Supprimer">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="question-inputs">
                    <input type="text" name="question[]" placeholder="Entrez la question" required>
                    <input type="text" name="answer[]" placeholder="Réponse correcte" required>
                </div>
            `;
            container.appendChild(div);
        }

        function deleteQuestion(btn) {
            const container = document.getElementById('questionsContainer');
            const blocks = container.querySelectorAll('.question-block');

            if (blocks.length <= 1) {
                alert('Vous devez avoir au moins une question dans le quiz.');
                return;
            }

            if (confirm('Supprimer cette question ?')) {
                btn.closest('.question-block').remove();
                updateQuestionNumbers();
            }
        }

        function updateQuestionNumbers() {
            const blocks = document.querySelectorAll('.question-block');
            questionCount = blocks.length;
            blocks.forEach((block, index) => {
                block.querySelector('.question-number').textContent = `Question ${index + 1}`;
            });
        }



        function handleCancel() {
            if (confirm('Annuler les modifications ? Toutes les modifications non enregistrées seront perdues.')) {
                window.location.href = 'quizes.php';
            }
        }
    </script>
</body>

</html>
