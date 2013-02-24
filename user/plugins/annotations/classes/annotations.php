<?php
namespace Habari;
class Annotations extends Posts
{
	public static function get($paramarray = array()) {
		$defaults = array(
			'content_type' => 'annotation',
			'fetch_class' => 'Annotation',
		);
		
		$paramarray = array_merge($defaults, Utils::get_params($paramarray));
		return Posts::get( $paramarray );
	}
}

?>