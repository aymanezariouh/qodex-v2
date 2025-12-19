<?php
require_once 'C:\Users\LENOVO\Desktop\quiz-app\includes\database.php';
require_once __DIR__ . '/../includes/functiones.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom_categ = $_POST["categoryName"];
    $description_categ = $_POST["categoryDescription"];
    
   
    $teacher_id = $_SESSION['teacher_id'];

$sql = $DB->prepare(
  "INSERT INTO categories (Nom_categorie, description, teacher_id)
   VALUES (?, ?, ?)"
);
$sql->bind_param("ssi", $nom_categ, $description_categ, $teacher_id);
$sql->execute();



    header("Location: create_category.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Catégorie - Quiz App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/CAEGORIE.css">
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
                    <a href="./Dashboard.php" >
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li>
                    <a href="create_category.php" class="active">
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

    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title-section">
                <h2>Nouvelle Catégorie</h2>
                <p>Créez une nouvelle catégorie pour organiser vos quiz</p>
            </div>
            <a href="create_category.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Retour aux catégories
            </a>
        </div>

        <!-- Form Container -->
        <div class="form-container">
            <!-- Success Message (hidden by default) -->
            <div class="success-message" id="successMessage">
                <i class="fas fa-check-circle"></i>
                <p>Catégorie créée avec succès !</p>
            </div>

            <!-- Create Form -->
            <form id="createCategoryForm" method="post" name="creat">
                <!-- Category Name -->
                <div class="form-group">
                    <label for="categoryName">
                        Nom de la catégorie <span style="color: #dc3545;">*</span>
                    </label>
                    <input
                        type="text"
                        id="categoryName"
                        name="categoryName"
                        required
                        placeholder="Ex: HTML/CSS, JavaScript, PHP...">
                    <small>Choisissez un nom unique et descriptif</small>
                </div>

                <!-- Category Description -->
                <div class="form-group">
                    <label for="categoryDescription">
                        Description
                    </label>
                    <textarea
                        id="categoryDescription"
                        name="categoryDescription"
                        placeholder="Décrivez brièvement le contenu de cette catégorie..."></textarea>
                    <small>Optionnel - Une description aide les étudiants à comprendre le contenu</small>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i>
                        Créer la catégorie
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="handleCancel()">
                        <i class="fas fa-times"></i>
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>