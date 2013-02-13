<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
<div class="container">
	<?php $theme->display('sidebar'); ?>
	<div class="thirteen columns offset-by-three content">
		<?php $theme->display('editor'); ?>
		<div class="article page">
			<header><h1 <?php if(  $document->get_permissions()->edit  ) { ?>contenteditable="false"<?php } ?>><?php echo $page->title_out; ?></h1></header>
			<hr class="large">
			<form id="update_page" class="inplace" action="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'update_page', 'id' => $page->id))); ?>">
			<div class="doc-section body editable" id="intro" name="content" <?php if(  $document->get_permissions()->edit  ) { ?>contenteditable="false" designmode="on"<?php } ?>>
				<?php echo $page->content_out; ?>
			</div>
			</form>
		</div>
	</div>
</div>
<?php $theme->display('footer'); ?>