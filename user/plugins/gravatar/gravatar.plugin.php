<?php
namespace Habari;
class gravatar extends Plugin {	
	public function action_plugins_loaded() {}	

	public static function image( $email ) {
		$image = 'avatar.jpg';
		$r = 'https://www.gravatar.com/avatar.php?gravatar_id=';
		$r .= md5($email);
		$r .= '&amp;size=60&amp;default=' . Site::get_url( 'theme' ) . '/images/' . $image;
		return $r;
	}

	public static function get( $email, $size = 60 ) {
		$image = 'avatar.jpg';
		$r = '<img src="https://www.gravatar.com/avatar.php?gravatar_id=';
		$r .= md5($email);
		$r .= '&amp;size=' . $size . '&amp;default=' . Site::get_url( 'theme' ) . '/images/' . $image;
		$r .= '" alt="personal avatar" class="gravatar">';
		return $r;
	}
	
	public static function show( $email, $size = 60 ) {
		echo self::get( $email, $size );
	}
}
?>