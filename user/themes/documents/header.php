<?php namespace Habari; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Doco &raquo; Eventcollab</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="robots" content="index, follow">
<link rel="stylesheet" href="<?php Site::out_url('theme'); ?>/style.css" media="screen" type="text/css">
<link rel="stylesheet" href="<?php Site::out_url('theme'); ?>/css/etch.css" media="screen" type="text/css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.3.3/underscore-min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/backbone.js/0.9.2/backbone-min.js"></script>
<script src="http://etchjs.com/media/scripts/lib/rangy-core.js"></script>
<script src="<?php Site::out_url('theme'); ?>/js/etch.js"></script>

<script>
	if ( typeof(DI) == "undefined" ) { DI = {}; }
		DI.url = "<?php Site::out_url('habari'); ?>";
		DI.user_id = "<?php echo $user->id; ?>";
		DI.username = "<?php echo $user->username; ?>";
</script>

<?php echo $theme->header(); ?>
</head>
<body>