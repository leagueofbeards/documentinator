<?php
namespace Habari;

define('TYPE_DOCUMENTATION', 1);
define('TYPE_GENERIC', 2);

class DocumentsPlugin extends Plugin
{
	const APPROVAL_TYPE_APPROVAL = 1;
	const APPROVAL_STATUS_APPROVED = 1;
	const APPROVAL_STATUS_REJECTED = 2;

	public function filter_autoload_dirs($dirs) {
		$dirs[] = __DIR__ . '/classes';
		return $dirs;
	}

	public function action_init() {
		DB::register_table( 'documents' );
		DB::register_table( 'user_documents' );
		DB::register_table( 'approvals' );
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

	public function filter_post_get($out, $name, $document) {
		if('document' == Post::type_name($document->get_raw_field('content_type'))) {
			switch($name) {
				case 'approval_summary' :
					$out = $this->get_approval_summary(self::APPROVAL_TYPE_APPROVAL, $document);
				break;
				case 'is_approved' :
					$out = $this->check_approved(self::APPROVAL_TYPE_APPROVAL, $document);
				break;
				case 'approval_status' :
					$out = $this->get_approvals(self::APPROVAL_TYPE_APPROVAL, $document);
				break;
			}
		}
		
		return $out;
	}

	public function filter_default_rewrite_rules( $rules ) {
		$this->add_rule('"d"/"new"', 'display_create_doc');
		$this->add_rule('"d"/slug', 'display_document');
		return $rules;
	}

	private function check_approved($approval_type, $document) {
		$ids = array();
		$p_count = Pages::get( array('document_id' => $document->id, 'count' => true) );
		$a_count = Pages::get( array('document_id' => $document->id, 'approved' => 1, 'count' => true) );
		$pages = Pages::get( array('document_id' => $document->id) );
		
		foreach( $pages as $page ) {
			$ids[] = $page->id;
		}
		
		$approvals = DB::get_value( "SELECT count(id) FROM {approvals} WHERE post_id IN(?) AND approval_type = ? AND approval_status = ?", array(implode(',', $ids), $approval_type, self::APPROVAL_STATUS_APPROVED) );
		
		if( $a_count != $p_count ) {
			return false;
		} else {
			return true;
		}
	}

	private function get_approvals($approval_type, $document) {
		if($document->is_approved == true ) {
			$out = '<span class="status_icon approved"><i class="icon-approved">c</i></span>';
		} else {
			$out = false;
		}
		
		return $out;
	}

	private function get_approval_summary($approval_type, $document) {
		$approved = DB::get_value(
			'SELECT count(*) FROM {approvals} WHERE post_id = :post_id AND approval_type = :approval_type AND approval_status = :approval_status',
			array(
				'post_id' => $document->id,
				'approval_type' => $approval_type,
				'approval_status' => self::APPROVAL_STATUS_APPROVED,
			)
		);
		
		$rejected = DB::get_value(
			'SELECT count(*) FROM {approvals} WHERE post_id = :post_id AND approval_type = :approval_type AND approval_status = :approval_status',
			array(
				'post_id' => $document->id,
				'approval_type' => $approval_type,
				'approval_status' => self::APPROVAL_STATUS_REJECTED,
			)
		);
		
		$total = DB::get_value(
			'SELECT count(*) FROM {approvals} WHERE post_id = :post_id AND approval_type = :approval_type',
			array(
				'post_id' => $document->id,
				'approval_type' => $approval_type,
			)
		);
		
		$out = array();
		
		$out[]= '<span class="approvals"><i class="icon-approved">c</i>' . $approved . '</span>';
		$out[]= '<span class="denials"><i class="icon-denied">x</i>' . $rejected . '</span>';
		
		if(count($out) == 0) {
			$out[]= '0';
		}
		
		$out = implode('', $out);
		
		if($total == 0) {
			$out = '&nbsp;';
		} elseif( $total == $approved ) {
			$out = '&nbsp;';
		}
				
		return $out;
	}

	private function update_approval($record) {
		$approvers = $this->count_approvers( $record );
		$approved = DB::get_value('SELECT count(*) FROM {approvals} WHERE post_id = ? AND approval_type = ? AND approval_status = ?', array($record, self::APPROVAL_TYPE_APPROVAL, self::APPROVAL_STATUS_APPROVED) );	
				
		if( $approved == $approvers ) {
			$page = Page::get( array('id' => $record) );
			$page->approved = 1;
			$page->update();
		}
	}

	public function count_approvers($document) {
		$u_ids = array();
		$doc = Document::get( array('id' => Page::get( array('id' => $document))->document_id) );
		$ids = DB::get_results( "SELECT user_id FROM {user_documents} WHERE document_id = ?", array($doc->id) );
		$u_ids[] = $doc->user_id;
		
		foreach( $ids as $id ) {
			$u_ids[] = $id->user_id;
		}
				
		return count( $u_ids );
	}

	public static function approved($document, $user) {
		$ret;
		$status = DB::get_row(
			'SELECT approval_type, approval_status FROM {approvals} WHERE post_id = :post_id AND user_id = :user_id',
			array(
				'post_id' => $document->id,
				'user_id' => $user->id,
			)
		);
						
		switch($status->approval_status) {
			case self::APPROVAL_STATUS_APPROVED:
				$ret = true;
			break;
			case self::APPROVAL_STATUS_REJECTED:
				$ret = false;
			break;
			default:
				$ret = 0;
			break;
		}
						
		return $ret;
	}

	public function theme_route_display_create_doc($theme) {
		$theme->title = 'Create a new Document';
		
		$theme->display( 'document.new' );
	}

	public static function connect_doc($user, $doc) {
		$args = array( 'user_id' => $user->id, 'document_id' => $doc );
		DB::insert( DB::table('user_documents'), $args );
	}
		
	public function get_approvers($document) {
		$u_ids = array();
		$ids = DB::get_results( "SELECT user_id FROM {user_documents} WHERE document_id = ?", array($document) );
		$doc = Document::get( array('id' => $document) );
				
		foreach( $ids as $id ) {
			$u_ids[] = $id->user_id;
		}
				
		return Users::get( array('id' => $u_ids) );
	}

	public function theme_route_display_document($theme) {
		$theme->document = Document::get( array('slug' => $theme->matched_rule->named_arg_values['slug']) );
		$theme->pages = Pages::get( array('document_id' => $theme->document->id, 'orderby' =>  'id ASC') );
		$theme->approvers = $this->get_approvers( $theme->document->id );
		$theme->title = $theme->document->title;
		$theme->post_id = $theme->document->id;
		
		$theme->display( 'document.single' );
	}

	public function action_auth_ajax_create_document($data) {
		$vars = $data->handler_vars;
		$user = User::identify();
		
		$args = array(
					'title'			=>	strip_tags($vars['title']),
					'slug'			=>	Utils::slugify( strip_tags($vars['title']) ),
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
		$ar->callback = 'window.location = "' . URL::get( 'display_document', array('slug' => $doc->slug) ) . '"';
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
			
	public function action_auth_ajax_approval($data) {
		$user = User::identify();
		$document_id = $data->handler_vars['id'];
		$action = $data->handler_vars['action'];
		$doc = Post::get( array('id' => $document_id) );
		
		if( $doc->content_type == Post::type('document') ) {
			$doc_id = $doc->id;
		} elseif( $doc->content_type == Post::type('docpage') ) {
			$doc_id = Page::get( array('id' => $document_id) )->document_id;
		}
		
		switch($action) {
			case 'approve':
				$approval_status = self::APPROVAL_STATUS_APPROVED;
				$averb = 'approved';
				break;
			case 'reject':
				$approval_status = self::APPROVAL_STATUS_REJECTED;
				$averb = 'rejected';
				break;
			default:
				$approval_status = self::APPROVAL_STATUS_REJECTED;
				$averb = 'rejected';
				break;
		}

		DB::update(
			'{approvals}',
			array(
				'approval_date' => DateTime::date_create()->sql,
				'approval_status' => $approval_status,
			),
			array(
				'post_id' => $document_id,
				'user_id' => $user->id,
				'approval_type' => self::APPROVAL_TYPE_APPROVAL,
			)
		);

		if( $doc->content_type == Post::type('docpage') ) {
			$this->update_approval( $doc->id );
		}

		$ar = new AjaxResponse(200, _t('You %s ' . $doc->title, array($averb)));
		$ar->html('#participating', '#');
		$ar->out();

	}
	
	public function action_auth_ajax_get_documents($data) {
		$vars = $data->handler_vars;
		$documents = Documents::get( array('nolimit' => true, 'orderby' => 'id ASC', 'not:slug' => $vars['current']) );
		$str = '<ul id="choices">';	
		
		foreach( $documents as $document ) {
			$str .= '<li><a href="' . URL::get('display_document', array('slug' => $document->slug)) . '">' . $document->title . '</a></li>';
		}
		
		$str .= '</ul>';
		
		$ar = new AjaxResponse( 200, null, $str );
		$ar->out();
	}
	
	public function action_auth_ajax_set_permissions($data) {
		$vars = $data->handler_vars;
		$document = Document::get( array('id' => $vars['document']) );
		$person = User::get( $vars['id'] );
				
		switch( $vars['perm'] ) {
			case '1' :
				$document->revoke( $person );
				$document->grant( $person, 'read' );
				$message = $person->displayname . ' has been granted review rights.';
			break;
			case '2' :
				$document->revoke( $person );
				$document->grant( $person, array('read','edit') );
				$message = $person->displayname . ' has been granted edit rights.';
			break;			
		}
		
		$ar = new AjaxResponse( 200, $message, null );
		$ar->html('#participating', '#');
		$ar->out();
	}
	
	public function action_auth_ajax_export_document($data) {
		$vars = $data->handler_vars;
		$document = Document::get( array('id' => $vars['document_id']) );
		$pages = Pages::get( array('document_id' => $document->id) );
		
		$objects = array( 'document' => array('content' => $document, 'fields' => array('title', 'slug', 'content')), 'page' => array('content' => $pages, 'fields' => array('title', 'slug', 'content')) );
		$assets = array( 'style.css', 'prettify.css' );
		
		$args = array(
					'connected'			=>	array('parent' => $document, 'items' => 'page'),
					'export_name'		=>	$document->slug,
					'template_types'	=>	array('document', 'page'),
					'template_location'	=>	__DIR__ . '/export_templates',
					'objects'			=>	$objects,
					'export_location'	=>	'exports',
					'assets'			=>	$assets,
				);
		
		Exporter::parse( $args );
	}
}
?>