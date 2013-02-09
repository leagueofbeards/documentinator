<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
<div class="container">
	<?php $theme->display('sidebar'); ?>
	<div class="thirteen columns offset-by-three content">
		<div class="article page">
			<header><h1><?php echo $document->title_out; ?></h1></header>
			<hr class="large">
			<div class="doc-section body editable" id="intro" data-button-class="all">
				<?php echo $document->content_out; ?>
			</div>
		</div>
	</div>
</div>
<?php $theme->display('footer'); ?>