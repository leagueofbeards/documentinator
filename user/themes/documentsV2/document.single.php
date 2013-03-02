<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
	<?php $theme->display('document.sidebar'); ?>
	<div class="thirteen columns offset-by-three omega">
		<div class="article page">
			<header><h1 contenteditable="true"><?php echo $document->title_out; ?></h1></header>
			<hr class="large">
			<form id="create_document" class="inplace" action="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'create_document'))); ?>">
			<div class="doc-section body editable" id="intro" name="content" contenteditable="true" designmode="on">
				<?php echo $document->content_out; ?>
			</div>
			</form>
		</div>
	</div>
<?php $theme->display('footer'); ?>