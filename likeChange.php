<?php

session_start();
require_once('pdo.php');

if (!isset($_SESSION['userId'])) {
   die('ACCESS DENIED');
   return;
};

if (!isset($_POST['lType'])) {
   die('ACCESS DENIED');
   return;
}

if ($_POST['lType'] == 0) {

   $stmt = $pdo->prepare('SELECT * FROM topics where topicId = :topicId');
   $stmt->execute(array(':topicId' => $_POST['topicId']));
   if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) === false) {
      return;
   }

   $stmt = $pdo->prepare('SELECT * FROM likes where userId = :userId and topicId = :topicId');
   $stmt->execute(array(':userId' => $_SESSION['userId'], ':topicId' => $_POST['topicId']));

   if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) === false) {
      $stmt = $pdo->prepare('INSERT INTO likes (likeValue,userId,topicId,likeType) values (:lValue, :uId, :tId, 0)');
      $stmt->execute(array(':uId' => $_SESSION['userId'], ':tId' => $_POST['topicId'], ':lValue' => $_POST['lValue']));
   }

   if ($row !== false && $row['likeValue'] == $_POST['lValue']) {
      $stmt = $pdo->prepare('UPDATE likes set likeValue = null where likeId = :lId');
      $stmt->execute(array(':lId' => $row['likeId']));
   } else if ($row !== false && $row['likeValue'] != $_POST['lValue']) {
      $stmt = $pdo->prepare('UPDATE likes set likeValue = :lValue where likeId = :lId');
      $stmt->execute(array(':lId' => $row['likeId'], ':lValue' => $_POST['lValue']));
   }
}
if ($_POST['lType'] == 1) {

   $stmt = $pdo->prepare('SELECT * FROM comments where commentId = :commentId');
   $stmt->execute(array(':commentId' => $_POST['commentId']));
   if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) === false) {
      return;
   }

   $stmt = $pdo->prepare('SELECT * FROM likes where userId = :userId and commentId = :commentId');
   $stmt->execute(array(':userId' => $_SESSION['userId'], ':commentId' => $_POST['commentId']));

   if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) === false) {
      $stmt = $pdo->prepare('INSERT INTO likes (likeValue,userId,commentId,likeType) values (:lValue, :uId, :cId, 1)');
      $stmt->execute(array(':uId' => $_SESSION['userId'], ':cId' => $_POST['commentId'], ':lValue' => $_POST['lValue']));
   }

   if ($row !== false && $row['likeValue'] == $_POST['lValue']) {
      $stmt = $pdo->prepare('UPDATE likes set likeValue = null where likeId = :lId');
      $stmt->execute(array(':lId' => $row['likeId']));
   } else if ($row !== false && $row['likeValue'] != $_POST['lValue']) {
      $stmt = $pdo->prepare('UPDATE likes set likeValue = :lValue where likeId = :lId');
      $stmt->execute(array(':lId' => $row['likeId'], ':lValue' => $_POST['lValue']));
   }
}
