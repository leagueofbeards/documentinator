<?php namespace Habari; ?>
<div class="three columns sidebar">
	<nav>
		<h3 id="logo"><a href="<?php Site::out_url('habari'); ?>" title="Go Home">Home</a></h3>
	<?php if( isset($document) ) { ?>
		<h5><a href="<?php URL::out('display_document', array('slug' => $document->slug)); ?>" title="Go Home"><?php echo $document->title; ?></a></h5>
	<?php } ?>
		<?php if( isset($pages[0]) ) { ?>
		<hr style="margin-bottom:10px;">		
		<ul>
			<?php foreach( $pages as $page ) { ?>
				<li><a href="<?php URL::out('display_docpage', array('slug' => $document->slug, 'page' => $page->name)); ?>"><?php echo $page->title; ?></a></li>
			<?php } ?>
		</ul>
		<?php } ?>
		<?php if( isset($document) ) { ?>
		<hr style="margin-bottom:10px;">
		<h5>Approvals <?php if( $document->author->id == $user->id ) { ?><i class="icon-add" id="add_contributor"><a href="#new_approv_form" role="button" data-toggle="modal">a</a></i><?php } ?></h5>
		<?php if( $document->author->id == $user->id ) { ?>
		<form id="invite_submit" style="display:none;margin-bottom:0px;margin-left:-1px;" action="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'add_approver', 'id' => $document->id))); ?>">
			<input type="text" name="invitee" id="invitee" placeholder="email@site.com" style="width:147px;">
		</form>
		<?php } ?>
		<ul id="participating">
			<li>
				<?php Gravatar::show( $document->author->email ); ?>
				<strong><?php if( $document->author->id == $user->id ) { echo 'You'; } else { echo $document->author->displayname; } ?></strong>
				<?php if( $document->author->id == $user->id ) { ?><span class="controls"><i class="icon-approve">c</i> <i class="icon-approve">x</i></span><?php } ?>
			</li>
			<?php foreach( $approvers as $approvee ) { ?>
			<li>
				<?php Gravatar::show( $approvee->email ); ?>
				<span class="<?php if( $approvee->in_group('quarantine') ) { echo 'grey'; } ?>"><?php if( $approvee->id == $user->id ) { echo 'You'; } else { ?><?php echo $approvee->displayname; ?><?php } ?></span>
				<?php if( $approvee->id == $user->id ) { ?><span class="controls"><i class="icon-approve">c</i> <i class="icon-approve">x</i></span><?php } ?>
			</li>
			<?php } ?>
		</ul>
		<?php } ?>
	</nav>
</div>