<?php
session_start();
require_once './includes/database.php';


if (isset($_POST['signUP'])) {

    $name     = $_POST['nom'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];

    
    $checkemail = $DB->query("SELECT email FROM utilisateurs WHERE email = '$email'");

    if ($checkemail->num_rows > 0) {
        $_SESSION['signup_error'] = 'Email already used!';
    } else {
        $DB->query("INSERT INTO utilisateurs (Nom, Email, motdepasse, role)
                    VALUES ('$name', '$email', '$password', '$role')");
    }

    header("Location: index.php");
    exit();
}

if (isset($_POST['signIN'])) {

    $email    = $_POST['email'];
    $password = $_POST['password'];

    $result = $DB->query("SELECT * FROM utilisateurs WHERE Email = '$email'");

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['motdepasse'])) {

            $_SESSION['name']  = $user['Nom'];
            $_SESSION['email'] = $user['Email']; 

            if ($user['role'] === 'enseignant') {
                header("Location: teacher-pages/Dashboard.php");
            } else {
                header("Location: index.php");}
            exit();
        }
    }

    $_SESSION['signin_error'] = 'Incorrect email or password';
    header("Location: index.php");
    exit();
}
?>
