<?php
\// DELETE category (FINAL – FK SAFE)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {

    $category_id = (int) $_POST['delete_id'];

    // 1. delete questions linked to quizzes in this category
    $stmt = $DB->prepare(
        "DELETE q
         FROM questions q
         JOIN quiz z ON q.id_quiz = z.id_quiz
         WHERE z.id_categories = ?"
    );
    $stmt->bind_param("i", $category_id);
    $stmt->execute();

    // 2. delete quizzes in this category
    $stmt = $DB->prepare(
        "DELETE FROM quiz WHERE id_categories = ?"
    );
    $stmt->bind_param("i", $category_id);
    $stmt->execute();

    // 3. delete the category
    $stmt = $DB->prepare(
        "DELETE FROM categories WHERE id_categories = ?"
    );
    $stmt->bind_param("i", $category_id);
    $stmt->execute();

    header("Location: create_category.php");
    exit;
}
