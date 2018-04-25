<?php

if (!defined("ADMIN_LAYOUT")) {
  ob_start();
  header('Location: /index.php');
  ob_end_flush();
  die();
}

?>

<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Welcome <?php echo $_SESSION['username'] ?></h1>
    </br>
    <img src="logo.png" alt="The Hub" id="logo" class="logo"></img>
  </div>
</div>