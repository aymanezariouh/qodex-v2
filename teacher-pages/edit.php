<?php
require_once 'C:\Users\LENOVO\Desktop\quiz-app\includes\database.php';
require_once __DIR__ . '/../includes/functiones.php';
$id = $_GET['id'];
$categories = get_categories();
$categ = get_id($id);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // echo '<pre>';
    // print_r($_POST);
    // echo '</pre>';
    $id = $_POST['id'];
    $nom = $_POST['Nom_categorie'];
    $description = $_POST['description'];
    $new = $DB ->prepare(
        "UPDATE categories 
        SET Nom_categorie = ?, description = ? 
        WHERE id_categories = ?"
    );
    $new->bind_param("ssi",$nom,$description, $id );
    $new->execute();
    header("location : create_category.php");
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la Catégorie - Quiz App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/edit.css"> 
</head>
<body>

    <header class="header">
        <div class="header-left">
            <button class="menu-toggle" id="menuToggle" name="logoutBtn">
                <i class="fas fa-bars"></i>
            </button>
            <h1>
                <i class="fas fa-graduation-cap"></i>
                <span>Quiz App</span>
            </h1>
        </div>
        <button class="logout-btn" type="submit">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </button>
    </header>

    <!-- Sidebar -->
    
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
                    <a href="create_category.php" class="active">
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
                <h2>Modifier la Catégorie</h2>
                <p>Mettre à jour les informations de la catégorie</p>
            </div>
            <a href="create_category.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Retour aux catégories
            </a>
        </div>

        <!-- Form Container -->
        <div class="form-container">
            <!-- Info Alert -->
            <div class="info-alert">
                <i class="fas fa-info-circle"></i>
                <p>Modifiez les informations de votre catégorie. Les modifications seront appliquées à tous les quiz associés.</p>
            </div>

            <!-- Edit Form -->
            <form id="editCategoryForm" method="POST">
                <!-- Category Name -->
                <div class="form-group">
                    <label for="categoryName">
                        Nom de la catégorie <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <input 
                        type="text" 
                        id="categoryName" 
                        name="Nom_categorie" 
                        value="<?php echo $categ['Nom_categorie'];?>"
                        required
                        placeholder="Ex: HTML/CSS, JavaScript, PHP..."
                    >
                    <small>Le nom doit être unique et descriptif</small>
                </div>

                <!-- Category Description -->
                <div class="form-group">
                    <label for="categoryDescription">
                        Description
                    </label>
                    <textarea 
                        id="categoryDescription" 
                        name="description"
                        placeholder="Décrivez brièvement le contenu de cette catégorie..."
                    ><?php echo $categ['description']; ?></textarea>
                    <small>Une description claire aide les étudiants à comprendre le contenu</small>
                </div>

                <!-- Form Actions -->
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

    <script src="../js/edit.js"></script>
</body>

</html>