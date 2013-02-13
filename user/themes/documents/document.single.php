<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
<div class="container">
	<?php $theme->display('sidebar'); ?>
	<div class="thirteen columns offset-by-three content">
		<?php $theme->display('editor'); ?>
		<div class="article page">
			<header><h1 <?php if( $document->get_permissions()->edit ) { ?>contenteditable="false"<?php } ?>><?php echo $document->title_out; ?></h1></header>
			<hr class="large">
			<form id="update_doc" class="inplace" action="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'update_document', 'id' => $document->id))); ?>">
			<div class="doc-section body editable" id="intro" name="content" <?php if( $document->get_permissions()->edit ) { ?>contenteditable="false" designmode="on"<?php } ?>>
				<?php echo $document->content_out; ?>
			</div>
			</form>
		</div>
	</div>
</div>
<?php $theme->display('footer'); ?>