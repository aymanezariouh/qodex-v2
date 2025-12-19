<?php
session_start();
require_once __DIR__ . '/../includes/database.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../index.php");
    exit;
}

$error = null;
$stmt = $DB->prepare(
    "SELECT id_categories, Nom_categorie
     FROM categories
     WHERE teacher_id = ?"
);
$stmt->bind_param("i", $_SESSION['teacher_id']);
$stmt->execute();
$categories = $stmt->get_result();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre        = trim($_POST['quizTitle'] ?? '');
    $description  = trim($_POST['quizDescription'] ?? '');
    $category_id  = (int) ($_POST['quizCategory'] ?? 0);
    $teacher_id   = (int) $_SESSION['teacher_id'];
    $questions    = $_POST['question'] ?? [];
    $answers      = $_POST['answer'] ?? [];

    if ($titre === '' || $category_id === 0) {
        $error = "Titre et catégorie obligatoires.";
    }

    if (!$error) {
        $checkCat = $DB->prepare(
            "SELECT id_categories
             FROM categories
             WHERE id_categories = ? AND teacher_id = ?"
        );
        $checkCat->bind_param("ii", $category_id, $teacher_id);
        $checkCat->execute();

        if ($checkCat->get_result()->num_rows === 0) {
            $error = "Catégorie invalide.";
        }
    }

    if (!$error) {
        $stmt = $DB->prepare(
            "INSERT INTO quiz (titre_quiz, description, id_categories, id_enseignant)
     VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssii", $titre, $description, $category_id, $teacher_id);
        $stmt->execute();

        $quiz_id = $DB->insert_id;


        $validQuestions = 0;

        for ($i = 0; $i < count($questions); $i++) {

            $qText = trim($questions[$i] ?? '');
            $aText = trim($answers[$i] ?? '');

            if ($qText === '' || $aText === '') {
                continue;
            }

            $validQuestions++;

            $validQuestions = 0;

            for ($i = 0; $i < count($questions); $i++) {

                $qText = trim($questions[$i] ?? '');
                $aText = trim($answers[$i] ?? '');

                if ($qText === '' || $aText === '') {
                    continue;
                }

                $validQuestions++;

                $points = 1;
                $option1 = null;
                $option2 = null;
                $option3 = null;
                $option4 = null;

                $stmtQ = $DB->prepare(
                    "INSERT INTO questions
        (text_question, reponse_correct, points, id_quiz, option1, option2, option3, option4)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                );

                $stmtQ->bind_param(
                    "ssiiisss",
                    $qText,
                    $aText,
                    $points,
                    $quiz_id,
                    $option1,
                    $option2,
                    $option3,
                    $option4
                );

                $stmtQ->execute();
            }
        }

        if ($validQuestions === 0) {
            $DB->query("DELETE FROM quiz WHERE id_quiz = $quiz_id");
            $error = "Ajoutez au moins une question valide.";
        }
    }

    if (!$error) {
        header("Location: quizes.php");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Quiz - Quiz App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/creat_quiz.css">
    <style>
    </style>
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
                <h2>Créer un Quiz</h2>
                <p>Ajoutez un nouveau quiz et ses questions</p>
            </div>
            <a href="quizes.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Retour aux quiz
            </a>
        </div>

        <div class="form-container">
            <form id="createQuizForm" method="POST" action="create_quiz.php">
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
                            required
                            placeholder="Ex: Introduction à HTML">
                    </div>

                    <div class="form-group">
                        <label for="quizCategory">
                            Catégorie <span style="color: #dc3545;">*</span>
                        </label>
                        <select id="quizCategory" name="quizCategory" required>
                            <option value="">Sélectionnez une catégorie</option>

                            <?php while ($cat = $categories->fetch_assoc()) { ?>
                                <option value="<?= $cat['id_categories'] ?>">
                                    <?= htmlspecialchars($cat['Nom_categorie']) ?>
                                </option>
                            <?php } ?>
                        </select>

                    </div>

                    <div class="form-group">
                        <label for="quizDescription">
                            Description du quiz
                        </label>
                        <textarea
                            id="quizDescription"
                            name="quizDescription"
                            placeholder="Décrivez brièvement le contenu du quiz..."></textarea>
                        <small>Optionnel - Une description aide les étudiants</small>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Questions du quiz</h3>

                    <div class="questions-container" id="questionsContainer">
                        <div class="question-block">
                            <div class="question-header">
                                <span class="question-number">Question 1</span>
                                <button type="button" class="delete-question-btn" onclick="deleteQuestion(this)" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="question-inputs">
                                <input
                                    type="text"
                                    name="question[]"
                                    placeholder="Entrez la question"
                                    required>
                                <input
                                    type="text"
                                    name="answer[]"
                                    placeholder="Réponse correcte"
                                    required>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="add-question-btn" onclick="addQuestion()">
                        <i class="fas fa-plus"></i>
                        Ajouter une question
                    </button>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-circle"></i>
                        Créer le quiz
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
        let questionCount = 1;

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
            const questionsContainer = document.getElementById('questionsContainer');

            const questionBlock = document.createElement('div');
            questionBlock.className = 'question-block';
            questionBlock.innerHTML = `
                <div class="question-header">
                    <span class="question-number">Question ${questionCount}</span>
                    <button type="button" class="delete-question-btn" onclick="deleteQuestion(this)" title="Supprimer">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="question-inputs">
                    <input 
                        type="text" 
                        name="question[]" 
                        placeholder="Entrez la question"
                        required
                    >
                    <input 
                        type="text" 
                        name="answer[]" 
                        placeholder="Réponse correcte"
                        required
                    >
                </div>
            `;

            questionsContainer.appendChild(questionBlock);
        }

        function deleteQuestion(btn) {
            const questionsContainer = document.getElementById('questionsContainer');
            const questionBlocks = questionsContainer.querySelectorAll('.question-block');

            if (questionBlocks.length <= 1) {
                alert('Vous devez avoir au moins une question dans le quiz.');
                return;
            }

            if (confirm('Êtes-vous sûr de vouloir supprimer cette question ?')) {
                const questionBlock = btn.closest('.question-block');
                questionBlock.remove();

                updateQuestionNumbers();
            }
        }

        function updateQuestionNumbers() {
            const questionBlocks = document.querySelectorAll('.question-block');
            questionCount = questionBlocks.length;

            questionBlocks.forEach((block, index) => {
                const numberSpan = block.querySelector('.question-number');
                numberSpan.textContent = `Question ${index + 1}`;
            });
        }






        function handleCancel() {
            const quizTitle = document.getElementById('quizTitle').value;

            if (quizTitle.trim() !== '') {
                if (confirm('Annuler la création ? Toutes les informations saisies seront perdues.')) {
                    window.location.href = 'edit_quiz.php';
                }
            } else {
                window.location.href = 'edit_quiz.php';
            }
        }
    </script>
</body>

</html>
