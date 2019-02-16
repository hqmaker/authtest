<?php
include 'system/start.php';
$error = '';

if(isset($_POST['submit'])) {
  $auth = new Auth($db);

  $username = isset($_POST['username']) ? check($_POST['username']) : '';
  $password = isset($_POST['password']) ? check($_POST['password']) : '';
  $password2 = isset($_POST['password2']) ? check($_POST['password2']) : '';
  $fullname = isset($_POST['fullname']) ? check($_POST['fullname']) : '';
  $email = isset($_POST['email']) ? check($_POST['email']) : '';

  if(strlen($password) < 6 || strlen($password) > 16) {
    $error .= '<div>Password min. 6 simvoll, max. 16 simvoll</div>';
  }

  if($password != $password2) {
    $error .= '<div>Repeat password is not correct</div>';
  }

  if(empty($username)) {
    $error .= '<div>Username is empty</div>';
  }

  if(empty($fullname)) {
    $error .= '<div>Fullname is empty</div>';
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error .= '<div>Email is incorrect</div>';
  }

  if($auth->isEmail($email)) {
    $error .= '<div>Email already exists</div>';
  }

  if($auth->isUsername($username)) {
    $error .= '<div>Username already exists</div>';
  }

  if($error == '' && $auth->registerProfile($username, $password, $fullname, $email)) {
    $auth->authenticateUser($username, $password);

    header('Location: /blog.php');
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
      <form action="/register.php" method="POST">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" class="form-control" id="username" name="username" placeholder="Username">
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Password">
        </div>
        <div class="form-group">
          <label for="password2">Repeat password</label>
          <input type="password" class="form-control" id="password2" name="password2" placeholder="Repeat password">
        </div>
        <div class="form-group">
          <label for="fullname">Fullname</label>
          <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Fullname">
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="Email">
        </div>
        <input type="submit" name="submit" value="Submit" class="btn btn-default">
      </form>
    </div>
  </div>
</div>
</body>
</html>
