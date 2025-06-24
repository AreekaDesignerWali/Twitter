<?php require 'db.php'; session_start();
if(!isset($_SESSION['user_id'])||!isset($_GET['u'])) exit;
$uid=$_SESSION['user_id']; $other=$_GET['u'];
// get id
$stmt=$pdo->prepare("SELECT id FROM users WHERE username=?"); $stmt->execute([$other]);
if(!$row=$stmt->fetch()) exit;
$oid=$row['id'];
// toggle follow
$up=$pdo->prepare("SELECT 1 FROM follows WHERE follower_id=? AND following_id=?");
$up->execute([$uid,$oid]);
if($up->fetch()){ $pdo->prepare("DELETE FROM follows WHERE follower_id=? AND following_id=?")->execute([$uid,$oid]); }
else{ $pdo->prepare("INSERT INTO follows (follower_id,following_id) VALUES (?,?)")->execute([$uid,$oid]); }
echo "<script>redirect('profile.php?user=$other');</script>";
