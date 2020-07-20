<?php

session_start();
require_once('pdo.php');

//Initialising the variables
$error = false;
$success = false;
$loggedIn = false;
$highlight = false;
$uName = $pass = false;

$required = ['uName', 'pass'];

//If the cancel is pressed
if (isset($_POST['cancel'])) {
   header('location: index.php');
   return;
}

//Validating the form
if (isset($_POST['login'])) {
   foreach ($required as $i) {
      if (isset($_POST[$i]) && strlen($_POST[$i]) === 0) {
         $_SESSION['cause'] = $i;
         $_SESSION['error'] = 'All * fields are required.';
         $_SESSION['post'] = $_POST;
         header('location: login.php');
         return;
      }
   }

   if (preg_match('/ /', $_POST['uName']) === 1) {
      $_SESSION['error'] = 'Invalid username.';
      $_SESSION['cause'] = 'uName';
      $_SESSION['post'] = $_POST;
      header('location: login.php');
      return;
   }

   //Fetching from the table

   $stmt = $pdo->prepare('SELECT * from users where userName = :uName and password = :password');
   $stmt->execute(array(':uName' => $_POST['uName'], ':password' => hash('md5','Hash'.$_POST['pass'])));
   $row = $stmt->fetch(PDO::FETCH_ASSOC);
   if ($row === false) {
      $_SESSION['error'] = 'Invalid Login Credentials.';
      $_SESSION['cause'] = 'uName, #pass';
      $_SESSION['post'] = $_POST;
      header('location: login.php');
      return;
   } else {
      $_SESSION['userId'] = $row['userId'];
      $_SESSION['userName'] = $row['userName'];
      $_SESSION['name'] = "<span class='caps'><span class='lcase'>".$row['name']. "</span></span>";
      header('location: index.php');
      return;
   }
}

if (isset($_SESSION['userName'])) {
   $loggedIn = "Logged In as " . $_SESSION['userName'];
}

if (isset($_SESSION['error'])) {
   $error = $_SESSION['error'];
   $highlight = "<style>
      #" . $_SESSION['cause'] . " {
         background-color: rgb(255, 201, 195);
         border: 2px solid red;
         outline: none;
      }
   </style>";
   unset($_SESSION['error']);
   unset($_SESSION['cause']);
}

if (isset($_SESSION['post'])) {
   $uName = htmlentities($_SESSION['post']['uName']);
   $pass = htmlentities($_SESSION['post']['pass']);
   unset($_SESSION['post']);
}

?>

<!DOCTYPE html>

<html>

<head>
   <title>Login | My Forums</title>
   <link rel="stylesheet" href="style.css" type="text/CSS">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <?= $highlight ?>
</head>

<body>
   <div id="navbar">
      <h1 id="logo">My Forums</h1>
   </div>
   <div id="auth">
      <div id="switchAuth">
         <div id="login" class="switchBlocks selected">Log In</div>
         <div id="register" class="switchBlocks"><a href="register.php">Register</a></div>
      </div>
      <div id="authMain">
         <p style="color: red;"><?= $error ?></p>
         <p style="color: green;"><?= $success ?></p>
         <p style="color: green;"><?= $loggedIn ?></p>
         <form method="POST">
            <p class="fields">
               <label class="label" for="uName">User Name <span class="red">*</span> : </label>
               <input type="text" name="uName" id="uName" size="35" value="<?= $uName ?>" />
            </p>
            <p class="fields">
               <label class="label" for="pass">Password <span class="red">*</span> : </label>
               <input type="password" name="pass" id="pass" size="35" value="<?= $pass ?>" />
            </p>
            <input type="submit" value="Login" name="login" class="btn good" />
            <input type="submit" value="Cancel" name="cancel" class="btn bad" />
            <p id="switchP">Do not have an account? <a class="href" href="register.php">Create One</a>!</p>
         </form>
      </div>
   </div>
</body>

</html>