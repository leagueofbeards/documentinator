<?php
namespace Habari;
class Annotations extends Plugin
{
	public function action_init() {
		DB::register_table( 'annotations' );
	}
	
	public function action_plugin_activation( $plugin_file ) {
		$this->create_annotations_table();
	}

	public function action_plugin_deactivation ( $file='' ) {}

	private function create_annotations_table() {
		$sql = "CREATE TABLE {\$prefix}annotations (
				id int unsigned NOT NULL AUTO_INCREMENT,
				user_id int unsigned NOT NULL,
				post_id int unsigned NOT NULL,
				text text,
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

	private function save_selection($args) {
		DB::insert( DB::table('annotations'), $args );
		return DB::last_insert_id();
	}
	
	/* REST APIs*/

	public function rest_get__v1() {
		$return = array(
				'name' => 'Annotator Store API',
				'version' => '2.0.0'
				);
		echo json_encode( $return );
	}
	
	public function rest_get__v1_annotations__postid($params) {
		$annotations = $this->get_selections( $params['postid'] );
		echo $annotations;
	}

	public function rest_get__v1_annotation__annoid($params) {
		$annotation = $this->get_annotation( $params['annoid'] );
		echo $annotation;
	}

	public function rest_get_v1_read_annotations($params) {
		$bits = explode('/', $_SERVER['HTTP_REFERER']);
		$return = array();
		$rows = array();
		
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
		
		$count = DB::get_value( "SELECT count(id) FROM {annotations} WHERE post_id = ?", array($post->id) );
		$annotations = DB::get_results( "SELECT id, url, permissions, ranges, user_id, updated, created, content FROM {annotations} WHERE post_id = ?", array($post->id) );
		
		foreach( $annotations as $annotation ) {
			$row = array();
			$row['uri'] = $annotation->url;
			$row['permissions'] = json_decode($annotation->permissions);
			$row['id'] = $annotation->id;
			$row['ranges'] = json_decode($annotation->ranges);
			$row['user'] = User::get_by_id( $annotation->user_id )->displayname;
			$row['updated'] = $annotation->updated;
			$row['created'] = $annotation->created;
			$row['consumer'] = 'coworkspace';
			$row['quote'] = $annotation->content;
			
			$rows[] = $row;
		}
		
		$return['total'] = intval($count);
		$return['rows'] = $rows;
		
		if( $count > 0 ) {
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode( $return );
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
						'post_id' => $post->id,
						'user_id' => $user->id,
						'text' => $payload->text,
						'content' => $payload->quote,
						'url' => $post->permalink,
						'ranges' => json_encode($payload->ranges),
						'permissions' => json_encode($payload->permissions),
						'created' => date(DATE_RFC822),
						'updated' => date(DATE_RFC822)
					);
		
		if( ($post->id != '') ) {
			$annotation = $this->save_selection( $args );
			echo json_encode(array('id' => $annotation));
		} else {
			echo json_encode( array('No JSON payload sent. Annotation not created.', 'status=400') );
		}
	}
}
?>