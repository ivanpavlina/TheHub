<?php
require_once('conf/conf.main.php');
require_once('inc/audit.php');

logMessage("Index auth pending");
require_once('inc/auth.php');
if (!authorize_session()) {
    logMessage("Index auth fail, redirecting to login");

    ob_start();
    header('Location: /login.php');
    ob_end_flush();
    die();
}
logMessage("Index auth ok");

// Default section if not set
if (!isset($_REQUEST['section'])) {
    $_REQUEST['section'] = 'hub_index';
}

// Set constant so we can check in sections if they are called through here
define("ADMIN_LAYOUT", True);

$availableSections = array('network' => 'Network Monitoring');//,
                           //'finances' => 'My Finances');
?>

<html lang="en">
  <head>
    <script>
      //SESSIONID = "<?php //echo md5($_COOKIE['PHPSESSID']); ?>";
    </script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>The Hub</title>
    <meta name="description" content="The Hub">
    <meta name="author" content="ExitCode">

    <!--[if lt IE 9]>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
    <![endif]-->

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <!-- Template CSS -->
    <link href="css/sb-admin-2.css" rel="stylesheet">
    <link href="css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="css/custom.css">
    <link rel="stylesheet" type="text/css" href="css/card.css">
  </head>
  <body>
    <div id="wrapper">
      <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php?section=hub_index">The Hub</a>
            <div class="navbar-right" id="logout-button">
              <a class="btn btn-info btn-lg" href="logout.php">
                <span class="glyphicon glyphicon-log-out"></span> Log out
              </a>
            </div>
          </div>
          <!-- /.navbar-header -->
          <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
              <ul class="nav" id="side-menu">
                <?php foreach($availableSections as $index => $description): ?>
                  <li><a href="index.php?section=<?php echo $index; ?>"><?php echo $description; ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </nav>
        <div id="page-wrapper">
          <?php
            include("sections/".$_REQUEST['section'].".php");
          ?>
        </div>
    </div>   

    <!-- JQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <!-- JsRender -->
    <script src="//www.jsviews.com/download/jsrender.js"></script>


    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <!-- Custom JS -->
    <script type="text/javascript" src="js/<?php echo $_REQUEST['section'].'.js'?>"></script>
  </body>
</html>
