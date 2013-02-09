<?php
namespace Habari;

class PagesPlugin extends Plugin
{
	public function action_init() {
		DB::register_table( 'pages' );
	}
	
	public function action_plugin_activation( $plugin_file ) {
		Post::add_new_type( 'docpage' );
		$this->create_pages_table();
	}

	public function action_plugin_deactivation ( $file='' ) {}

	private function create_pages_table() {
		$sql = "CREATE TABLE {\$prefix}pages (
				id int unsigned NOT NULL AUTO_INCREMENT,
				post_id int unsigned NOT NULL,
				client_id int unsigned NOT NULL,
				document_id int unsigned NOT NULL,
				approved int unsigned NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `post_id` (`post_id`),
				KEY `client_id` (`client_id`),
				KEY `document_id` (`document_id`)
				) DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;";

		DB::dbdelta($sql);
	}

	public function filter_posts_get_paramarray($paramarray) {
		$queried_types = Posts::extract_param($paramarray, 'content_type');
		if($queried_types && in_array('docpage', $queried_types)) {
			$paramarray['post_join'][] = '{pages}';
			$default_fields = isset($paramarray['default_fields']) ? $paramarray['default_fields'] : array();
			$default_fields['{pages}.client_id'] = '';
			$default_fields['{pages}.document_id'] = 0;
			$paramarray['default_fields'] = $default_fields;
		}
		return $paramarray;
	}

	public function filter_post_schema_map_docpage($schema, $post) {
		$schema['pages'] = $schema['*'];
		$schema['pages']['post_id'] = '*id';
		return $schema;		
	}

	public function filter_default_rewrite_rules( $rules ) {
		$rules[] = array(
			'name'			=>	'display_page',
			'parse_regex'	=>	'%^doc/(?P<document>[^/]*)?/?(?P<slug>[^/]*)?/?$%i',
			'build_str'		=>	'doc/{$document}/{$slug}',
			'handler'		=>	'PluginHandler',
			'action'		=>	'display_page',
			'priority'		=>	100,
			'description' => 'Display time tracking for the entire shebang.',
		);


		return $rules;
	}

	public function theme_route_display_page($theme) {
		$theme->document = Document::get( array('slug' => $theme->matched_rule->named_arg_values['document']) );
		$theme->page = Page::get( array('id' => $theme->matched_rule->named_arg_values['slug']) );
		$theme->pages = Pages::get( array('document_id' => $theme->document->id) );
		
		$theme->display( 'page.single' );
	}

	public function action_auth_ajax_create_page($data) {
		$vars = $data->handler_vars;
		$user = User::identify();
		
		$args = array(
					'title'			=>	$vars['name'],
					'slug'			=>	Utils::slugify( $vars['name'] ),
					'content'		=>	$vars['description'] ? $vars['description'] : '',
					'user_id'		=>	$user->id,
					'pubdate'		=>	DateTime::date_create( date(DATE_RFC822) ),
					'status'		=>	Post::status('published'),
					'content_type'	=>	Post::type('document'),
					'client_id'		=>	$vars['client_id'] ? $vars['client_id'] : '',
					'document_id'	=>	$vars['document_id']
				);
		
		try {
			$doc = Document::create( $args );
			$doc->grant( $user, 'full' );
			$status = 200;
			$message = 'Your Document has been created';
		} catch( Exception $e ) {
			$status = 401;
			$message = 'We couldn\'t create your document, please try again.' ;
		}
				
		$ar = new AjaxResponse( $status, $message, null );
		$ar->html( '.content', '#' );
		$ar->out();
	}
}
?>