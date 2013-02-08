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

	public function act_display_home( $user_filters = array() ) {		
		parent::act_display_home();
	}
	
	public function add_template_vars() {}
}
?>
