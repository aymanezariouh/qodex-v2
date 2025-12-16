 <?php
    session_start();
    $errors = [
        'signin' => $_SESSION['signin_error'] ?? '',
        'signup' => $_SESSION['signup_error'] ?? ''
    ];
    session_unset();
    function show_eror($errors)
    {
        return !empty($errors) ? "<p class='error-message'> $errors</p>" : '';
    }

    ?>
 <!DOCTYPE html>
 <html lang="fr">

 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Quiz App</title>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
     <link rel="stylesheet" href="./css/style.css">
 </head>

 <body>

     <h1 id="Title"> Quiz app</h1>

     <main>
         <div class="container" id="container">

             <div class="form-container sign-up">
                 <form action="login_register.php" method="post">
                     <h1>Create Account</h1>
                     <?= show_eror($errors['signup']) ?>
                     <div class="social-container">
                         <a href="#" class="social"><i class="fa-brands fa-github"></i></a>
                     </div>
                     <input name="email" type="text" placeholder="Email">
                     <input name="password" type="password" placeholder="Password">
                     <input name="nom" type="text" placeholder="Nom">
                     <select name="role" id="">
                         <option value="enseignant">enseignant</option>
                         <option value="etudiant">etudiant</option>

                     </select>
                     <button type="submit" name="signUP">Sign Up</button>
                 </form>
             </div>

             <div class="form-container sign-in">
                 <form action="login_register.php" method="post">
                     <h1>Sign In</h1>
                     <?= show_eror($errors['signin']) ?>
                     <div class="social-container">
                         <a href="#" class="social"><i class="fa-brands fa-github"></i></a>
                     </div>
                     <input name="email" type="text" placeholder="Email">
                     <input name="password" type="password" placeholder="Password">
                     <button type="submit" name="signIN">Sign In</button>
                 </form>
             </div>

             <div class="overlay-container">
                 <div class="overlay">

                     <div class="overlay-panel overlay-left">
                         <h1>Welcome Back!</h1>
                         <p>Have an account? Log in!</p>
                         <button class="ghost" id="signIn">Log In</button>
                     </div>

                     <div class="overlay-panel overlay-right">
                         <h1>Hello!</h1>
                         <p>Don't have an account? Sign up!</p>
                         <button class="ghost" id="signUp">Sign Up</button>
                     </div>

                 </div>
             </div>

         </div>
     </main>


     <script src="./js/script.js"></script>
 </body>

 </html>