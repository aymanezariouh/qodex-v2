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
    "SELECT id_quiz 
     FROM quiz 
     WHERE id_quiz = ? AND id_enseignant = ?"
);
$stmt->bind_param("ii", $quiz_id, $teacher_id);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    die("Access denied.");
}

$stmt = $DB->prepare("DELETE FROM quiz WHERE id_quiz = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();

header("Location: quizes.php?deleted=1");
exit;
