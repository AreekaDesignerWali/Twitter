<?php require 'db.php'; session_start();
if($_SERVER['REQUEST_METHOD']==='POST'){
  $stmt=$pdo->prepare("SELECT * FROM users WHERE username=?");
  $stmt->execute([$_POST['username']]);
  if($u=$stmt->fetch()){
    if(password_verify($_POST['password'],$u['password_hash'])){
      $_SESSION['user_id']=$u['id']; echo "<script>redirect('home.php');</script>"; exit;
    }
  }
  $error='Invalid credentials';
}
?>
<!DOCTYPE html><html><head><title>Login</title></head><body>
<div class="card"><h2>Login</h2>
<form method="POST">
  <input name="username" placeholder="Username" required>
  <input type="password" name="password" placeholder="Password" required>
  <button class="btn" type="submit">Login</button>
</form>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<p>No account? <a href="signup.php">Sign up</a></p>
</div></body></html>
