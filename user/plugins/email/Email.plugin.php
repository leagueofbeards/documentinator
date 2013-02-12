<?php 
//Email.plugin.php - class to handle (currently only outbound) email
namespace Habari;

define('EMAIL_SERVER', 'smtp.mandrillapp.com');
define('EMAIL_PORT', 587);
define('EMAIL_ENCRYPTION', 'tls');
define('EMAIL_USER', 'chrisjdavis');
define('EMAIL_PASS', '4a72bb08-1891-4c09-ac74-dfa18b74329c');

class Email extends Plugin {
	const TYPE_COMMENT	 		= 'comment';
		
	const LOG_TYPE_MESSAGE		= 'M';
	const LOG_TYPE_ERROR		= 'E';
	const LOG_TYPE_TRACE		= 'T';
	const LOG_TYPE_BOUNCE		= 'B';
	const LOG_TYPE_SPAM			= 'S';
	const LOG_TYPE_DELIVERED	= 'D'; 
	
	const STATUS_INIT			= 'init';
	const STATUS_DRAFT 			= 'draft';
	const STATUS_NEW			= 'new';
	const STATUS_SCHEDULED		= 'scheduled';
	const STATUS_READ			= 'read';
	const STATUS_ARCHIVED		= 'archived';
	const STATUS_SENT			= 'sent';
	const STATUS_DEFERRED		= 'deferred';
	const STATUS_ERROR			= 'error';
	const STATUS_CANCELLED		= 'cancelled';
	const STATUS_TRASHED		= 'trashed';
	const STATUS_DELETED		= 'deleted';
	const STATUS_BOUNCED		= 'bounced';

	public function action_init() {
		DB::register_table('mail_headers');
		DB::register_table('mail_body');
		DB::register_table('mail_log');
		DB::register_table('mail_events');
		DB::register_table('mail_templates');
	}
	
	public function action_plugin_activation( $plugin_file ) {	
		$this->create_headers();
		$this->create_body();
		$this->create_log();
		$this->create_events();
		$this->create_templates();
		$this->create_beacon();
	}

