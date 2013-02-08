<?php $theme->display('header'); ?>
<div class="container">
	<div class="three columns sidebar">
		<nav>
			<h3 id="logo">Doco</h3>
			<ul>
				<?php foreach( $pages as $page ) { ?>
					<li><a href="<?php echo $page->permalink; ?>"><?php echo $page->title; ?></a></li>
				<?php } ?>
			</ul>
		</nav>
	</div>
	<div class="thirteen columns offset-by-three content">
		<div class="article page">
			<header><h1><?php echo $first->title_out; ?></h1></header>
			<hr class="large">
			<div class="doc-section body editable" id="intro" data-button-class="all">
				<?php echo $first->content_out; ?>
			</div>
		</div>
	</div>
</div>
<?php $theme->display('footer'); ?>