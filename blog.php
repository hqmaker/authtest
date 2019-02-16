<?php
include 'system/start.php';

$auth = new Auth($db);

if(!$auth->isAuthenticated()) {
  header('Location: /index.php');
}

$userId = $auth->getUserId();
$userName = $auth->getUserName();

if(isset($_POST['submit'])) {
  $articleName = isset($_POST['articleName']) ? check($_POST['articleName']) : '';
  $articleDesc = isset($_POST['articleDesc']) ? check($_POST['articleDesc']) : '';

  if(!empty($articleName) && !empty($articleDesc)) {
    $db->query('INSERT INTO articles
                  (user_id,
                  article_name,
                  article_desc,
                  created)
                VALUES
                  (' . $userId . ',
                  "' . $db->escape_string($articleName) . '",
                  "' . $db->escape_string($articleDesc) . '",
                  "' . date('Y-m-d H:i:s') . '")
                ');
  }
}

include 'includes/header.php';
?>
<div class="container">
  <div class="row">
    <div class="col">
      <form action="/blog.php" method="POST">
        <div class="form-group">
          <label for="article">Article name</label>
          <input type="text" class="form-control" name="articleName" id="article" placeholder="Article name">
        </div>
        <div class="form-group">
          <label for="text">Text</label>
          <textarea name="articleDesc" id="text" class="form-control" placeholder="Text"></textarea>
        </div>
        <input type="submit" name="submit" value="Submit" class="btn btn-default">
      </form>
    </div>

    <hr>

    <ul>
        <?php
        $dbResult = $db->query('SELECT article_name, article_desc, created FROM articles WHERE user_id = ' . $userId);

        while(list($dbArticleName, $dbArticleDesc, $dbCreated) = $db->fetch_row($dbResult)) {
          echo '
          <li>
            <span>' . $userName . ' (' . $dbCreated . ') - <strong>' . $dbArticleName . '</strong></span>
            <p>' . $dbArticleDesc . '</p>
          </li>
          ';
        }
        ?>
    </ul>
  </div>

</div>
</body>
</html>
