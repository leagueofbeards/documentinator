<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
<div class="container">
	<?php $theme->display('sidebar'); ?>
	<div class="thirteen columns offset-by-three content">
		<div id="new_page">
			<a class="new_page" href="#"><i class="icon-page">n</i></a>
		</div>
		<div id="editor">
			<div id="save">
				<a class="save" href="#"><i class="icon-save">s</i></a>
			</div>
			<a id="heading" href="#"><strong>H</strong></a>
			<div id="submenu">
				<ul>
					<li><a href="#" class="heading" data-level="H1">h1</a></li>
					<li><a href="#" class="heading" data-level="H2">h2</a></li>
					<li><a href="#" class="heading" data-level="H3">h3</a></li>
					<li><a href="#" class="heading" data-level="H4">h4</a></li>
					<li><a href="#" class="heading" data-level="H5">h5</a></li>
				</ul>
			</div>			
			<a id="bold" href="#"><strong>b</strong></a>
			<a id="italic" href="#"><i>i</i></a>
			<a id="underline" href="#"><u>u</u></a>
			<a id="code" href="#"><i class="icon-code">P</i></a>
		</div>
		<div class="article page">
			<header><h1><?php echo $document->title_out; ?></h1></header>
			<hr class="large">
			<form id="update_doc" action="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'update_document', 'id' => $document->id))); ?>">
			<div class="doc-section body editable" id="intro" name="content" contenteditable="true" designmode="on">
				<?php echo $document->content_out; ?>
			</div>
			</form>
		</div>
	</div>
</div>
<?php $theme->display('footer'); ?>