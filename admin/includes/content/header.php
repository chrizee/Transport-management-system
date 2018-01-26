<?php
  require_once '../core/init.php';
  try {
    $init = Info::get();
  } catch (Exception $e) {
    print_r($e->getMessage());
  }
  $user = new User();
  $parkObj = new Park();
  if(!$user->isLoggedIn()) {
    Session::flash('home', "you need to login to access that page");
    Redirect::to('login');
  }
  if(!empty($user->getMeta()->results())) $meta = $user->getMeta()->first();
  $notification = new Notification();
  $notice = array_reverse($notification->getN($user->data()->groups, $user->data()->location));
  $count = 0 ;   //stores no of unseen notification
  foreach($notice as $key => $value) {
    if($value->status == 0 && weekCheck($value->date)) {   //any notification more than a week is no longer new
      $count++;
    }
  }

  $messageObj = new Message();
  $message = array_reverse($messageObj->getN($user->data()->id, $user->data()->location));
  $trash = array_reverse($messageObj->getN($user->data()->id, $user->data()->location,true));
  $newMail = 0;
  foreach ($message as $key => $value) {
    if($value->receiver_status == Config::get('message/not_read')) {
      $newMail++;
    }
  }
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $init->title ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta http-equiv="Cache-control" content="private">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/iCheck/flat/blue.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="plugins/morris/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="plugins/jvectormap/jquery-jvectormap-1.2.2.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="plugins/datepicker/datepicker3.css">
  <!-- fullCalendar 2.2.5-->
  <link rel="stylesheet" href="plugins/fullcalendar/fullcalendar.min.css">
  <link rel="stylesheet" href="plugins/fullcalendar/fullcalendar.print.css" media="print">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/iCheck/square/blue.css">
  <?php
  if(basename($_SERVER['PHP_SELF'], '.php') == "mailbox") {
  ?>
  <link rel="stylesheet" href="plugins/iCheck/flat/blue.css">
<?php } ?>
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="plugins/select2/select2.min.css">
  <!--font-awesome style-->
  <link rel="stylesheet" href="../font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="../css/main.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/Admin.min.css">
<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<style type="text/css">
   div.form-group.no-margin-on-form {
    margin-left: 0;
    margin-right: 0;
    margin-bottom: 1em;
  }
  .text-default {
    color:#000;
  }
