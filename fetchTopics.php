<?php

session_start();
require_once('pdo.php');

$array = array();

function countLikes($pdo, $param, $likeType, $likeValue){
   $like = false;
   if ($likeType === 0) {

      $stmt = $pdo->prepare('SELECT count(*) FROM likes where topicId = :topicId and likevalue = :lValue');
      $stmt->execute(array( ':topicId' => $param, ':lValue' => $likeValue));
      $like = $stmt->fetch(PDO::FETCH_ASSOC);

      $like = $like['count(*)'];
   } else if ($likeType === 1) {

      $stmt = $pdo->prepare('SELECT count(*) FROM likes where commentId = :commentId and likevalue = :lValue');
      $stmt->execute(array(':commentId' =>$param, ':lValue' => $likeValue));
      $like = $stmt->fetch(PDO::FETCH_ASSOC);

      $like = $like['count(*)'];
   }

   return ($like);
}

function fetchLike($pdo,$param,$likeType){
   $like = false;
   if($likeType === 0){

      $stmt = $pdo->prepare('SELECT * FROM likes where userId = :userId and topicId = :topicId ');
      $stmt->execute(array(':userId' => $_SESSION['userId'], ':topicId' => $param));
      $like = $stmt->fetch(PDO::FETCH_ASSOC);

      $like = $like['likeValue'];
   } else if ($likeType === 1) {

      $stmt = $pdo->prepare('SELECT * FROM likes where userId = :userId and commentId = :commentId ');
      $stmt->execute(array(':userId' => $_SESSION['userId'], ':commentId' => $param));
      $like = $stmt->fetch(PDO::FETCH_ASSOC);

      $like = $like['likeValue'];
   }

   return($like);
}

function fetchComments($topicId, $pdo){
   
   $GLOBALS['array'][''. $topicId] = array();
   $commentsHtml = false;
   $stmt = $pdo->prepare('SELECT distinct A.commentId,A.comment as comment,C.userName as givenBy,case when A.replyTo is not null THEN B.comment else null end as originalComment,case when A.replyTo is not null THEN D.userName else null end as replyTo FROM comments A join comments B JOIN users C join users D on (A.replyTo = B.commentId or A.replyTo is null) and A.givenBy = C.userId and D.userId = B.givenBy  where A.topicId =:topicId order by A.commentId');

   $stmt->execute(array(':topicId' => 0 + $topicId));
   $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

   foreach ($comments as $comment) {
      $GLOBALS['array']['' . $topicId][] = $comment['commentId'];
      $like = fetchLike($pdo, $comment['commentId'] + 0, 1);
      $l0 = $l1 = $dl0 = $dl1 = false;
      if ($like === false || $like === null) {
         $l1 = "style='display : none;'";
         $dl1 = "style='display : none;'";
      }
      if ($like == '1') {
         $l0 = "style='display : none;'";
         $dl1 = "style='display : none;'";
      }
      if ($like == '-1') {
         $l1 = "style='display : none;'";
         $dl0 = "style='display : none;'";
      }
      $likeBox = "<span class='like'> <i  " . $l0 . " class='like0 far fa-thumbs-up'></i><i " . $l1 . " class='like1 fas fa-thumbs-up'></i>".countLikes($pdo,$comment['commentId']+0,1,1)."</span><span class='dislike' ><i  " . $dl0 . " class='dislike0 far fa-thumbs-down'></i><i  " . $dl1 . " class='fas fa-thumbs-down dislike1'></i>" . countLikes($pdo, $comment['commentId'] + 0, 1, -1) . "</span><span class='reply'><i class='fas fa-reply'></i></span>";
      if($comment['givenBy'] === $_SESSION['userName']){
         $comment['givenBy'] = 'You';
      }
      if($comment['replyTo'] === $_SESSION['userName']){
         $comment['replyTo'] = 'You';
      }
      $commentsHtml .= "
      <div class='commentOuter' id='comment".$comment['commentId']."'>
         <div class='comment " . (('You' === $comment['givenBy']) ? 'user' : false) . "'>
            <div class='givenBy'>" . $comment['givenBy'] . "</div>
            ".  (($comment['replyTo'] !== null) ? "<div class='commentOuter'>
            <div class='replyTo'>
               <div class='givenBy'>" . $comment['replyTo'] . "</div>
               <div class='message'>" . $comment['originalComment'] . "</div>
            </div>
         </div>" : false)."
            <div class='message'>" . $comment['comment'] . "</div>
            <div class='options'>" . $likeBox . "</div>
         </div>
      </div>
      ";
   }
    $commentsHtml .= "<div class='clearBoth'></div>";

   return ($commentsHtml);
}

$topicsHtml = false;

$stmt = $pdo->prepare('SELECT * FROM topics order by topicId');
$stmt->execute();
$topics = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($topics as $topic) {
   $like = fetchLike($pdo,$topic['topicId'] + 0,0);
   $l0 = $l1 = $dl0 = $dl1 = false;
   if($like === false || $like === null) {
      $l1 = "style='display : none;'";
      $dl1 = "style='display : none;'";
   }
   if($like == '1') {
      $l0 = "style='display : none;'";
      $dl1 = "style='display : none;'";
   }
   if($like == '-1') {
      $l1 = "style='display : none;'";
      $dl0 = "style='display : none;'";
   }
   $likeBox = "<span class='like'> <i  ".$l0. " class='like0 far fa-thumbs-up'></i><i " . $l1 . " class='like1 fas fa-thumbs-up'></i>" . countLikes($pdo, $topic['topicId'] + 0, 0, 1) . "</span><span class='dislike' ><i  " . $dl0 . " class='dislike0 far fa-thumbs-down'></i><i  " . $dl1 . " class='fas fa-thumbs-down dislike1'></i>" . countLikes($pdo, $topic['topicId'] + 0, 0, -1) . "</span>";
   $topicsHtml .= "
   <div class='topic' id='topic" . $topic['topicId']."'>
      <div class='heading'>" . $topic['topicName'] . "</div>
      <div class='content'>
         <pre>" . $topic['topicContent'] . "</pre>
      </div>
            <div class='options'>" . $likeBox . "</div>
      <div class='commentBox'>
         <div class='comments'>" . fetchComments($topic['topicId'], $pdo,$array) . "
         </div>
         <div class='addComment'>
            <form class='formComment'>
               <textarea  name='newComment' placeholder='Type your comment here.' class='commentNewBox'></textarea>
               <input type='submit' name'comment' value='Comment' class='btn'/>
            </form>
         </div>
      </div>
   </div>";
}

echo json_encode(array('html' => $topicsHtml, 'array' => $array));
