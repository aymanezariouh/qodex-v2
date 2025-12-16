<?php function get_categories() {

    // Get DB connection explicitly
    $DB = require __DIR__ . '/database.php';

    $sql = "
        SELECT 
            cat.id_categories,
            cat.Nom_categorie,
            cat.description,
            COUNT(q.id_quiz) AS quiz_count
        FROM categories cat
        LEFT JOIN quiz q 
            ON q.id_categories = cat.id_categories
        GROUP BY cat.id_categories
    ";

    $result = mysqli_query($DB, $sql);

    if (!$result) {
        die("SQL ERROR: " . mysqli_error($DB));
    }

    return $result;
};

function get_id($id) {
    global $DB;
    $query = "SELECT * FROM categories WHERE id_categories = $id";
    $result = $DB->query($query);

    return $result->fetch_assoc();
}

