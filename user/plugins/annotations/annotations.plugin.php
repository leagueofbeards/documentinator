<?php
namespace Habari;
class AnnotatePlugin extends Plugin
{
	public function action_init() {
		DB::register_table( 'annotations' );
	}
	
	public function action_plugin_activation( $plugin_file ) {
		Post::add_new_type( 'annotation' );
		$this->create_annotations_table();
	}

	public function filter_autoload_dirs($dirs) {
		$dirs[] = __DIR__ . '/classes';
		return $dirs;
	}

	public function action_plugin_deactivation ( $file='' ) {}

	private function create_annotations_table() {
		$sql = "CREATE TABLE {\$prefix}annotations (
				id int unsigned NOT NULL AUTO_INCREMENT,
				post_id int unsigned NOT NULL,
				connection_id int unsigned NOT NULL,
				text TEXT NULL,
				url varchar(255),
				ranges varchar(255),
				permissions varchar(255),
				created varchar(258) NOT NULL,
				updated varchar(256) NOT NULL,
				PRIMARY KEY (`id`),
				KEY `user_id` (`user_id`),
				KEY `post_id` (`post_id`)
				) DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;";

		DB::dbdelta($sql);
	}

	public function filter_posts_get_paramarray($paramarray) {
		$queried_types = Posts::extract_param($paramarray, 'content_type');
		if($queried_types && in_array('annotation', $queried_types)) {
			$paramarray['post_join'][] = '{annotations}';
			$default_fields = isset($paramarray['default_fields']) ? $paramarray['default_fields'] : array();
			$default_fields['{annotations}.text'] = '';
			$default_fields['{annotations}.connection_id'] = '';
			$default_fields['{annotations}.url'] = '';
			$default_fields['{annotations}.ranges'] = '';
			$default_fields['{annotations}.permissions'] = '';
			$default_fields['{annotations}.created'] = '';
			$default_fields['{annotations}.updated'] = '';

			$paramarray['default_fields'] = $default_fields;
		}
		
		return $paramarray;
	}

	public function filter_post_schema_map_annotation($schema, $post) {
		$schema['annotations'] = $schema['*'];
		$schema['annotations']['post_id'] = '*id';
		return $schema;		
	}

	public function filter_post_get($out, $name, $annotation) {
		if('annotation' == Post::type_name($annotation->get_raw_field('content_type'))) {
			switch($name) {}
		}
		
		return $out;
	}

	public function filter_default_rewrite_rules( $rules ) {
		$this->add_rule('"a"/slug', 'display_annotation');
		return $rules;
	}

	private function get_selections($vars) {
		$return = array();
		$rows = array();
		$cnt = DB::get_value( "SELECT count(id) as count FROM {annotations} WHERE post_id = ?", array($vars) );
		$selections = DB::get_results( "SELECT id, user_id, post_id, text, content, url FROM {annotations} WHERE post_id = ?", array($vars) );
		
		$return['total'] = $cnt;
		
		foreach( $selections as $selection ) {
			$rows[] = $selection->text;
		}
		
		$return['rows'] = $rows;
		
		return json_encode( $return );
	}

	private function get_annotation($vars) {
		$return = array();
		$rows = array();
		$selection = DB::get_row( "SELECT id, user_id, post_id, text, content, url FROM {annotations} WHERE id = ?", array($vars) );
		return $selection->to_json();
	}

	private function get_all($vars) {
		$vars = 43;
		$return = array();
		$rows = array();
		$cnt = DB::get_value( "SELECT count(id) as count FROM {annotations} WHERE post_id = ?", array($vars) );
		$selections = DB::get_results( "SELECT id, user_id, post_id, text, content, url FROM {annotations} WHERE post_id = ?", array($vars) );
		
		$return['total'] = $cnt;
		
		foreach( $selections as $selection ) {
			$rows[] = $selection->text;
		}
		
		$return['rows'] = $rows;
		
		return json_encode( $return );
	}

	public function theme_route_display_annotation($theme) {
		$theme->annotation = Annotation::get( array('slug' => $theme->matched_rule->named_arg_values['slug'], 'ignore_permissions' => true) );
		$theme->person = User::get( $theme->annotation->user_id );
		$theme->document = Post::get( array('id' => $theme->annotation->connection_id) );
		$theme->title = 'View Annotation';
		
		$theme->display( 'annotation.single' );
	}

	public function jsonToReadable($json) {
		$tc = 0;
		$r = '';
		$q = false;
		$t = "\t";
		$nl = "\n";
	
		for($i=0;$i<strlen($json);$i++) {
			$c = $json[$i];
			if($c=='"' && $json[$i-1]!='\\') $q = !$q;
			
			if($q) {
				$r .= $c;
				continue;
			}
			
			switch($c) {
				case '{':
				case '[':
					$r .= $c . $nl . str_repeat($t, ++$tc);
				break;
				case '}':
				case ']':
					$r .= $nl . str_repeat($t, --$tc) . $c;
				break;
				case ',':
					$r .= $c;
					if($json[$i+1]!='{' && $json[$i+1]!='[') $r .= $nl . str_repeat($t, $tc);
				break;
				case ':':
					$r .= $c . ' ';
				break;
				default:
					$r .= $c;
			}
		}
		
		return $r;
	}

	/* REST APIs*/	
	public function rest_get__v1_annotations__postid($params) {
		$annotations = $this->get_selections( $params['postid'] );
		echo $annotations;
	}

	public function rest_get__v1_annotation__annoid($params) {
		$annotation = $this->get_annotation( $params['annoid'] );
		echo $annotation;
	}

	public function rest_get_v1_read_annotations__postid($params) {
		$bits = explode('/', $_SERVER['HTTP_REFERER']);
		$return = array();
		$rows = array();

		$obj = Post::get( array('id' => $params['postid']) );

		switch( $obj->content_type ) {
			case Post::type( 'document' ) :
				$post = Document::get( array('id' => $params['postid']) );
			break;
			case Post::type( 'page' ) :
				$post = Page::get( array('id' => $params['postid']) );
			break;
		}
		
		if( $post ) {				
			$count = Annotations::get( array('connection_id' => $post->id, 'count' => true, 'ignore_permissions' => true) );
			$annotations = Annotations::get( array('connection_id' => $post->id, 'ignore_permissions' => true) );
	
			foreach( $annotations as $annotation ) {
				$person = User::get_by_id( $annotation->user_id );
				$ranges = json_decode( $annotation->ranges );
				$row = array();
				$row['uri'] = $annotation->url;
				$row['updated'] = $annotation->updated;			
				$row['id'] = $annotation->id;
				$row['links'][] = array( 'rel' => 'alternate', 'href' => URL::get('display_annotation', array('slug' => $annotation->slug)), 'type' => 'text/html' );
				$row['quote'] = $annotation->content;			
				$row['ranges'] = unserialize($annotation->ranges);
				$row['created'] = $annotation->created;
				$row['consumer'] = 'coworkspace';
				$row['text'] = $annotation->text;
				$row['user'] = $person->displayname;
				$row['avatar'] = Gravatar::get( $person->email );
				
				$rows[] = $row;
			}
		} else {
			$count = 0;
			$rows = array();
		}
		
		$return['total'] = intval($count);
		$return['rows'] = $rows;
		
		if( $count > 0 ) {
			header('Content-Type: application/json; charset=utf-8');
			$parsed = str_replace( '\/','/', json_encode( $return ) );
			echo $this->jsonToReadable( $parsed );
		} else {
			header('Content-Type: application/json');
			echo json_encode( array('No Annotations found.', 'status=404') );
		}
		
		exit();
	}

	public function rest_post_v1_create_annotation($params) {
		$user = User::identify();
		$payload = file_get_contents( 'php://input' );
		$payload = json_decode($payload);
		$id = rand() . time();
		$bits = explode('/', $_SERVER['HTTP_REFERER']);
				
		switch( count($bits) ) {
			case 6 :
				$doc = array_pop($bits);
				$prefix = array_pop($bits);
				$post = Document::get( array('slug' => $doc) );
			break;
			case 7 :
				$pge = array_pop($bits);
				$doc = array_pop($bits);
				$document = Document::get( array('slug' => $doc) );
				$prefix = array_pop($bits);
				$post = Page::get( array('slug' => $pge, 'document_id' => $document->id) );
			break;
		}
		
		$args = array( 
			'title' => $id,
			'slug' => Utils::slugify( $id ),
			'connection_id' => $post->id,
			'user_id' => $user->id,
			'text' => $payload->text,
			'content' => $payload->quote,
			'url' => $post->permalink,
			'ranges' => serialize($payload->ranges),
			'permissions' => serialize($payload->permissions),
			'created' => date(c),
			'updated' => date(c),
			'content_type' => Post::type('annotation'),
			'status' => Post::status('published'),
		);
		
		if( ($post->id != '') ) {
			$annotation = Annotation::create( $args );
			echo json_encode(array('id' => $annotation->id));
		} else {
			echo json_encode( array('No JSON payload sent. Annotation not created.', 'status=400') );
		}
	}
	
	public function rest_post_v1_destroy_annotation__annoid($params) {}
}
?>