</style>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition skin-black-light sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="dashboard" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><?php echo $init->header_mini ?></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b><?php echo $init->header ?></b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Messages: style can be found in dropdown.less-->
          <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-success"><?php echo ($count != 0) ? $count: ''; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have <?php echo ($count != 0)? $count: 'no'; ?> new notification</li>
              <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu">
                  <?php foreach ($notice as $key => $value) { ?>
                  <li><!-- start message -->
                    <a href="<?php echo $notification->links[$value->category]; ?>">
                      <div class="pull-left">
                        <img src="<?php
                        $userNotice = new User($value->initiated);
                        $n = $userNotice->getMeta()->first();
                        echo (empty($n->pic_src)) ? 'img/avatar-male.png' : "img/{$n->pic_src}"?>" class="img-circle" alt="User Image">
                      </div>
                      <h4>
                        <?php echo $notification->headers[$value->category]?>
                        <small><i class="fa fa-clock-o"></i> <?php echo  $notification->date($value->date);?></small>
                      </h4>
                      <p> <?php echo (empty($notification->message[$value->category]))? $value->message : $notification->message[$value->category]?></p>
                    </a>
                  </li>
                  <?php } ?>
                </ul>
              </li>
            </ul>
          </li>
          <!-- Notifications: style can be found in dropdown.less -->
          <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-envelope-o"></i>
              <span class="label label-warning"><?php echo ($newMail == 0) ? '' : $newMail; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have <?php echo ($newMail == 0)? 'no' : $newMail; ?> messages</li>
              <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu">
                  <?php
                    foreach ($message as $key => $value) {
                      if($key >= 10) break;
                  ?>
                  <li style="position:relative;">
                    <a href="read-mail_<?php echo encode($value->id);?>">
                      <i class="fa fa-envelope <?php if($value->receiver_status == Config::get('message/not_read')) echo 'text-aqua'?>"></i> <?php echo $value->subject?>
                      <span style="position:absolute;right:6px;top:10px;font-size:9px" class="pull-right"><i class="fa fa-clock-o"></i> <?php echo  $messageObj->date($value->date);?></span>
                    </a>
                  </li>
                  <?php } ?>
                </ul>
              </li>
              <li class="footer"><a href="mailbox">View all</a></li>
            </ul>
          </li>
          <!-- Tasks: style can be found in dropdown.less -->
          <li class="dropdown tasks-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-flag-o"></i>
              <span class="label label-danger">9</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have 9 tasks</li>
              <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu">
                  <li><!-- Task item -->
                    <a href="#">
                      <h3>
                        Design some buttons
                        <small class="pull-right">20%</small>
                      </h3>
                      <div class="progress xs">
                        <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                          <span class="sr-only">20% Complete</span>
                        </div>
                      </div>
                    </a>
                  </li>
                  <!-- end task item -->
                  <li><!-- Task item -->
                    <a href="#">
                      <h3>
                        Create a nice theme
                        <small class="pull-right">40%</small>
                      </h3>
                      <div class="progress xs">
                        <div class="progress-bar progress-bar-green" style="width: 40%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                          <span class="sr-only">40% Complete</span>
                        </div>
                      </div>
                    </a>
                  </li>
                  <!-- end task item -->
                  <li><!-- Task item -->
                    <a href="#">
                      <h3>
                        Some task I need to do
                        <small class="pull-right">60%</small>
                      </h3>
                      <div class="progress xs">
                        <div class="progress-bar progress-bar-red" style="width: 60%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                          <span class="sr-only">60% Complete</span>
                        </div>
                      </div>
                    </a>
                  </li>
                  <!-- end task item -->
                  <li><!-- Task item -->
                    <a href="#">
                      <h3>
                        Make beautiful transitions
                        <small class="pull-right">80%</small>
                      </h3>
                      <div class="progress xs">
                        <div class="progress-bar progress-bar-yellow" style="width: 80%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                          <span class="sr-only">80% Complete</span>
                        </div>
                      </div>
                    </a>
                  </li>
                  <!-- end task item -->
                </ul>
              </li>
              <li class="footer">
                <a href="#">View all tasks</a>
              </li>
            </ul>
          </li>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo (empty($meta->pic_src)) ? 'img/avatar-male.png' : "img/{$meta->pic_src}"?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo ucwords($user->data()->name); ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?php echo (empty($meta->pic_src)) ? 'img/avatar-male.png' : "img/{$meta->pic_src}"?>" class="img-circle" alt="User Image">

                <p>
                  <?php echo ucwords($user->data()->name); ?> - <?php echo $user->getTitle();?>
                  <small>Member since <?php $date = new DateTime($user->data()->created); echo $date->format('d-M-Y');?></small>
                </p>
              </li>
              <!-- Menu Body -->
              <li class="user-body">
                <div class="row">
                  <div class="col-xs-4 text-center">
                    <a href="#">Followers</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Sales</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Friends</a>
                  </div>
                </div>
                <!-- /.row -->
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="profile" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="-logout" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo (empty($meta->pic_src)) ? 'img/avatar-male.png' : "img/{$meta->pic_src}"?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo ucwords($user->data()->name); ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- search form -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        <li class="active treeview">
          <a href="dashboard">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
          </a>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-automobile"></i>
            <span>Travels</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
          <?php
            if($user->hasPermission('staff')) {
          ?>
            <li><a href="load"><i class="fa fa-circle-o"></i> Load new</a></li>
          <?php } ?>
            <li><a href="view"><i class="fa fa-circle-o"></i> View</a></li>
          </ul>
        </li>
        <?php
          if($user->checkPermission(array('staff'),false)) {
        ?>
        <li>
          <a href="waybill">
            <i class="fa fa-suitcase"></i> <span>Waybill</span>
          </a>
        </li>
        <?php } ?>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-lightbulb-o"></i>
            <span>View</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="viewstaff"><i class="fa fa-circle-o"></i> Staffs</a></li>
            <li><a href="viewvehicles"><i class="fa fa-circle-o"></i> Vehicles</a></li>
            <li><a href="viewlocation"><i class="fa fa-circle-o"></i> Location</a></li>
          </ul>
        </li>
        <li>
          <a href="passengers">
            <i class="fa fa-users"></i> <span> Passengers</span>
            <span class="pull-right-container">
              <small class="label pull-right bg-red">3</small>
              <small class="label pull-right bg-blue">17</small>
            </span>
          </a>
        </li>
        <?php
          if($user->hasPermission('admin') || $user->hasPermission('manager')) { ?>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-plus-square"></i>
                <span>Create</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="createstaff"><i class="fa fa-circle-o"></i> Staff</a></li>
                <li><a href="createvehicle"><i class="fa fa-circle-o"></i> Vehicle</a></li>
                <li><a href="createlocation"><i class="fa fa-circle-o"></i> Location</a></li>
              </ul>
            </li>
        <?php } ?>
        <li>
          <a href="account">
            <i class="fa fa-dollar"></i> <span>Accounts</span>
          </a>
        </li>
        <li class="treeview">
          <a href="mailbox">
            <i class="fa fa-envelope"></i> <span>Mailbox</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="active">
              <a href="mailbox">Inbox
                <span class="pull-right-container">
                  <span class="label label-primary pull-right"><?php echo ($newMail == 0) ? '' : $newMail; ?></span>
                </span>
              </a>
            </li>
            <li><a href="compose">Compose</a></li>
            <li><a href="sentmail">Sent</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="-logout">
            <i class="fa fa-dashboard"></i> <span>Logout</span>
          </a>
        </li>

      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
