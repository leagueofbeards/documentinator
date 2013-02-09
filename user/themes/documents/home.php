<?php namespace habari; ?>
<?php $theme->display('header'); ?>
<div class="container">
	<?php $theme->display('sidebar'); ?>
	<div class="thirteen columns offset-by-three content">
		<?php if( isset($docs) ) { ?>
			<?php foreach( $docs as $doc ) { ?>
				<div id="doc-<?php echo $doc->id; ?>" class="document grid_2 columns">
					<a href="<?php URL::out('display_document', array('slug' => $doc->slug)); ?>" title=""><?php echo $doc->title; ?></a>
				</div>
			<?php } ?>
			<div class="grid_2 columns add_doc">
				<p><i class="icon-add"><a href="#new_doc" title="Add a new Document" role="button" data-toggle="modal">+</a></i></p>
				<p>Add a Document</p>
			</div>
		<?php } else { ?>
			<div class="add_doc">
				<p>Add a Document</p>
			</div>
		<?php } ?>
	</div>
</div>
<?php $theme->display('footer'); ?>