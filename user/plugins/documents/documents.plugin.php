<?php
namespace Habari;

define('TYPE_DOCUMENTATION', 1);
define('TYPE_GENERIC', 2);

class DocumentsPlugin extends Plugin
{
	public function action_init() {
		DB::register_table( 'documents' );
		DB::register_table( 'user_documents' );
	}
	
	public function action_plugin_activation( $plugin_file ) {
		Post::add_new_type( 'document' );
		$this->create_documents_table();
	}

	public function action_plugin_deactivation ( $file='' ) {}

	private function create_documents_table() {
		$sql = "CREATE TABLE {\$prefix}documents (
				id int unsigned NOT NULL AUTO_INCREMENT,
				post_id int unsigned NOT NULL,
				client_id int unsigned NOT NULL,
				type int unsigned NOT NULL,
				approved int unsigned NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `post_id` (`post_id`),
				KEY `client_id` (`client_id`)
				) DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;";

		DB::dbdelta($sql);
	}

	private function create_user_documents_table() {
		$sql = "CREATE TABLE {\$prefix}user_documents (
				id int(10) unsigned NOT NULL AUTO_INCREMENT,
				user_id int(10) unsigned NOT NULL,
				document_id int(10) unsigned NOT NULL,
				PRIMARY KEY (`id`),
				KEY `document_id` (`document_id`),
				KEY `user_id` (`user_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";

		DB::dbdelta($sql);
	}

	public function filter_posts_get_paramarray($paramarray) {
		$queried_types = Posts::extract_param($paramarray, 'content_type');
		if($queried_types && in_array('document', $queried_types)) {
			$paramarray['post_join'][] = '{documents}';
			$default_fields = isset($paramarray['default_fields']) ? $paramarray['default_fields'] : array();
			$default_fields['{documents}.client_id'] = '';
			$default_fields['{documents}.type'] = TYPE_DOCUMENTATION;
			$paramarray['default_fields'] = $default_fields;
		}
		
		return $paramarray;
	}

	public function filter_post_schema_map_document($schema, $post) {
		$schema['documents'] = $schema['*'];
		$schema['documents']['post_id'] = '*id';
		return $schema;		
	}

	public function filter_default_rewrite_rules( $rules ) {
		$this->add_rule('"d"/"new"', 'display_create_doc');
		$this->add_rule('"d"/slug', 'display_document');
		return $rules;
	}

	public function theme_route_display_create_doc($theme) {
		$theme->display( 'document.new' );
	}

	private function connect_doc($user, $doc) {
		$args = array( 'user_id' => $user->id, 'document_id' => $doc );
		DB::insert( DB::table('user_documents'), $args );
	}
	
	public function get_approvers($document) {
		$u_ids = array();
		$ids = DB::get_results( "SELECT user_id FROM {user_documents} WHERE document_id = ?", array($document) );
		foreach( $ids as $id ) {
			$u_ids[] = $id->user_id;
		}
				
		return Users::get( array('id' => $u_ids) );
	}

	public function theme_route_display_document($theme) {
		$theme->document = Document::get( array('slug' => $theme->matched_rule->named_arg_values['slug']) );
		$theme->pages = Pages::get( array('document_id' => $theme->document->id, 'orderby' =>  'id ASC') );
		$theme->approvers = $this->get_approvers( $theme->document->id );

		$theme->display( 'document.single' );
	}

	public function action_auth_ajax_create_document($data) {
		$vars = $data->handler_vars;
		$user = User::identify();
		
		$args = array(
					'title'			=>	$vars['title'],
					'slug'			=>	Utils::slugify( $vars['title'] ),
					'content'		=>	$vars['content'] ? $vars['content'] : '',
					'user_id'		=>	$user->id,
					'pubdate'		=>	DateTime::date_create( date(DATE_RFC822) ),
					'status'		=>	Post::status('published'),
					'content_type'	=>	Post::type('document'),
					'client_id'		=>	$vars['client_id'] ? $vars['client_id'] : '',
					'type'			=>	$vars['type'] ? $vars['type'] : ''
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
	
	public function action_auth_ajax_update_document($data) {
		$vars = $data->handler_vars;
		$document = Document::get( array('id' => $vars['id']) );
		
		$document->title = strip_tags( $vars['title'] );
		$document->content = $vars['content'];
		
		try {		
			$document->update();
			$status = 200;
			$message = $document->title . ' was updated.';
		} catch( Exception $e ) {
			$status = 401;
			$message = 'There was an error updating' . $document->title;
		}

		$ar = new AjaxResponse( $status, $message, null );
		$ar->out();
	}
	
	public function action_auth_ajax_add_approver($data) {
		$vars = $data->handler_vars;
		$document = Document::get( array('id' => $vars['id']) );
		
		try {
			$group = UserGroup::get('quarantine');
			$user = new User(array(
						'username' => $vars['invitee'],
						'email' => $vars['invitee'],
						'password' => Utils::crypt($vars['invitee']),
					));
					
			$user->insert();
			$group->add( $user );

			$this->connect_doc( $user, $vars['id'] );

			$user->info->creation_date = DateTime::date_create()->int;			
			$user->info->commit();
			
			$document->grant( $user, 'read');
			
			$status = 200;
			$message = 'We added ' . $vars['invitee'] . ' to the approvers list.';			
/* 			Mailer::send_message( $user, 'invite' ); */
		} catch( Exception $e ) {
			$status = 401;
			$message = 'We couldn\'t add ' . $vars['invitee'] . ' to the approvers list.';
		}
		
		$ar = new AjaxResponse( $status, $message, null );
		$ar->html( '#participating', '#' );
		$ar->out();
		
	}
}
?>