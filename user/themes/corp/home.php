<?php namespace Habari; ?>
<?php $theme->display('header'); ?>
<div id="app_preview">
	<div class="container">
		<div class="offset-by-three twelve column">
			<div class="create_account five columns alpha"><a href="#create" title="create an account">Give it a try</a></div>
			<div class="take_tour five columns omega"><a href="#tour" title="Take a tour">Take a Tour</a></div>
		</div>
	</div>
</div>
<div id="features_overview">
	<div class="container">
		<div class="columns eight alpha">
			<div class="feature columns eight">
				<div class="one columns alpha"><i class="icon-start">p</i></div>
				<div class="six columns omega">
					<strong>Start it together</strong>
					<p>Create your document and immediately invite people to collaborate.</p>
				</div>
			</div>
			<div class="feature eight columns">
				<div class="one columns alpha"><i class="icon-start">a</i></div>
				<div class="six columns omega">
					<strong>I approve!</strong>
					<p>People invited to your document are able to approve each page as it is completed.</p>
				</div>
			</div>
			<div class="feature eight columns">
				<div class="one columns alpha"><i class="icon-start">d</i></div>
					<div class="six columns omega">
						<strong>Take it with you</strong>
						<p>Cwrkspace integrates with some of your favorite services like Dropbox and Github. You can also export a document as HTML, PDF or ePUB.</p>
					</div>
			</div>
			<div class="feature eight columns">
				<div class="one columns alpha"><i class="icon-start">c</i></div>
				<div class="six columns omega">
					<strong>Cheap to boot!</strong>
					<p>For the price of a few cups of coffee you can bring your document creation into the future.</p>
				</div>
			</div>
		</div>
		<div id="previews" class="columns eight omega">
			<div class="column eight cont">
				<div>
					<span><i class="icon-play">v</i></span>
					<img src="<?php Site::out_url('theme'); ?>/images/create_doc_poster.png" class="scale-with-grid">
				</div>	
				<p>Creating a document and inviting collaborators is as easy as pie. Tasty, tasty pie. Simply click the giant "new document" button to get started.</p>
			</div>
		</div>
	</div>
</div>
<?php $theme->display('footer'); ?>