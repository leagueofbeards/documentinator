<?php namespace Habari; ?>
<div class="three columns sidebar">
	<nav>
		<h3 id="logo"><a href="<?php Site::out_url('habari'); ?>" title="Go Home">Doco</a></h3>
		<ul>
			<?php foreach( $pages as $page ) { ?>
				<li><a href="<?php echo $page->permalink; ?>"><?php echo $page->title; ?></a></li>
			<?php } ?>
		</ul>
		<?php if( isset( $assigned ) ) { ?>
		<h5>Approvals <i class="icon-add" id="add_contributor"><a href="#new_approv_form" role="button" data-toggle="modal">a</a></i></h5>
		<ul id="participating">
			<li>
				<?php Gravatar::show( $post->author->email ); ?>
				<strong>You</strong>
				<span class="controls"><i class="icon-approve">c</i> <i class="icon-approve">x</i></span>
			</li>
			<li>
				<?php Gravatar::show( 'jakob@chrisjdavis.org' ); ?>
				Jakob Davis
				<span class="controls"><i class="icon-approve approved">c</i></span>
			</li>
			<li>
				<?php Gravatar::show( 'klein@leagueofbeards.com' ); ?>
				Klein Maetschke
				<span class="controls"><i class="icon-denial denied">x</i></span>
			</li>
			<li>
				<?php Gravatar::show( 'heather@chrisjdavis.org' ); ?>
				Heather Davis
				<span class="controls"><i class="icon-unknown bigger">?</i></span>
			</li>
		</ul>
		<?php } ?>
	</nav>
</div>