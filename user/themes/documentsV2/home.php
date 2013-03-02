<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
<div class="sixteen columns clearfix">
<?php if( isset($docs) ) { ?>
	<?php $i = 1; ?>
	<?php foreach( $docs as $doc ) { ?>
	<div class="five columns">
		<div class="document columns five">
			<header><a href="<?php echo $doc->permalink; ?>"><?php echo $doc->title; ?></a></header>
			<div class="meta">
				<ul>
					<li class="when"><i class="icon-time">T</i><?php echo DateTime::create( $doc->updated )->friendly(1); ?></li>
				</ul>
			</div>
			<div class="body">
				<p><?php echo strip_tags($doc->content_excerpt); ?></p>
			</div>
			<div class="attending">
				<p>Participating</p>
				<?php Gravatar::show( $doc->author->email ); ?>
				<?php foreach( $doc->attached as $approvers ) { ?>
					<?php Gravatar::show( $approvers->email ); ?>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php if( ($i % 3) == 0 ) { ?>
		<div class="clear"></div>
	<?php } ?>
	<?php $i++; ?>
<?php } } ?>
</div>
<?php $theme->display('footer'); ?>