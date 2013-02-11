<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
<div class="container">
	<?php $theme->display('sidebar'); ?>
	<div class="thirteen columns offset-by-three content">
		<div id="editor">
			<div id="save">
				<a class="save" href="#"><i class="icon-save">s</i></a>
			</div>
			<a id="heading" href="#"><strong>H</strong></a>
			<div id="submenu">
				<ul>
					<li><a href="#" class="heading" data-level="H1">h1</a></li>
					<li><a href="#" class="heading" data-level="H2">h2</a></li>
					<li><a href="#" class="heading" data-level="H3">h3</a></li>
					<li><a href="#" class="heading" data-level="H4">h4</a></li>
					<li><a href="#" class="heading" data-level="H5">h5</a></li>
				</ul>
			</div>			
			<a id="bold" href="#"><strong>b</strong></a>
			<a id="italic" href="#"><i>i</i></a>
			<a id="underline" href="#"><u>u</u></a>
			<a id="code" href="#"><i class="icon-code">P</i></a>
		</div>
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
</div>
<?php $theme->display('footer'); ?>