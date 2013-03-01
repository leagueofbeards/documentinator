<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
<div class="sixteen columns clearfix">
<?php if( isset($docs) ) { ?>
	<?php $i = 1; ?>
	<?php foreach( $docs as $doc ) { ?>
	<?php if( $i == 1 ) { $which = ' alpha'; } else { $which = ''; } $i++; ?>
	<div class="five columns<?php echo $which; ?>">
		<div class="document columns five">
			<header><?php echo $doc->title; ?></header>
			<div class="meta">
				<ul>
					<li class="when"><i class="icon-time">T</i><?php echo DateTime::create( $doc->updated )->friendly(1); ?></li>
				</ul>
			</div>
			<div class="body">
				<?php echo $doc->content_excerpt; ?>
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
<?php } } ?>
	<div class="five columns omega">
		<div class="new_doc document columns five">
			<header>Create A New Document</header>
			<div class="meta">
				<ul>
					<li class="when"><i class="icon-time">T</i>Right Now!</li>
				</ul>
			</div>
			<div class="body">
				<p>Click me to create a new document! Don't just stand there, click away <?php echo $user->displayname; ?>!</p>
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
</div>
<?php $theme->display('footer'); ?>