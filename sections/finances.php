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
    <h1 class="page-header">Dashboard</h1>
  </div>
</div>
<div class="row">
  <div class="col-lg-8">
    <div class="panel panel-default">
      <div class="panel-heading">
        <i class="fa fa-bar-chart-o fa-fw"></i> My Finances
      </div>
      <div class="panel-body">
        <div id="bandwidth-chart">
          <!-- Content inside box -->
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <i class="fa fa-cubes fa-fw"></i> Installments
      </div>
      <!-- /.panel-heading -->
      <div class="panel-body">
        <div id="installments-list" class="list-group"></div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-8">
    <div class="panel panel-default">
      <div class="panel-heading">
        <i class="fa fa-bar-chart-o fa-fw"></i> Current expenses
      </div>
      <div class="panel-body">
        <div id="bandwidth-chart">
          <!-- Content inside box -->
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <i class="fa fa-cubes fa-fw"></i> Create expense
      </div>
      <!-- /.panel-heading -->
      <div class="panel-body">
        <div id="installments-list" class="list-group">
          <form action="" id="form">
            <label for="email">Name:</label>
            <input type="text" name="name" id="name" placeholder="Gablec"><br>
            <label for="email">Price:</label>
            <input type="text" name="price" id="price" placeholder="52.44"><br>
            <button name="submit" id="submit">Add</button>
            <label id="info"></label>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>