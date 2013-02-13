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
				name varchar(255) NULL,
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
			$default_fields['{pages}.approved'] = 0;
			$default_fields['{pages}.name'] = '';
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
		$this->add_rule('"p"/slug/"new"', 'display_create');
		$this->add_rule('"p"/slug/page', 'display_docpage');
		return $rules;
	}

	public function theme_route_display_create($theme) {
		$theme->document = Document::get( array('slug' => $theme->matched_rule->named_arg_values['slug']) );
		$theme->pages = Pages::get( array('document_id' => $theme->document->id, 'orderby' =>  'id ASC') );
		$theme->title = 'Create a new page in ' . $theme->document->title;
		
		$theme->display( 'page.new' );
	}

	public function theme_route_display_docpage($theme) {
		$doc = new DocumentsPlugin();
		$theme->document = Document::get( array('slug' => $theme->matched_rule->named_arg_values['slug']) );
		$theme->page = Page::get( array('document_id' => $theme->document->id, 'name' => $theme->matched_rule->named_arg_values['page']) );
		$theme->pages = Pages::get( array('document_id' => $theme->document->id, 'orderby' =>  'id ASC') );
		$theme->approvers = $doc->get_approvers( $theme->document->id );
		$theme->title = $theme->document->title . ' &raquo; ' . $theme->page->title;
		$theme->post_id = $theme->page->id;
		
		$theme->display( 'page.single' );
	}

	public function action_auth_ajax_create_page($data) {
		$vars = $data->handler_vars;
		$user = User::identify();
		
		$args = array(
					'title'			=>	strip_tags($vars['title']),
					'slug'			=>	Utils::slugify( strip_tags($vars['title']) ),
					'content'		=>	$vars['content'] ? $vars['content'] : '',
					'user_id'		=>	$user->id,
					'pubdate'		=>	DateTime::date_create( date(DATE_RFC822) ),
					'status'		=>	Post::status('published'),
					'content_type'	=>	Post::type('docpage'),
					'client_id'		=>	$vars['client_id'] ? $vars['client_id'] : '',
					'document_id'	=>	$vars['doc'],
					'name'			=>	Utils::slugify( strip_tags($vars['title']) )
				);
		
		try {
			$page = Page::create( $args );
			$page->grant( $user, 'full' );
			$status = 200;
			$message = 'Your page has been created';
		} catch( Exception $e ) {
			$status = 401;
			$message = 'We couldn\'t create your page, please try again.' ;
		}
				
		$ar = new AjaxResponse( $status, $message, null );
		$ar->html('#pages', '#');
		$ar->out();
	}
	
	public function action_auth_ajax_update_page($data) {
		$vars = $data->handler_vars;

		$page = Page::get( array('id' => $vars['id']) );
		
		$page->title = strip_tags( $vars['title'] );
		$page->content = $vars['content'];
		
		try {		
			$page->update();
			$status = 200;
			$message = $page->title . ' was updated.';
		} catch( Exception $e ) {
			$status = 401;
			$message = 'There was an error updating' . $page->title;
		}

		$ar = new AjaxResponse( $status, $message, null );
		$ar->html('#pages', '#');
		$ar->out();
	}
}
?>