<?php namespace habari; ?>
<?php $theme->display('header'); ?>
<div class="container">
	<?php $theme->display('sidebar'); ?>
	<div class="thirteen columns offset-by-three">
		<div class="article page home">
			<header><h1>Your Documents</h1></header>
			<hr class="large">
			<div id="docs" class="thirteen columns content">
				<div class="nine columns">
				<?php if( isset($docs) ) { ?>
					<?php foreach( $docs as $doc ) { ?>
						<div id="doc-<?php echo $doc->id; ?>" class="doc_container grid_2 columns">
							<div class="document">
								<i class="icon-doc"><a href="<?php URL::out('display_document', array('slug' => $doc->slug)); ?>" title="">D</a></i>
							</div>
							<p><?php echo $doc->title; ?></p>
						</div>
					<?php } ?>
				</div>
				<div class="three columns" style="float:right;">
					<div class="grid_2 columns add_doc">
						<p><i class="icon-add"><a href="<?php URL::out('display_create_doc'); ?>" title="Add a new Document" role="button" data-toggle="modal">n</a></i></p>
					</div>
				</div>
				<?php } else { ?>
					<div class="grid_2 columns add_doc">
						<p><i class="icon-add"><a href="<?php URL::out('display_create_doc'); ?>" title="Add a new Document" role="button" data-toggle="modal">n</a></i></p>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<?php $theme->display('footer'); ?>