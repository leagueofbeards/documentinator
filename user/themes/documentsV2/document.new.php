<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
	<div id="sidebar" class="three columns alpha">
		<h3>New Document</h3>
	</div>
	<div class="thirteen columns offset-by-three omega">
		<div class="article page">
			<header><h1 contenteditable="true">My New Great Document</h1></header>
			<hr class="large">
			<form id="create_document" class="inplace" action="<?php URL::out('auth_ajax', Utils::WSSE(array('context' => 'create_document'))); ?>">
			<div class="doc-section body editable" id="intro" name="content" contenteditable="true" designmode="on">
				<p>The powered flight took a total of about eight and a half minutes. It seemed to me it had gone by in a lash. We had gone from sitting still on the launch pad at the Kennedy Space Center to traveling at 17,500 miles an hour in that eight and a half minutes. </p>
				<p>It is still mind-boggling to me. I recall making some statement on the air-to-ground radio for the benefit of my fellow astronauts, who had also been in the program a long time, that it was well worth the wait.</p>
			</div>
			</form>
		</div>
	</div>
<?php $theme->display('footer'); ?>