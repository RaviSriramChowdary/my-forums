<?php

session_start();
require_once('pdo.php');

//Initialising the variables
$name = false;
$profile = false;
$showForm = 0;

if (isset($_SESSION['name'])) {
   $name = $_SESSION['name'];
}

?>

<!DOCTYPE html>

<html>

<head>
   <title>
      My Forums | Create and Manage
   </title>
   <link rel="stylesheet" href="style.css" type="text/CSS">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
   <script src="https://kit.fontawesome.com/843e13ba28.js" crossorigin="anonymous"></script>

</head>

<body>
   <div id="navbar">
      <h1 id="logo">My Forums</h1>
      <div id="profile">
         <div id="dPic"></div>
         <p id="profileName"><?= $name ?> </p>
         <div id="status">Administator
            <p id="logout">Logout</p>
         </div>
      </div>
   </div>
   <div id="welcome">
      Welcome back <?= $name ?>
   </div>
   <p class="successMsg" id="successMsgIndex" style="color: green;"></p>
   <div id="topics">
      <div id="null">
         <div id="error808">
            <div id="a808">808</div>
            <div id="sideError">Error</div>
         </div>
         No topic found......<br />
         Seems like no body created a topic. Why would it not be you to create the first? Click the <u>+ New Topic</u> to create a topic.
      </div>
   </div>

   <div id="newTopicBlock">
      <div id="topicbackdrop"></div>
      <div id="topicFormDiv">
         <form id="topicForm" method="post">
            <h2>Create Topic</h2>
            <p class="errorMsg" id="errorMsgForm" style="color: red;"></p>
            <label for="tName">Topic Title :</label>
            <input class="input" type="text" name="tName" id="tName" />
            <label for="tContent">Content</label>
            <textarea rows="10" cols="40" name="tContent" id="tContent"></textarea><br />

            <input type="submit" class="btn" value="Create" name="create" id="create" />
            <input type="submit" class="btn" value="Discard" id="discard" name="discard" />
         </form>
      </div>
   </div>

   <div id="addNewTopicDiv">
      <button id="addNewTopic" class="btn">+ New Topic</button>
   </div>

   <script src="index.js"></script>
</body>

</html>