<?php
	require_once 'core/init.php';
	$user = new User();

	if(!$user->isLoggedIn()) {
		//Redirect::to('index.php');
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Valency solutions</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="css/main.css">
		<link rel="stylesheet" type="text/css" href="css/datatables.bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="css/responsive.bootstrap.min.css">
		<link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">

	</head>
<body>
	<div class="container">
	<header>
		<nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
	        <div class="container">
	            <div class="navbar-header">
	                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse">
	                    Menu <i class="fa fa-bars"></i>
	                </button>
	                <a class="navbar-brand page-scroll" href="#page-top">
	                    <i class="fa fa-play-circle"></i> <span class="light">valence</span> Solutions
	                </a>
	            </div>

	            <!-- Collect the nav links, forms, and other content for toggling -->
	            <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
	                <ul class="nav navbar-nav">
	                    <!-- Hidden li included to remove active class from about link when scrolled up past about section -->
	                    <li class="hidden">
	                        <a href="#page-top"></a>
	                    </li>
	                    <li>
	                        <a class="page-scroll" href="register.php">register</a>
	                    </li>
	                    <li>
	                        <a class="page-scroll" href="test.php">take test</a>
	                    </li>
	                    <li>
	                        <a class="page-scroll" href="login.php">login</a>
	                    </li>
	                    <li>
	                        <a class="page-scroll" href="about.php">about</a>
	                    </li>
	                    <li>
	                        <a class="page-scroll" href="contact.php">Contact</a>
	                    </li>
	                </ul>
	            </div>
	            <!-- /.navbar-collapse -->
	        </div>
	        <!-- /.container -->
   		</nav>
	</header>
	<div class="main">
	<?php 
		if(Session::exists('home')) {
       		echo "<p class='text-danger text-center'>". Session::flash('home'). "</p>";
    	}
    ?>