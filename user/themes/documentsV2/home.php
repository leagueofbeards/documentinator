<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
<div class="sixteen column">
<?php if( isset($docs) ) { ?>
		<?php $i = 1; ?>
		<?php foreach( $docs as $doc ) { ?>
		<div class="five columns">
			<div class="document columns five">
				<header><a href="<?php echo $doc->permalink; ?>"><?php echo $doc->title; ?></a></header>
				<div class="meta">
					<ul>
						<?php if( $doc->is_approved ) { ?>
							<li class="when"><i class="icon-time">T</i>Finished <?php echo DateTime::create( $doc->updated )->friendly(1); ?></li>
						<?php } else { ?>
							<li class="when"><i class="icon-time">T</i>Started <?php echo DateTime::create( $doc->updated )->friendly(1); ?></li>					
						<?php } ?>
					</ul>
				</div>
				<div class="body">
					<p><?php echo strip_tags( $doc->content_excerpt ); ?></p>
				</div>
				<div class="attending">
					<p>Participating</p>
					<div class="person">
						<?php if( DocumentsPlugin::check_approvals( $doc->author, $doc ) == true ) { ?><span class="status"><i class="icon-status">c</i></span><?php } ?>
						<?php Gravatar::show( $doc->author->email ); ?>
					</div>
					<?php foreach( $doc->attached as $approvers ) { ?>
						<div class="person">
							<?php if( DocumentsPlugin::check_approvals( $approver, $doc ) == true ) { ?><span class="status"><i class="icon-status">c</i></span><?php } ?>
							<?php Gravatar::show( $approvers->email ); ?>							
						</div>
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