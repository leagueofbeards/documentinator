<?php namespace Habari; ?>
<div id="sidebar" class="four columns alpha">
	<h3><a href="<?php URL::out('display_document', array('slug' => $document->slug)); ?>"><?php echo $document->title_out; ?></a></h3>
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
					<?php Gravatar::show( $page->author->email ); ?>
					<strong><?php if( $page->author->id == $user->id ) { echo 'You'; } else { echo $page->author->displayname; } ?></strong>
					<?php if( DocumentsPlugin::approved($page, $page->author) === true ) { ?>
						<span class="controls"><i class="icon-approve approved">c</i></span>
					<?php } elseif( DocumentsPlugin::approved($page, $page->author) === false ) { ?>
						<span class="controls"><i class="icon-reject denied">x</i></span>
					<?php } else { ?>
							<?php if( $page->author->id == $user->id ) { ?><span class="controls"><i data-url="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'approval', 'id' => $page->id, 'action' => 'approve'))); ?>" class="icon-approve wsse">c</i> <i data-url="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'approval', 'id' => $page->id, 'action' => 'reject'))); ?>" class="icon-reject wsse">x</i></span><?php } ?>
					<?php } ?>
				</li>
				<?php foreach( $approvers as $approvee ) { ?>
				<li id="user-<?php echo $approvee->id ?>" class="other <?php if($document->get_permissions()->edit) { echo 'update'; } ?>" data-url="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'set_permissions', 'document' => $page->id, 'id' => $approvee->id))); ?>">
					<?php Gravatar::show( $approvee->email ); ?>
					<span class="<?php if( $approvee->in_group('quarantine') ) { echo 'grey'; } ?>"><?php if( $approvee->id == $user->id ) { echo 'You'; } else { ?><?php echo $approvee->displayname; ?><?php } ?></span>
						<?php if( DocumentsPlugin::approved($page, $approvee) === true ) { ?>
						<span class="controls"><i class="icon-approve approved">c</i></span>
					<?php } elseif( DocumentsPlugin::approved($page, $approvee) === false ) { ?>
						<?php if( $approvee->id == $user->id ) { ?>
						<span class="controls"><i data-url="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'approval', 'id' => $page->id, 'action' => 'approve'))); ?>" class="icon-approve wsse">c</i> <i data-url="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'approval', 'id' => $page->id, 'action' => 'reject'))); ?>" class="icon-reject denied wsse">x</i></span>
						<?php } else { ?>
							<span class="controls"><i class="icon-reject denied">x</i></span>
						<?php } ?>
					<?php } else { ?>
						<?php if( $approvee->id == $user->id ) { ?><span class="controls"><i data-url="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'approval', 'id' => $page->id, 'action' => 'approve'))); ?>" class="icon-approve wsse">c</i> <i data-url="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'approval', 'id' => $page->id, 'action' => 'reject'))); ?>" class="icon-reject wsse">x</i></span><?php } ?>
					<?php } ?>
				</li>
				<?php } ?>
			</ul>
	<?php } ?>
</div>