<?php
namespace Habari;

class Annotation extends Post
{
	public static function create($paramarray = array()) {
		$post = new Annotation( $paramarray );
		$post->insert();
		return $post;
	}
	
	public static function get($paramarray = array()) {
		$defaults = array(
			'content_type' => 'annotation',
			'fetch_fn' => 'get_row',
			'limit' => 1,
			'fetch_class' => 'Annotation',
		);
		
		$paramarray = array_merge($defaults, Utils::get_params($paramarray));
		return Posts::get( $paramarray );
	}

	public function jsonSerialize() {
		$array = array_merge( $this->fields, $this->newfields );		
		return json_encode($array);
	}
}?>
