<?php

session_start();
require_once('pdo.php');


//Initialising the variables
$error = false;
$highlight = false;
$tName = $tContent = false;
$showForm = 0;

$required = ['tName', 'tContent'];

//Validating the form
foreach ($required as $i) {
   if (isset($_POST[$i]) && strlen($_POST[$i]) === 0) {
      $highlight = $i;
      $error = 'Please fill the details about the topic.';
      $showForm = 1;
      echo json_encode(array('error' => $error, 'highlight' => $highlight, 'showForm' => $showForm));
      return;
   }
}

//Inserting into the table
$stmt = $pdo->prepare('INSERT INTO topics (topicName,topicContent,startedBy) values (:tName,:tContent,:uId)');
$stmt->execute(array(':tName' => $_POST['tName'], ':tContent' => $_POST['tContent'], ':uId' => $_SESSION['userId']));
$showForm = 0;

echo json_encode(array('error' => $error, 'highlight' => $highlight, 'showForm' => $showForm));
