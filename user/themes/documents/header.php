<?php namespace Habari; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<title><?php Options::out('title'); ?> &raquo; <?php echo $theme->title; ?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="robots" content="index, follow">

<link rel="stylesheet" href="<?php Site::out_url('theme'); ?>/style.css" media="screen" type="text/css">
<link rel="stylesheet" href="<?php Site::out_url('theme'); ?>/css/bootstrap.css" media="screen" type="text/css">
<link rel="stylesheet" href="<?php Site::out_url('theme'); ?>/css/humanmsg.css" media="screen" type="text/css">
<link rel="stylesheet" href="<?php Site::out_url('theme'); ?>/css/annotator.min.css" media="screen" type="text/css">
<link href="//get.pictos.cc/fonts/2135/10" rel="stylesheet" type="text/css">
              
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="<?php Site::out_url('theme'); ?>/js/bootstrap.min.js"></script>
<script src="<?php Site::out_url('theme'); ?>/js/rangy-core.js"></script>
<script src="<?php Site::out_url('theme'); ?>/js/html5.wyswyg.js"></script>
<script src="<?php Site::out_url('theme'); ?>/js/humanmsg.js"></script>

<script>
	if ( typeof(DI) == "undefined" ) { DI = {}; }
		DI.url = "<?php Site::out_url('habari'); ?>";
		DI.user_id = "<?php echo $user->id; ?>";
		DI.username = "<?php echo $user->username; ?>";
		DI.post_id = "<?php echo $theme->post_id ? $theme->post_id : ''; ?>";
		DI.WSSE = <?php echo json_encode(Utils::WSSE()); ?>;
		DI.WSSE_update = '<?php echo URL::get('auth_ajax', array('context' => 'wsse_update')); ?>';
</script>

<?php echo $theme->header(); ?>
</head>
<body>
<div id="masthead" class="container">
<header>
	<h1><a href="<?php Site::out_url('habari'); ?>" title="Go Home"><?php Options::out('title'); ?></a></h1>
	<menu>
		<ul>
		<li id="auth"><a href="<?php Site::out_url('habari'); ?>/auth/logout">Logout</a></li>
		<li><a href="<?php URL::out('display_useraccount', array('slug' => $user->username)); ?>" title="Your Account"><?php Gravatar::show( $user->email ); ?></a></li>
		</ul>
	</menu>
</header>
</div>