<?php namespace Habari; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<title><?php Options::out('title'); ?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="robots" content="index, follow">
<link rel="stylesheet" href="<?php Site::out_url('theme'); ?>/style.css" media="screen" type="text/css">            
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
<link href="//get.pictos.cc/fonts/2135/11" rel="stylesheet" type="text/css">
<link href="http://vjs.zencdn.net/c/video-js.css" rel="stylesheet">
<script src="http://vjs.zencdn.net/c/video.js"></script>
<?php echo $theme->header(); ?>
</head>
<body>
<header>
	<div class="container">
		<div class="logo">
			<a href="<?php Site::out_url('habari'); ?>" title="Coworkspace">Cwkspace.us</a>
		</div>
		<ul id="menu">
			<li><a href="<?php Site::out_url('habari'); ?>">Home</a></li>
			<li><a href="#features_overview">Features</a></li>
			<li><a href="#plans">Plans</a></li>
			<li><a href="http://app.coworkspace.us">Login</a></li>
		</ul>
	</div>
</header>
<div id="intro">
	<div class="container">
		<h1>Welcome to the future of documents</h1>
		<h2>Collaborate and create revisions like a wiki, request approvals like a publishing<br>system, edit inline like a word processor. All from the cloud.</h2>
	</div>
</div>