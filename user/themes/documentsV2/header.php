<?php namespace Habari; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<title><?php Options::out('title'); ?> &raquo; <?php echo $theme->title; ?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="robots" content="index, follow">

<link rel="stylesheet" href="<?php Site::out_url('theme'); ?>/style.css" media="screen" type="text/css">
<link href="//get.pictos.cc/fonts/2135/10" rel="stylesheet" type="text/css">
              
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>

<script>
	if ( typeof(DI) == "undefined" ) { DI = {}; }
		DI.url = "<?php Site::out_url('habari'); ?>";
		DI.user_id = "<?php echo $user->id; ?>";
		DI.username = "<?php echo $user->username; ?>";
		DI.displayname = "<?php echo $user->displayname; ?>";
		DI.avatar = '<?php Gravatar::show( $user->email ); ?>';
		DI.post_id = "<?php echo $theme->post_id ? $theme->post_id : ''; ?>";
		DI.WSSE = <?php echo json_encode(Utils::WSSE()); ?>;
		DI.WSSE_update = '<?php echo URL::get('auth_ajax', array('context' => 'wsse_update')); ?>';
</script>
<?php echo $theme->header(); ?>
</head>
<body>
<header id="masthead">
		<nav>
			<ul>
				<li><a href="<?php URL::out('display_useraccount', array('slug' => $user->username)); ?>"><i class="icon-configure">g</i></a></li>
			</ul>
		</nav>
</header>
<div class="container">