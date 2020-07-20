<?php

session_start();
require_once('pdo.php');

//Initialising the variables
$error = false;
$success = false;
$highlight = false;
$name = $uName = $email = $pass = $rePass = $dob =  false;

$required = ['name', 'uName', 'pass', 'repass', 'dob'];

//If the cancel is pressed
if (isset($_POST['cancel'])) {
   header('location: index.php');
   return;
}

//Validating the form
if (isset($_POST['register'])) {
   foreach ($required as $i) {
      if (isset($_POST[$i]) && strlen($_POST[$i]) === 0) {
         $_SESSION['cause'] = $i;
         $_SESSION['error'] = 'All * fields are required.';
         $_SESSION['post'] = $_POST;
         header('location: register.php');
         return;
      }
   }

   if (preg_match('/ /', $_POST['uName']) === 1) {
      $_SESSION['error'] = 'Spaces are not allowed in username.';
      $_SESSION['cause'] = 'uName';
      $_SESSION['post'] = $_POST;
      header('location: register.php');
      return;
   }
   if (strlen($_POST['pass']) < 6 || preg_match('/[A-Z]/i', $_POST['pass']) === 0 || preg_match('/[0-9]/i', $_POST['pass']) === 0 || preg_match('/[a-z]/i', $_POST['pass']) === 0 || preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $_POST['pass']) === 0) {
      $_SESSION['error'] = 'Passwords have to pass the criteria.';
      $_SESSION['cause'] = 'pass';
      $_SESSION['post'] = $_POST;
      header('location: register.php');
      return;
   }
   if ($_POST['pass'] !== $_POST['repass']) {
      $_SESSION['error'] = 'Passwords have to match.';
      $_SESSION['cause'] = 'repass';
      $_SESSION['post'] = $_POST;
      header('location: register.php');
      return;
   }
   if ($_POST['acType'] + 0 === 0) {
      $_SESSION['error'] = 'Please select an account type.';
      $_SESSION['cause'] = 'acType';
      $_SESSION['post'] = $_POST;
      header('location: register.php');
      return;
   }

   $dateOfBirth = $_POST['dob'];
   $year = 12 +  explode('-', $dateOfBirth)[0];
   $month = 0 +  explode('-', $dateOfBirth)[1];
   $day = 0 +  explode('-', $dateOfBirth)[2];
   if (strtotime($year . '-' . $month . '-' . $day) > time()) {
      $_SESSION['error'] = 'Age criteria not fulfilled. Min 12 years Old required';
      $_SESSION['cause'] = 'dob';
      $_SESSION['post'] = $_POST;
      header('location: register.php');
      return;
   }

   //Adding to the table

   $stmt = $pdo->prepare('SELECT * from users where userName = :uName');
   $stmt->execute(array(':uName' => $_POST['uName']));

   if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) === false) {
      $stmt = $pdo->prepare('INSERT INTO users (userName,password,name,emailId,dob,accountType) values (:userName,:password,:name,:emailId,:dob,:accountType)');
      $stmt->execute(array(':userName' => $_POST['uName'], ':password' => hash('md5', 'Hash' . $_POST['pass']), ':name' => $_POST['name'], ':emailId' => $_POST['email'], ':dob' => $_POST['dob'], ':accountType' => $_POST['acType']));

      $_SESSION['loginSuccess'] = "Succesfully logged In. Please login to continue.";
      header('location: login.php');
      return;
   } else {
      $_SESSION['error'] = 'Already a user exists with that user name.';
      $_SESSION['cause'] = 'uName';
      $_SESSION['post'] = $_POST;
      header('location: register.php');
      return;
   }
}

if (isset($_SESSION['error'])) {
   $error = $_SESSION['error'];
   $highlight = "<style>#" . $_SESSION['cause'] . "{
      background-color: rgb(255, 201, 195);
      border: 2px solid red;
      outline: none;}</style>";
   unset($_SESSION['error']);
   unset($_SESSION['cause']);
}

if (isset($_SESSION['post'])) {
   $name = htmlentities($_SESSION['post']['name']);
   $uName = htmlentities($_SESSION['post']['uName']);
   $email = htmlentities($_SESSION['post']['email']);
   $dob = htmlentities($_SESSION['post']['dob']);
   $pass = htmlentities($_SESSION['post']['pass']);
   $rePass = htmlentities($_SESSION['post']['repass']);
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
         <div id="login" class="switchBlocks "><a href="login.php">Log In</a></div>
         <div id="register" class="switchBlocks selected">Register</div>
      </div>
      <div id="authMain">
         <p style="color: red;"><?= $error ?></p>
         <p style="color: green;"><?= $success ?></p>
         <form method="POST">
            <p class="fields">
               <label class="label2" for="name">Your Name <span class="red">*</span> : </label>
               <input type="text" name="name" id="name" size="35" value="<?= $name ?>" />
            </p>
            <p class="fields">
               <label class="label2" for="uName">User Name <span class="red">*</span> : </label>
               <input type="text" name="uName" id="uName" size="35" value="<?= $uName ?>" />
            </p>
            <p class="fields">
               <label class="label2" for="email">Email Id : </label>
               <input type="email" name="email" id="email" size="35" value="<?= $email ?>" />
            </p>
            <p class="fields">
               <label class="label2" for="pass">Password <span class="red">*</span> : </label>
               <input type="password" name="pass" id="pass" size="35" value="<?= $pass ?>" />
            </p>
            <p class="fields">
               <label class="label2" for="repass">Re-Enter Password <span class="red">*</span> : </label>
               <input type="password" name="repass" id="repass" size="35" value="<?= $rePass ?>" />
            </p>
            <p class="fields">
               <label class="label2" for="dob">Date of Birth <span class="red">*</span> : </label>
               <input type="date" name="dob" id="dob" value="<?= $dob ?>" />
            </p>
            <p class="fields">
               <label class="label2" for="acType">Account Type <span class="red">*</span> : </label>
               <select name="acType" id="acType">
                  <option value="0">-- Select --</option>
                  <option value="1">Administrator</option>
                  <option value="2">User</option>
               </select>
            </p>
            <input type="submit" value="Register" name="register" class="btn good" />
            <input type="submit" value="Cancel" name="cancel" class="btn bad" />
            <p id="switchP">Already have an account? <a class="href" href="login.php">Login</a>!</p>
         </form>
      </div>
   </div>
</body>