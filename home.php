<?php require 'db.php'; session_start();
if(!isset($_SESSION['user_id'])) echo "<script>redirect('login.php');</script>";
$uid=$_SESSION['user_id'];
// compose tweet
if($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['tweet'])){
  $stmt=$pdo->prepare("INSERT INTO tweets (user_id,content) VALUES (?,?)");
  $stmt->execute([$uid,trim($_POST['tweet'])]);
  echo "<script>redirect('home.php');</script>"; exit;
}
// fetch feed
$sql="SELECT t.*,u.username,u.profile_pic,
 (SELECT COUNT(*) FROM likes WHERE tweet_id=t.id) AS likes,
 (SELECT COUNT(*) FROM comments WHERE tweet_id=t.id) AS comments
 FROM tweets t JOIN users u ON u.id=t.user_id
 WHERE t.user_id=? OR t.user_id IN (SELECT following_id FROM follows WHERE follower_id=?)
 ORDER BY t.created_at DESC";
$stmt=$pdo->prepare($sql); $stmt->execute([$uid,$uid]); $tweets=$stmt->fetchAll();
?>
<!DOCTYPE html><html><head><title>Home</title></head><body>
<div class="header"><span>MyTwit</span><div>
  <button class="btn" onclick="redirect('profile.php')">Profile</button>
  <button class="btn" onclick="redirect('logout.php')">Logout</button>
</div></div>
<div class="card">
  <form method="POST">
    <textarea name="tweet" rows="3" placeholder="What's happening?"></textarea>
    <button class="btn" type="submit">Tweet</button>
  </form>
</div>
<?php foreach($tweets as $t): ?>
  <div class="card">
    <div><strong><?=htmlspecialchars($t['username'])?></strong> <small><?=date('j M H:i',strtotime($t['created_at']))?></small></div>
    <p><?=nl2br(htmlspecialchars($t['content']))?></p>
    <div>
      <button onclick="redirect('like.php?tid=<?=$t['id']?>')">👍<?=$t['likes']?></button>
      <button onclick="redirect('comment.php?tid=<?=$t['id']?>')">💬<?=$t['comments']?></button>
      <button onclick="redirect('profile.php?user=<?=htmlspecialchars($t['username'])?>')">View</button>
    </div>
  </div>
<?php endforeach; ?>
</body></html>
