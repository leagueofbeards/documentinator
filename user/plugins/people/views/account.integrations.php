<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
<div class="container">
	<?php $theme->display('account.sidebar'); ?>
	<div class="thirteen columns offset-by-three content">
		<div class="article page">
			<header><h1>Integrate this mother</h1></header>
			<hr class="large">
			<div class="doc-section body editable" id="intro" data-button-class="all">
				<p>Coworkspace Integrates with tons of other sites and services. Pick one below to get started.</p>
				<p><strong><a href="<?php URL::out('display_billing'); ?>" title="Upgrade your account">Upgrade to Pro</a> to setup integrations with Dropbox, Amazon S3, Github or Beanstalk.</strong></p>
			</div>
		</div>
	</div>
</div>
<?php $theme->display('footer'); ?>