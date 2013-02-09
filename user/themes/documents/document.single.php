<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
<div class="container">
	<?php $theme->display('sidebar'); ?>
	<div class="thirteen columns offset-by-three content">
		<div id="editor">
			<a id="bold" href="#"><strong>b</strong></a>
			<a id="italic" href="#"><i>i</i></a>
			<a id="underline" href="#"><u>u</u></a>
			<a id="code" href="#"><i class="icon-code">P</i></a>
		</div>
		<div class="article page">
			<header><h1><?php echo $document->title_out; ?></h1></header>
			<hr class="large">
			<div class="doc-section body editable" id="intro" contenteditable="true" designmode="on">
				<?php echo $document->content_out; ?>
			</div>
		</div>
	</div>
</div>

<?php $theme->display('footer'); ?>