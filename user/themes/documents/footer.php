<?php namespace Habari; ?>
<footer>
</footer>
<script src="<?php Site::out_url('theme'); ?>/js/application.js"></script>

<div id="new_approv_form" class="modal hide">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4>Add a new Approver</h4>
	</div>
	<form id="new_approver" action="" method="POST" class="modal-form">
		<div class="modal-body">
			<div id="add_apprv_error" class="box_error" style="display:none;"></div>
			<?php echo Utils::setup_WSSE(); ?>
			<label style="display:none;">Email</label><input type="text" id="company_name" name="company_name" value="" placeholder="name@website.com">
		</div>
		<div class="modal-footer">
			<a href="#" data-dismiss="modal" aria-hidden="true" class="btn cancel">Cancel</a>
			<a href="" id="add_approv" class="btn">Add Approver</a>
		</div>
	</form>
</div>

<div id="new_doc" class="modal hide">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4>Add a new Document</h4>
	</div>
	<form id="new_document" action="<?php URL::out('auth_ajax', array('context' => 'create_document')); ?>" method="POST" class="modal-form">
		<div class="modal-body">
			<div id="add_doc_error" class="box_error" style="display:none;"></div>
			<?php echo Utils::setup_WSSE(); ?>
			<label style="display:none;">Email</label>
			<input type="text" id="name" name="name" value="" placeholder="Document Name">&nbsp;
			<label style="display:none;">Type</label>
			<select id="type" name="type" placeholder="Document Type">
				<option>Document Type</option>
				<option value="1">Documentation</option>
				<option value="2">Generic</option>
			</select>
			<div class="clear"></div>
			<label style="display:none;">Document Description</label>
			<textarea id="description" name="description" placeholder="Document Description" style="width:90%;height:100px;"></textarea>
		</div>
		<div class="modal-footer">
			<a href="#" data-dismiss="modal" aria-hidden="true" class="btn cancel">Cancel</a>
			<a href="#" id="add_document" class="btn">Add Document</a>
		</div>
	</form>
</div>
</body>
</html>