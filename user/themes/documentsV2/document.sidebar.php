<?php namespace Habari; ?>
<div id="sidebar" class="four columns alpha">
	<h3><?php echo $document->title_out; ?></h3>
	<?php if( isset($pages[0]) ) { ?>
		<ul id="pages">
			<?php foreach( $pages as $link ) { ?>
				<li><a href="<?php URL::out('display_docpage', array('slug' => $document->slug, 'page' => $link->name)); ?>"><?php echo $link->title; ?></a></li>
			<?php } ?>
		</ul>
		<?php } ?>
		<?php if( isset($page) ) { ?>
		<h5>Approvals</h5>
		<?php if( $page->is_approved == false ) { ?>
			<?php if( $page->author->id == $user->id ) { ?>
			<form id="invite_submit" style="display:none;margin-bottom:0px;margin-left:-1px;" action="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'add_approver', 'id' => $document->id))); ?>">
				<input type="text" name="invitee" id="invitee" placeholder="email@site.com" style="width:147px;">
			</form>
			<?php } ?>
		<?php } ?>
			<ul id="participating">
				<li>
					<?php Gravatar::show( $document->author->email ); ?>
					<strong><?php if( $document->author->id == $user->id ) { echo 'You'; } else { echo $document->author->displayname; } ?></strong>
					<?php if( $document->is_approved === true ) { ?>
						<span class="controls"><i class="icon-approve approved">c</i></span>
					<?php } elseif( $document->is_approved === false ) { ?>
						<span class="controls"><i class="icon-unknown">?</i></span>
					<?php } ?>
				</li>
				<?php foreach( $approvers as $approvee ) { ?>
				<li id="user-<?php echo $approvee->id ?>" class="other <?php if($document->get_permissions()->edit) { echo 'update'; } ?>" data-url="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'set_permissions', 'document' => $document->id, 'id' => $approvee->id))); ?>">
					<?php Gravatar::show( $approvee->email ); ?>
					<span class="<?php if( $approvee->in_group('quarantine') ) { echo 'grey'; } ?>"><?php if( $approvee->id == $user->id ) { echo 'You'; } else { ?><?php echo $approvee->displayname; ?><?php } ?></span>
						<?php if( $document->is_approved === true ) { ?>
						<span class="controls"><i class="icon-approve approved">c</i></span>
					<?php } elseif( $document->is_approved === false ) { ?>
							<span class="controls"><i class="icon-unknown">?</i></span>
					<?php } ?>
				</li>
				<?php } ?>
			</ul>
	<?php } ?>
</div>