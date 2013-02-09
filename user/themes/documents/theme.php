<?php namespace Habari; ?>
<?php if ( !defined( 'HABARI_PATH' ) ) { die('No direct access'); } 

class Doco extends Theme
{
	public function action_init_theme() {
		$user = User::identify();
		Format::apply( 'autop', 'comment_content_out' );
		Format::apply( 'tag_and_list', 'post_tags_out' );
		Format::apply_with_hook_params( 'more', 'post_content_out', 'more' );
		Format::apply( 'autop', 'post_content_excerpt' );
		Format::apply_with_hook_params( 'more', 'post_content_excerpt', 'more',60, 1 );
		$this->assign( 'user', $user );
		$this->assign( 'pages', Posts::get(array('nolimit' => true, 'orderby' => 'id ASC')) );
		$this->assign( 'first', Post::get( array('id' => 2) ) );
	}
		
	public function action_add_template_vars() {
		if( !User::identify()->loggedin ) {
			$anonymous_routes = array();
			$anonymous_routes = Plugins::filter('anonymous_routes', $anonymous_routes, $this->matched_rule);
			$matched_rule = $this->matched_rule;
			if($matched_rule instanceof RewriteRule && !in_array($matched_rule->name, $anonymous_routes)) {
				Session::add_to_set( 'login', $_SERVER['REQUEST_URI'], 'original' );
				Utils::redirect( Site::get_url('habari') . '/auth/login' );
				exit;
			}
		} else {
			$user = User::identify();
		}
		
		$this->wsse = Utils::WSSE();
		$this->user = User::identify();
	}

	public function act_display_home( $user_filters = array() ) {
		$this->docs = Documents::get( array('user_id' => $this->user) );
		$this->display('home');
	}
}
?>
