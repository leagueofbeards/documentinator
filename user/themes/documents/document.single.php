<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
<div class="container">
	<?php $theme->display('sidebar'); ?>
	<div class="thirteen columns offset-by-three content">
		<?php if( $document->author->id == $user->id ) { ?>
		<div id="new_page">
			<a class="new_page" href="<?php URL::out('display_create', array('slug' => $document->slug)); ?>"><i class="icon-page">n</i></a>
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
			<a id="paragraph" href="#"><strong>p</strong></a>
			<a id="bold" href="#"><strong>b</strong></a>
			<a id="italic" href="#"><i>i</i></a>
			<a id="underline" href="#"><u>u</u></a>
			<a id="link" href="#"><i class="icon-link">l</i></a>
			<a id="code" href="#"><i class="icon-code">P</i></a>
		</div>
		<?php } ?>
		<div class="article page">
			<header><h1 <?php if( $document->author->id == $user->id ) { ?>contenteditable="true"<?php } ?>><?php echo $document->title_out; ?></h1></header>
			<hr class="large">
			<form id="update_doc" class="inplace" action="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'update_document', 'id' => $document->id))); ?>">
			<div class="doc-section body editable" id="intro" name="content" <?php if( $document->author->id == $user->id ) { ?>contenteditable="true" designmode="on"<?php } ?>>
				<?php echo $document->content_out; ?>
			</div>
			</form>
		</div>
	</div>
</div>
<?php $theme->display('footer'); ?>