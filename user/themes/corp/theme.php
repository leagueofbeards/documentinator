<?php namespace Habari; ?>
<?php if ( !defined( 'HABARI_PATH' ) ) { die('No direct access'); } 

class Cwspce extends Theme
{
	public function action_init_theme() {
		Format::apply( 'autop', 'comment_content_out' );
		Format::apply( 'tag_and_list', 'post_tags_out' );
		Format::apply_with_hook_params( 'more', 'post_content_out', 'more' );
		Format::apply( 'autop', 'post_content_excerpt' );
		Format::apply_with_hook_params( 'more', 'post_content_excerpt', 'more',60, 1 );
	}
}
?>
