<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
	<div id="sidebar" class="four columns alpha">
		<nav>
			<h4><a href="<?php URL::out('display_document', array('slug' => $document->slug)); ?>"><?php echo $document->title_out; ?></a></h4>
			<?php if( isset($pages[0]) ) { ?>
			<ul id="pages">
			<?php foreach( $pages as $link ) { ?>
				<li><a href="<?php URL::out('display_docpage', array('slug' => $document->slug, 'page' => $link->name)); ?>"><?php echo $link->title; ?></a></li>
			<?php } ?>
			</ul>
		<?php } ?>
		</nav>
	</div>
	<div class="thirteen columns offset-by-four omega">
		<?php if( $document->is_approved == false ) { ?>
			<?php $theme->display('editor'); ?>
		<?php } ?>
		<div class="article page">
			<header><h1 contenteditable="true">My New Great Page</h1></header>
			<hr class="large">
			<form id="create_page" class="inplace" action="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'create_page', 'doc' => $document->id))); ?>">
				<div class="doc-section body editable" id="intro" name="content" contenteditable="true" designmode="on">
					<p>The powered flight took a total of about eight and a half minutes. It seemed to me it had gone by in a lash. We had gone from sitting still on the launch pad at the Kennedy Space Center to traveling at 17,500 miles an hour in that eight and a half minutes. </p>
					<p>It is still mind-boggling to me. I recall making some statement on the air-to-ground radio for the benefit of my fellow astronauts, who had also been in the program a long time, that it was well worth the wait.</p>
				</div>
			</form>
		</div>
	</div>
<?php $theme->display('footer'); ?>