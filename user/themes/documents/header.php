<?php namespace Habari; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Doco &raquo; Eventcollab</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="robots" content="index, follow">
<link rel="stylesheet" href="<?php Site::out_url('theme'); ?>/style.css" media="screen" type="text/css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="<?php Site::out_url('theme'); ?>/js/application.js"></script>
<script>
	if ( typeof(DI) == "undefined" ) { DI = {}; }
		DI.url = "<?php Site::out_url('habari'); ?>";
		DI.user_id = "<?php echo $user->id; ?>";
		DI.username = "<?php echo $user->username; ?>";
</script>

<?php echo $theme->header(); ?>
</head>
<body>