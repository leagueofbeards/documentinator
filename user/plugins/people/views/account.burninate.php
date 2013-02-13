<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
<div class="container">
	<?php $theme->display('account.sidebar'); ?>
	<div class="thirteen columns offset-by-three content">
		<div class="article page">
			<header><h1>Burninate!</h1></header>
			<hr class="large">
			<div class="doc-section body editable" id="intro" data-button-class="all">
				<p>We're sorry to see you go, but we understand.</p>
				<p><strong>Once you click the scary button below we will burninate all your data. This is defcon 1, ground zero, scorched earth time. Make sure you have backed up any data you want to save.</strong></p>
				<form>
					<input type="submit" value="Burninate the Peasants!">
				</form>
			</div>
		</div>
	</div>
</div>
<?php $theme->display('footer'); ?>