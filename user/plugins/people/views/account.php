<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
<div class="container">
	<?php $theme->display('account.sidebar'); ?>
	<div class="thirteen columns offset-by-three content">
		<div class="article page">
			<header><h1>Hi there <?php echo $person->displayname; ?></h1></header>
			<hr class="large">
			<div class="doc-section body editable" id="intro" data-button-class="all">
				<p>You can update your info below.</p>
				<form id="user_update" class="ajax" action="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'update_account'))); ?>">
					<input type="hidden" name="id" id="id" value="<?php echo $person->id; ?>">
					<input type="text" name="name" id="name" value="<?php echo $person->displayname; ?>" placeholder="Your Name"><br>
					<input type="text" name="username" id="username" value="<?php echo $person->username; ?>" placeholder="Your Username"><br>
					<input type="text" name="email" id="email" placeholder="Your Email" value="<?php echo $person ? $person->email : ''; ?>"><br>
					<input type="password" placeholder="Passphrase" name="password" id="password"><br>
					<input type="submit" value="Update Your Account!">
				</form>
			</div>
		</div>
	</div>
</div>
<?php $theme->display('footer'); ?>