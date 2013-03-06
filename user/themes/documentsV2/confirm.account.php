<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
<div class="thirteen columns offset-by-four omega">
	<div class="article page">
		<header><h1>Almost There!</h1></header>
		<hr class="large">
		<p>We just need to know a little bit more about you before we can let you in.</p>
		<form id="user_update" class="ajax" action="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'update_approver'))); ?>">
			<input type="hidden" name="id" id="id" value="<?php echo $person->id; ?>">
			<input type="text" name="name" id="name" placeholder="Your Name"><br>
			<input type="text" name="email" id="email" placeholder="Your Email" value="<?php echo $person ? $person->email : ''; ?>"><br>
			<input type="password" placeholder="Passphrase" name="password" id="password"><br>
			<input type="submit" value="Let me in!">
		</form>
	</div>
</div>	
<?php $theme->display('footer'); ?>