<?php
include 'system/start.php';
$error = '';

if(isset($_POST['submit'])) {
  $auth = new Auth($db);

  $username = isset($_POST['username']) ? check($_POST['username']) : '';
  $password = isset($_POST['password']) ? check($_POST['password']) : '';

  if($auth->authenticateUser($username, $password)) {
    header('Location: /blog.php');
  } else {
    $error .= '<div>Login or password incorrect</div>';
  }
}

include 'includes/header.php';

if($error != '') {
  echo '<div class="bg-danger">' . $error . '</div>';
}
?>
<div class="container">
  <div class="row">
    <div class="col">
      <form action="/index.php" method="POST">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" class="form-control" name="username" id="username" placeholder="Username">
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" name="password" id="password" placeholder="Password">
        </div>
        <input type="submit" name="submit" value="Submit" class="btn btn-default">
      </form><br>
      <a href="/register.php" class="btn btn-primary">Register</a>
    </div>
  </div>
</div>
</body>
</html>
