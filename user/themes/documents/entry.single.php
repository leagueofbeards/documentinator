<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
<div class="container">
	<div class="three columns sidebar">
		<nav>
			<h3 id="logo"><a href="<?php Site::out_url('habari'); ?>" title="Go Home">Doco</a></h3>
			<ul>
				<?php foreach( $pages as $page ) { ?>
					<li><a href="<?php echo $page->permalink; ?>"><?php echo $page->title; ?></a></li>
				<?php } ?>
			</ul>
		</nav>
	</div>
	<div class="thirteen columns offset-by-three content">
		<div style="box-shadow: 0px 0px 5px #ddd; padding:10px;background:#fff;">
			<header><h1><?php echo $post->title_out; ?></h1></header>
			<hr class="large">
			<div class="doc-section" id="intro">
				<?php echo $post->content_out; ?>
			</div>
		</div>
	</div>
</div>
<?php $theme->display('footer'); ?>