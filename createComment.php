<?php
session_start();
require_once('pdo.php');

$stmt = $pdo->prepare('INSERT INTO comments (comment,topicId,givenBy) values (:comment,:topicId,:givenBy)');
$stmt->execute(array(':comment' => $_POST['comment'],':topicId' => $_POST['topicId'],':givenBy' => $_SESSION['userId']));