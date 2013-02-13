<?php
namespace Habari;
class People extends Plugin
{
	public function action_init() {	
		$this->add_template( 'account', __DIR__ . '/views/account.php' );
		$this->add_template( 'account.sidebar', __DIR__ . '/views/account.sidebar.php' );
		$this->add_template( 'account.integrations', __DIR__ . '/views/account.integrations.php' );
	}

	public function filter_default_rewrite_rules( $rules ) {
		$this->add_rule('"i"/slug', 'display_invite');
		$this->add_rule('"account"/slug/"integrations"', 'display_integrations');		
		$this->add_rule('"account"/slug', 'display_useraccount');
		return $rules;
	}

	public function theme_route_display_invite($theme) {
		$user = Users::get( array('info' => array('invite_code' => $theme->matched_rule->named_arg_values['slug'])) );
		
		$theme->title = 'Almost there!';
		$theme->person = $user[0];
		$theme->display( 'confirm.account' );
	}

	public function theme_route_display_integrations($theme) {
		$user = Users::get( array('info' => array('invite_code' => $theme->matched_rule->named_arg_values['slug'])) );

		$theme->title = 'Your Integrations';
		$theme->person = $user[0];
		$theme->display( 'account.integrations' );
	}

	public function theme_route_display_useraccount($theme) {
		$theme->person = User::get( $theme->matched_rule->named_arg_values['slug'] );
		$theme->title = 'Your Profile';
		
		$theme->display( 'account' );
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
			$user->remove_from_group('authenticated');
			DocumentsPlugin::connect_doc( $user, $vars['id'] );

			$user->info->invite_date = DateTime::date_create()->int;
			$user->info->invite_code = Utils::nonce();
			$user->info->commit();
			
			$document->grant( $user, 'read');
			
			$status = 200;
			$message = 'We added ' . $vars['invitee'] . ' to the approvers list.';
			$data = array( 'invite_link' => URL::get('display_invite', array('slug' => $user->info->invite_code)) );
			Email::send_message( 'invite', $user, $data );
		} catch( Exception $e ) {
			$status = 401;
			$message = 'We couldn\'t add ' . $vars['invitee'] . ' to the approvers list.';
		}
		
		$ar = new AjaxResponse( $status, $message, null );
		$ar->html( '#participating', '#' );
		$ar->out();
	}

	public function action_auth_ajax_update_approver($data) {
		$vars = $data->handler_vars;
		$user = User::get_by_id( $vars['id'] );
		$group = UserGroup::get('authenticated');
		$group->add( $user );
		$user->remove_from_group('quarantine');
		
		$user->email = $vars['email'];
		$user->username = $vars['email'];
		$user->password = Utils::crypt( $vars['password'] );
		$user->update();
		
		$user->info->join_date = DateTime::date_create()->int;
		$user->info->displayname = $vars['name'];
		$user->info->invite_code = '';
		$user->info->commit();
	}
	
	public function action_auth_ajax_update_account($data) {
		$vars = $data->handler_vars;
		$user = User::get_by_id( $vars['id'] );
		$user->email = $vars['email'];
		$user->username = $vars['username'];
		$user->info->displayname = $vars['name'];

		if( $vars['password'] != '' ) {
			$user->password = Utils::crypt( $vars['password'] );
		}

		try {
			$user->update();
			$user->info->commit();
			$status = 200;
			$message = 'Your account has been updated.';
		} catch( Exception $e ) {
			$status = 401;
			$message = 'We couldn\'t update your account, please try again.';
		}
		
		$ar = new AjaxResponse( $status, $message, null );
		$ar->html( '#user_update', '#' );
		$ar->out();
		
	}
}
?>