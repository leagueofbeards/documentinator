<?php namespace Habari; ?>
<?php if ( !defined( 'HABARI_PATH' ) ) { die('No direct access'); } 

class Doco extends Theme
{
	public function action_init_theme() {
		Format::apply( 'autop', 'comment_content_out' );
		Format::apply( 'tag_and_list', 'post_tags_out' );
		Format::apply_with_hook_params( 'more', 'post_content_out', 'more' );
		Format::apply( 'autop', 'post_content_excerpt' );
		Format::apply_with_hook_params( 'more', 'post_content_excerpt', 'more',60, 1 );
	}
		
	public function action_add_template_vars() {
		if( !User::identify()->loggedin ) {
			$anonymous_routes = array('display_invite');
			$anonymous_routes = Plugins::filter('anonymous_routes', $anonymous_routes, $this->matched_rule);
			$matched_rule = $this->matched_rule;
			if($matched_rule instanceof RewriteRule && !in_array($matched_rule->name, $anonymous_routes)) {
				Session::add_to_set( 'login', $_SERVER['REQUEST_URI'], 'original' );
				Utils::redirect( Site::get_url('habari') . '/auth/login' );
				exit;
			}
		} else {
		}
		
		$this->wsse = Utils::WSSE();
	}

	public function act_display_home( $user_filters = array() ) {
		$user = User::identify();
		$this->docs = Documents::get( array('orderby' => 'id ASC', 'nolimit' => true) );
		$this->title = 'Your Documents';
		$this->display('home');
	}
}
?>
