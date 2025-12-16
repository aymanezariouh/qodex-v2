<?php
require_once 'C:\Users\LENOVO\Desktop\quiz-app\includes\database.php';
require_once __DIR__ . '/../includes/functiones.php';
$categories = get_categories();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Catégories - Quiz App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/categories.css">
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
                <h2>Gestion des Catégories</h2>
                <p>Organisez vos quiz par catégories</p>
            </div>
            <button class="new-category-btn">
                <i class="fas fa-plus"></i>
                Nouvelle Catégorie
            </button>
        </div>

        <div class="categories-container">
            <?php
            while ($categ = mysqli_fetch_assoc($categories)) { ?>
                <div class="category-card">
                    <div class="category-header">
                        <div class="category-info">
                            <h3> <?php echo $categ['Nom_categorie'] ?></h3>
                            <p> <?php echo $categ['description'] ?></p>
                        </div>
                    </div>
                    <div class="category-actions">
                        <a href="edit.php?id=<?= $categ['id_categories'] ?>" class="action-btn edit" title="Modifier">

                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="delete.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $categ['id_categories'] ?>">
                            <button type="submit" class="action-btn delete" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        <span class="indice"><i class="fas fa-clipboard-list mr-2"></i> <?php echo $categ['quiz_count'] ?></span>
                    </div>
                </div>
            <?php } ?>
    </main>

    <script src="../js/categories.js"></script>
</body>

</html>