<?php 
namespace Habari;

if( !defined( 'HABARI_PATH' ) ) { 
	die('No direct access');
}

class HPO extends Theme
{
	public function action_init_theme()	{
		// Apply Format::autop() to comment content...
		Format::apply( 'autop', 'post_content_out' );
		Format::apply( 'autop', 'comment_content_out' );
	}

	public function action_add_template_vars() {
		$user = User::identify();
		$this->wsse = Utils::WSSE();
		$this->loggedin = User::identify()->loggedin;
	}
	
	public function section($theme) {
		echo $this->template_engine->matched_rule->named_arg_values['addon'];
	}
	
	public function section_blurb($type) {
		if( $type == null ) {
			$type = $this->template_engine->matched_rule->named_arg_values['addon'];
		}
		
		switch($type) {
			case 'plugins' :
				$out = 'Plugins extend Habari to do anything you can imagine. From Social Networking to Rest APIs it\'s all possible through plugins.';
			break;
			case 'themes' :
				$out = 'Not just a face lift, themes in Habari can add powerful features and modify how the core theme system works. Who\'s just a pretty face?';
			break;
			case 'classes' :
				$out = 'Going further than plugins, custom classes can add entire feature sets to Habari, or introduce a custom version of existing features, like permissions, posting or logging.';
			break;
		}
		
		return $out;
	}
}
?>