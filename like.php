<?php require 'db.php'; session_start();
if(!isset($_SESSION['user_id'])) exit;
$uid=$_SESSION['user_id']; $tid=(int)$_GET['tid'];
$stmt=$pdo->prepare("SELECT 1 FROM likes WHERE user_id=? AND tweet_id=?");
$stmt->execute([$uid,$tid]);
if($stmt->fetch()){ $pdo->prepare("DELETE FROM likes WHERE user_id=? AND tweet_id=?")->execute([$uid,$tid]); }
else{ $pdo->prepare("INSERT INTO likes(user_id,tweet_id) VALUES(?,?)")->execute([$uid,$tid]); }
echo "<script>redirect('home.php');</script>";
