<?php
namespace Habari;
class GooglePrettify extends Plugin {
	public function action_template_header() {
		Stack::add( 'template_header_javascript', $this->get_url('/google-code-prettify/prettify.js'), 'prettify-js' );
		Stack::add( 'template_stylesheet', array( $this->get_url('/google-code-prettify/prettify.css'), 'screen' ), 'prettify-css');
	}
}
?>
