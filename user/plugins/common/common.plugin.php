<?php 
namespace Habari;
class Common extends Plugin
{

	public function action_plugin_activation( $plugin_file ) {
		if( Plugins::id_from_file(__FILE__) == Plugins::id_from_file($plugin_file) ) {
			Post::add_new_type( 'work' );
		}
	}
	
	public function action_plugin_deactivation ( $file='' ) {
		if ( Plugins::id_from_file( $file ) == Plugins::id_from_file( __FILE__ ) ) {
			Post::deactivate_post_type( 'work' );
		}
	}
	
	public function filter_post_type_display($type, $g_number)	{
		switch($type) {
			case 'work':
				switch($g_number) {
					case 'singular':
						return _t('Work');
					case 'plural':
						return _t('Work');
				}
				break;
		}
		return $type;
	}

	/**
	 * action_before_act_admin function.
	 * Only allow admins to access the admin area.
	 * @access public
	 * @param mixed $that
	 * @return void
	 */
	public function action_before_act_admin( $that ) {
		$user = User::identify();
		if( !$user->can('superuser') ) {
			Utils::redirect( Site::get_url('habari') );
		}
	}
		
	/**
	 * Change the destination after loggin in, if previously specified
	 * @param string $login_dest The previously set login destination
	 * @param User $user The user account that logged in
	 * @param array $login_session An array of session data related to where the user was trying to go before they logged in
	 */
	public function filter_login_redirect_dest($login_dest, $user, $login_session ) {
		if(isset($login_session['original']) && is_string($login_session['original'])) {
			return ($login_session['original']);
		}
		return Site::get_url('habari');
	}

	public function filter_session_lifetime() {
		// Increase garbage collection to a whole hour!
		return '3600';
	}

	public function filter_sessions_clean($sql, $type, $args) {
		switch($type) {
			case 'gc':
				// Don't ever collect garbage.  Only delete sessions that are deleted explicitly.
				$sql = 'SELECT 1';
				break;
		}
		return $sql;
	}

	public function filter_session_read($dodelete, $session, $session_id) {
		// Don't ever delete on read.  This prevents old but valid sessions from being deleted upon login.
		return false;
	}

	private static function make_safe( $file ) {
		// check that this is an image, and not a file.
		$safe_file = $file['files']['name'];
		$safe_file = str_replace( "#", "No.", $safe_file );
		$safe_file = str_replace( "$", "Dollar", $safe_file );
		$safe_file = str_replace( "%", "Percent", $safe_file );
		$safe_file = str_replace( "^", "", $safe_file );
		$safe_file = str_replace( "&", "and", $safe_file );
		$safe_file = str_replace( "*", "", $safe_file );
		$safe_file = str_replace( "?", "", $safe_file );
		return $safe_file;
	}
	
	public static function create_dir($path) {
		if ( !is_dir( $path ) ) {
			mkdir( $path, 0777 );
		}
	}
	
	public static function upload_image($file, $upload_dir) {
		$return = new stdClass();
		if( $file != '' ) {
			$cleaned = self::make_safe( $file );
			self::create_dir( $upload_dir );
			$path = $upload_dir . $cleaned[0];
			if( copy($file['files']['tmp_name'][0], $path) ) {
				$file_name = $file['files']['name'][0];
				$file_size = $file['files']['size'][0];
				if( $file_size > 999999 ) {
					$div = $file_size / 1000000;
					$file_size = round( $div, 1 ) . ' MB';
				} else {
					$div = $file_size / 1000;
					$file_size = round( $div, 1 ) . ' KB';
				}
				
				$return->document = $path;
			}
		}
		return $return;
	}
	
	public static function email_is_valid($email) {
		$is_valid = true;
		$at_index = strrpos( $email, "@" );
		
		if (is_bool($at_index) && !$at_index) {
			$is_valid	= false;
		} else {
			$domain		= substr($email, $at_index+1);
			$local		= substr($email, 0, $at_index);
			$localLen	= strlen($local);
			$domainLen	= strlen($domain);
			if( $localLen < 1 || $localLen > 64 ) {
				// local part length exceeded
         		$is_valid = false;
      		} elseif( $domainLen < 1 || $domainLen > 255 ) {
				// domain part length exceeded
				$is_valid = false;
			} elseif( $local[0] == '.' || $local[$localLen-1] == '.' ) {
				// local part starts or ends with '.'
				$is_valid = false;
			} elseif( preg_match('/\\.\\./', $local) ) {
				// local part has two consecutive dots
				$is_valid = false;
			} elseif( !preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain) ) {
				// character not valid in domain part
				$is_valid = false;
			} elseif( preg_match('/\\.\\./', $domain) ) {
				// domain part has two consecutive dots
				$is_valid = false;
			} elseif( !preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local)) ) {
				// character not valid in local part unless 
				// local part is quoted
				if( !preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local)) ) {
					$is_valid = false;
				}
			}
			
			if( $is_valid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")) ) {
				// domain not found in DNS
				$is_valid = false;
			}
		}
		
		return $is_valid;
	}

    public static function human_filesize($bytes, $decimals = 2) {
	    $sz = 'BKMGTP';
	    $factor = floor((strlen($bytes) - 1) / 3);
	    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$sz[$factor] . 'B';
	}

	public function action_auth_ajax_verify($data) {
		if( !$verified = Utils::verify_wsse($data) ) {
			die('{error: "WSSE Failure"}');
		}
    }

	public function action_auth_ajax_wsse_update() {
		$ar = new AjaxResponse(200, null, Utils::WSSE());
		$ar->out();
	}
}
?>