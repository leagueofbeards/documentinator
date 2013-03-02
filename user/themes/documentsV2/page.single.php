<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
	<?php $theme->display('page.sidebar'); ?>
	<div class="thirteen columns offset-by-three omega">
		<div class="article page">
			<header><h1 contenteditable="true"><?php echo $page->title_out; ?></h1></header>
			<hr class="large">
			<form id="create_document" class="inplace" action="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'create_document'))); ?>">
			<div class="doc-section body editable" id="intro" name="content" contenteditable="true" designmode="on">
				<?php echo $page->content_out; ?>
			</div>
			</form>
		</div>
	</div>
<?php $theme->display('footer'); ?>