	private function create_headers() {
		$q = "CREATE TABLE {\$prefix}mail_headers (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `internalmsgid` varchar(64) NOT NULL,
		  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `sent_datetime` timestamp NULL DEFAULT NULL,
		  `status` varchar(32) NOT NULL,
		  `company_id` int(11) unsigned DEFAULT NULL,
		  `from_user_id` int(11) unsigned DEFAULT NULL,
		  `to_user_id` int(11) unsigned DEFAULT NULL,
		  `to_member_id` int(11) unsigned DEFAULT NULL,
		  `from_email` varchar(255) NOT NULL,
		  `to_email` varchar(255) NOT NULL,
		  `cc_email` varchar(255) NOT NULL,
		  `subject` varchar(255) NOT NULL,
		  `message_type` varchar(32) NOT NULL,
		  `body_type` varchar(4) NOT NULL DEFAULT '' COMMENT 'html or text',
		  `opened` char(1) NOT NULL DEFAULT 'N',
		  `outbound_channel` varchar(10) NOT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `internalmsgid` (`internalmsgid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8";
		$sql = DB::dbdelta($q);
	}

	private function create_body() {
		$q = "CREATE TABLE {\$prefix}mail_body (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `internalmsgid` varchar(64) NOT NULL,
			  `body_html` text NOT NULL,
			  `body_text` text NOT NULL,
			  `headers` text NOT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `internalmsgid` (`internalmsgid`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8";
		$sql = DB::dbdelta($q);
	}

	private function create_log() {
		$q = "CREATE TABLE {\$prefix}mail_log (	
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `internalmsgid` varchar(64) DEFAULT NULL,
			  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  `notes` mediumtext,
			  `type` char(1) DEFAULT 'M',
			  PRIMARY KEY (`id`),
			  KEY `internalmsgid` (`internalmsgid`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8";
		$sql = DB::dbdelta($q);
	}

	private function create_events() {
		$q = "CREATE TABLE {\$prefix}mail_events (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `internalmsgid` varchar(64) NOT NULL,
			  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  `event` varchar(20) NOT NULL,
			  `response` varchar(255) NOT NULL,
			  `notes` text NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `internalmsgid` (`internalmsgid`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8";
		$sql = DB::dbdelta($q);
	}
		
	private function create_templates() {
		$q = "CREATE TABLE {\$prefix}mail_templates (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `template_name` varchar(32) NOT NULL,
			  `template_html` text NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8";
		$sql = DB::dbdelta($q);
	}
	
	private function create_beacon() {
		$q = "CREATE TABLE {\$prefix}mail_beacons (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`internalmsgid` varchar(64) NOT NULL,
			`datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`ip_address` varchar(16) NOT NULL,
			PRIMARY KEY (`id`),
			KEY `composite1` (`internalmsgid`,`datetime`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
	}

	private static function bootstrap() {
		$mail = new PHPMailer();
	    $mail->IsSMTP();
   		$mail->SMTPAuth = true;
		$mail->Host = 'smtp.mandrillapp.com';
		$mail->Port = 587;
		$mail->Username = 'chrisjdavis';
		$mail->Password = '4a72bb08-1891-4c09-ac74-dfa18b74329c';
		return $mail;
	}

	public function log($internalmsgid, $notes, $type=self::LOG_TYPE_MESSAGE) {
		if( trim($internalmsgid) == "" ) {
			return false;
		}
		
		$log				= array();
		$log['internalmsgid']	= $internalmsgid;
		$log['notes']			= $notes;
		$log['type']			= $type;
		return DB::insert( DB::table('mail_log'), $log );
	}
	
	public function logEvent($internalmsgid, $event_type, $response, $notes= "") {
		$event 					= array();
		$event['internalmsgid']	= $internalmsgid;
		$event['event']			= $event_type;
		$event['response']		= $response;
		$event['notes']			= $notes;
		
		return DB::insert( DB::table('mail_events'), $event );
	}

	public function parseHeaders($headers) {
		$result = array();
		$lines = explode( "\n", $headers );
		$last_header = "";
		for( $i=0; $i < sizeof($lines); $i++ ) {
			if( substr($lines[$i], 0, 1 )==" " || substr($lines[$i],0,1) == "\t" ) {
				if( trim($lines[$i]) != "" ) {
					if( $last_header == "" ) {
						$result['__INVALID__'] .= $lines[$i] . "\n";
					} else {
						$result[$last_header] .= " " . $lines[$i];
					}
				}
			} else {
				$parts = explode( ":", $lines[$i], 2 );
				if( sizeof($parts) == 2 ) {
					$last_header = trim( $parts[0] );
					$result[$last_header] = trim( $parts[1] );
					$label = strtolower( trim($parts[0]) );
				} else {
					if( trim($lines[$i]) != "" ) {
						$result['__INVALID__'] .= $lines[$i] . "\n";
					}
				}
			}
		}
		
		return $result;
	}

	public function serializeHeaders($header_array) {
		$header = "";
		foreach( $header_array as $key => $value ) {
			if( $key != "__INVALID__" ) {
				$header .= $key . ": " . wordwrap( $value, 75, "\n\t" ) . "\n";
			}
		}
		
		return $header;
	}

	public function emailAddressOnly($str) {
		$i = preg_match( '([A-Za-z0-9\._%\-\+]+@[A-Za-z0-9\.\-]+\.[A-Za-z]{2,6})', $str, $ret, PREG_OFFSET_CAPTURE );

		if( $i == 0 ) {
			return "";
		} else {
			return strtolower( $ret[0][0] );
		}
	}
	
	public function parseEmailAddress($str) {
		//take in $str and return an associative array of email address components including:
		//	example for "Steve" <steve@devoll.net>...
		//		email-address		- steve@devoll.net
		//		domain				- devoll.net
		//		local-part			- steve
		//		comment				- Steve
		$i = preg_match( '/(.*?)([A-Za-z0-9\._%\-\+]+)\@([A-Za-z0-9\.\-]+\.[A-Za-z]{2,6})(.*)/', $str, $ret, PREG_OFFSET_CAPTURE );
		
		if( $i == 0 ) {
			return false;
		}

		$result=array();
		$result['email-address']	= $ret[2][0] . "@" . $ret[3][0];
		$result['domain']			= $ret[3][0];
		$result['local-part']		= $ret[2][0];
		
		if( trim($ret[0][1]) != "" ) {
			preg_match( '/["]*([^"\<]*)["\<\s]*/', $ret[1][0], $ret2, PREG_OFFSET_CAPTURE );
			$result['comment'] = trim( $ret2[1][0] );
		}
		
		return $result;
	}
	
	public function generateInternalmsgid($prefix="") {
		$d = date("YmdHis");
		list( $usec, $sec ) = explode( " ", microtime() );
		$usec = implode( "",explode(".",$usec) );
		$r = rand();
		return( $prefix!="" ? ($prefix."-") : "") . $d . "-" . $usec . "-" . $r;
	}

	public function makeSubstitution($orig_string,$field_name,$field_value) {
		return str_replace('{{'.$field_name.'}}', $field_value, $orig_string);
	}

	public function cleanExtraFields($orig_string) {
		return preg_replace('/\{\{.*\}\}/i','',$orig_string);
	}

	public function loadTemplate($template_name) {
	    $template= DB::get_results("SELECT id,template_body FROM " . DB::table('mail_templates') . " WHERE template_name = '$template_name'");
	    return clone $template[0];
	}

	public function loadTranslatedTemplate($template_name,$sub_array = array()) {
		$template = self::loadTemplate($template_name);
		
		if ($template===false) {
			return false;
		}
		
		$template_html = $template->template_html;
		
		foreach ($sub_array as $key => $value) {
			$template_html = self::makeSubstitution($template_html,$key,$value);
		}
		
		$template_html = self::cleanExtraFields($template_html);
		return $template_html;
	}

	public static function log_email($company_id, $from_user_id, $to_user_id, $msg_type, $headers, $from, $to, $subject, $body, $body_type = "", $body_text = "") {
		if( $from == "" ) {
			$from="notify@leagueofbeards.com";
		}
		
		//compose it
		$internalmsgid = self::generateInternalmsgid($company_id);
		
		if( $headers=="" || (is_array($headers) && sizeof($headers)==0) ) {
			$headers = array( "From" => $from );
		}
		
		if( !is_array($headers) ) {
			$headers=self::parseHeaders( $headers );
		}
		
		$headers['X-internalmsgid'] = $internalmsgid;

		$smtpapi = array(
			'unique_args' => array(
				'internalmsgid' => $internalmsgid,
			)
		);
		
		$headers['X-SMTPAPI'] = json_encode( $smtpapi );
		$headers['X-Tag'] = $msg_type;
		$header_serialized = self::serializeHeaders( $headers );
		$body_html = ( $body_type=="text" ) ? str_replace( "\n","<br>",$body ) : $body;
		$body_text = ( $body_type=="text" ) ? $body : ( $body_text == "" ? html2text($body) : $body_text );

/*
		$message = Swift_Message::newInstance();
		$message->setSubject( $subject );
		$message->addPart( $body_text, 'text/plain' );
		
		if( $body_type == "html" ) {
			$message->addPart( $body_html, 'text/html' );
		}
		
		$message->setFrom( $from );
		$message->setTo( $to );
		
		$hdrs = $message->getHeaders();
		
		foreach ($headers as $key => $value) {
			$hdrs->addTextheader( $key, $value );
		}
*/
				
/*
		$transport = Swift_SmtpTransport::newInstance( EMAIL_SERVER, EMAIL_PORT, EMAIL_ENCRYPTION )->setUsername( EMAIL_USER )->setPassword( EMAIL_PASS );
		$mailer = Swift_Mailer::newInstance( $transport );
		$logger = new Swift_Plugins_Loggers_ArrayLogger();
		$mailer->registerPlugin( new Swift_Plugins_LoggerPlugin($logger) );
*/
			
		$header = array();
		$header['internalmsgid']		= $internalmsgid;
		$header['company_id'] 			= $company_id;
		$header['from_user_id'] 		= $from_user_id;
		$header['to_user_id'] 			= $to_user_id;
		$header['sent_datetime']		= DateTime::date_create()->get('Y-m-d H:i:s');
		$header['status']				= 0;
		$header['from_email']			= $from;
		$header['to_email']				= $to;
		$header['cc_email']				= "";
		$header['subject']				= $subject;
		$header['message_type']			= $msg_type;
		$header['body_type']			= $body_type;
		$header['outbound_channel']		= "mail";
		
		DB::insert( DB::table('mail_headers'), $header );

		$b = array();
		$b['internalmsgid']				= $internalmsgid;
		$b['body_html']					= $body_html;
		$b['body_text']					= $body_text;
		$b['headers']					= $header_serialized;
		
		DB::insert( DB::table('mail_body'), $b );

		return $result;
	}

	private static function get_commenters($message) {
		$peoples = DB::get_results( "SELECT DISTINCT email FROM {comments} WHERE post_id = " . $message );
		return $peoples;
	}

	public static function send_receipt($product, $user, $transaction) {
		$data = array();
		$data['headers'] = array();
		$data['from'] = 'checkout@leagueofbeards.com';
		$data['subject'] = 'Receipt for your purchase of ' . $product->title;
		$data['product'] = $product;
		$data['transaction_id'] = $transaction['charge_id'];
		$data['date'] = date( 'M d Y' );
		$data['discounted'] = $transaction['discounted'];
		$data['amount'] = $product->price;
		self::send_message( 'receipt', $user, $data );
	}

	public static function notify( $message, $user ) {
		$data = array();
		$data['headers'] = array();
		$data['subject'] = 'We have a new question!';
		$data['content'] = $message->content;
		$data['person_name'] = $user->displayname;
		$data['question_title'] = $message->title ? $message->title : 'View this Question';
		$data['question_url'] = URL::get( 'display_message', array('slug' => $message->slug) );
		self::send_message( 'notification', $user, $data );
	}

	public static function comment_made( $comment, $user ) {
		$i = 0;
		$data = array();
		$data['headers'] = array();
		$data['subject'] = 'You have a comment waiting for you!';
		$data['comment'] = $comment;
		$data['content'] = $comment->content_excerpt;
		$data['person_name'] = $user->displayname;
		$data['question_title'] = $comment->post->title;
		$data['question_url'] = URL::get( 'display_message', array('slug' => $comment->post->slug) );

		if(empty($data['subject'])) {
			$data['subject'] = 'Yo.';
		}
		
		if(empty($data['headers'])) {
			$data['headers'] = array();
		}
		
		if(isset($data['from'])) {
			$data['headers']['from'] = $data['from'];
		}

		$mail = self::bootstrap();
		$mail->Subject = $data['subject'];
		$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';

		$template = file_get_contents( dirname(__FILE__) . '/templates/comment.html' );
		$message = eregi_replace( "[\]", '', $template );

		$message = Message::get( array('id' => $data['comment']->post->id) );
		$recipients = self::get_commenters( $message->id );
		
		foreach( $recipients as $recipient ) {
			if( $recipient->email != $data['comment']->email ) {
				$i++;
				$peep = User::get( $recipient->email );
				$mail->AddAddress( $peep->email, $peep->displayname  );
			}
		}
				
		if( $i > 0 ) {		
			$mail->SetFrom( 'messages@leagueofbeards.com', 'Notifybot' );
			$message = str_replace( "{person_name}", $data['person_name'], $template );
			$message = str_replace( "{comment}", $data['content'], $message );
			$message = str_replace( "{question_title}", $data['question_title'], $message );
			$message = str_replace( "{question_url}", $data['question_url'], $message );
			
			$mail->MsgHTML( $message );
			
			$user = User::identify();
			$company_id = 1;
			$from_user_id = $user->id;
			$to_user_id = $touser->id ? $touser->id : 0;
			$msg_type = '';
			$headers = $data['headers'];
			$from = isset($data['headers']['from']) ? $data['headers']['from'] : null;
			$to = $data['to'];
			$subject = $data['subject'];
			$body = $message;
			$body_type = 'html';
			$body_text = '';
	
			self::log_email( $company_id, $from_user_id, $to_user_id, $msg_type, $headers, $from, $to, $subject, $body, $body_type, $body_text );
	
			if( !$mail->Send() ) {
				echo "Mailer Error: " . $mail->ErrorInfo;
			}
		}
	}

	public static function send_message($template, User $touser, $data) {
		$mail = self::bootstrap();
		$switch = $template;
				
		$mail->AltBody = '';
		
		switch( $switch ) {
			case 'invite' :
				$mail->Subject = 'Your Coworkspace Invite is here!';
				$mail->AddAddress( $touser->email, $touser->email );
				$mail->SetFrom( 'invites@coworkspace.us', 'InviteBot' );
				$body = "You have been invited to collaborate on a document at Coworkspace! Click the link to get started.\r\n\r\n" . $data['invite_link'];
			break;
		}
		
		$mail->MsgHTML($body);
		
		if( !$mail->Send() ) {
			echo "Mailer Error: " . $mail->ErrorInfo;
		}
	}
}
?>