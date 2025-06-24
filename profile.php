<?php require 'db.php'; session_start();
if(!isset($_SESSION['user_id'])) echo "<script>redirect('login.php');</script>";
$me=$_SESSION['user_id']; $viewUser=$_GET['user'] ?? null;
if($viewUser){
  $stmt=$pdo->prepare("SELECT * FROM users WHERE username=?"); $stmt->execute([$viewUser]); $u=$stmt->fetch();
}else{
  $stmt=$pdo->prepare("SELECT * FROM users WHERE id=?"); $stmt->execute([$me]); $u=$stmt->fetch();
}
if(!$u) exit('User not found');
$uid2=$u['id'];
// counts
$c1=$pdo->prepare("SELECT COUNT(*) FROM tweets WHERE user_id=?"); $c1->execute([$uid2]);
$c2=$pdo->prepare("SELECT COUNT(*) FROM follows WHERE follower_id=?"); $c2->execute([$uid2]);
$c3=$pdo->prepare("SELECT COUNT(*) FROM follows WHERE following_id=?"); $c3->execute([$uid2]);
list($tweetsCount)= $c1->fetch(); list($following)= $c2->fetch(); list($followers)= $c3->fetch();
// tweets
$stmt=$pdo->prepare("SELECT * FROM tweets WHERE user_id=? ORDER BY created_at DESC"); $stmt->execute([$uid2]); $tweets=$stmt->fetchAll();
// follow status
$isFollowing=false;
if($me!=$uid2){
  $s=$pdo->prepare("SELECT 1 FROM follows WHERE follower_id=? AND following_id=?");
  $s->execute([$me,$uid2]); $isFollowing=!!$s->fetch();
}
?>
<!DOCTYPE html><html><head><title>Profile</title></head><body>
<div class="header"><button class="btn" onclick="redirect('home.php')">Home</button>
<?php if($me==$uid2){ ?><button class="btn" onclick="redirect('logout.php')">Logout</button><?php }?></div>
<div class="card">
  <img src="<?=$u['profile_pic']?:'default.png'?>" width="80"><h2><?=htmlspecialchars($u['display_name'])?></h2>
  <p><?=htmlspecialchars($u['bio'])?></p><p>Tweets: <?=$tweetsCount?> • Following: <?=$following?> • Followers: <?=$followers?></p>
  <?php if($me!=$uid2): ?>
    <button class="btn" onclick="redirect('follow.php?u=<?=htmlspecialchars($u['username'])?>')">
    <?=$isFollowing?'Unfollow':'Follow'?></button>
  <?php endif; ?>
</div>
<?php foreach($tweets as $t): ?>
<div class="card">
  <div><?=date('j M H:i',strtotime($t['created_at']))?></div><p><?=nl2br(htmlspecialchars($t['content']))?></p>
  <?php if($me==$uid2): ?>
    <button class="btn" onclick="redirect('tweet.php?edit=<?=$t['id']?>')">Edit</button>
    <button class="btn" onclick="redirect('tweet.php?del=<?=$t['id']?>')">Delete</button>
  <?php endif; ?>
</div>
<?php endforeach; ?>
</body></html>
