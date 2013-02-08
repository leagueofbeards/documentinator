<?php

namespace Habari;

define('GRANTEE_USER', 1);
define('GRANTEE_GROUP', 2);

class Permissions extends Plugin
{
	public function action_init() {
		DB::register_table('user_permissions');
		if(User::identify()->can('superuser')) {
			$this->add_rule('"admin"/"permissions"/post_id', 'admin_permissions');
			$this->add_template( 'permissions_admin', __DIR__ . '/permissions.admin.php' );
		}
	}

	public function action_plugin_activation( $file ) {
		$sql = "CREATE TABLE {\$prefix}user_permissions (
				grantee_id INT UNSIGNED NOT NULL,
				grantee_type TINYINT UNSIGNED NOT NULL,
				post_id INT UNSIGNED NOT NULL,
				access_mask TINYINT UNSIGNED NOT NULL,
				PRIMARY KEY (grantee_id, grantee_type, post_id)
				) DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
		";
		
		DB::dbdelta($sql);
	}

	public function perm_bitmask($what) {
		$bitmask = new Bitmask(array('read', 'edit', 'delete'));

		if(is_integer($what)) {
			$bitmask->value = $what;
		} else {
			$what = Utils::single_array($what);
			foreach($what as $bit) {
				$bitmask->$bit = true;
			}
		}
		
		return $bitmask;
	}
	
	/**
	 * function perms_apply_to
	 * Identify the objects in the system to apply our permisions to
	 * @param array Associative array of active post types
	 * @return array An array of post types to apply permissions to
	 *
	 **/
	public function perms_apply_to() {
		$types = Post::list_active_post_types();
		$types = array_intersect_key(
			$types,
			array(
				'section'	=>	1,
				'chapter'	=>	1,
				'page'		=>	1,
				'note'		=>	1,
			)
		);
		
		return $types;
	}

	public function action_posts_get_query(Query $query, $paramarray) {
		// Create a new QueryWhere to override the core one, and allow it to see anything
		$master_perm_where = new QueryWhere();
		$master_perm_where->add('1=1');

		$user = User::identify();

		if(isset($paramarray['ignore_permissions']) && $paramarray['ignore_permissions'] == true) {
			// Don't use the core permissions if permissions are ignored
			$query->where()->add($master_perm_where, array(), 'master_perm_where');
		} else {
			if(!$user->can('super_user')) {
				// Only join permissions if the user is not a superuser, who can see everything
				$query->join('LEFT JOIN {user_permissions} up ON {posts}.id=up.post_id', array(), 'user_permissions__allowed');
	
				// Any of the conditions on $perm_where permit the user access to a post
				$perm_where = new QueryWhere('OR');
				$perm_where->in('{posts}.content_type', $this->perms_apply_to(), 'resticted_types', 'intval', false);

				$outer_where = new QueryWhere('AND');
				$outer_where->in('{posts}.content_type', $this->perms_apply_to(), 'resticted_types', 'intval');
				$read_bit = $this->perm_bitmask('read')->value;
				$outer_where->add('up.access_mask & ' . $read_bit . ' = ' . $read_bit);

				$inner_where = new QueryWhere('OR');
				
				// Does the user have access directly (shared)
				$shared = new QueryWhere();
				$shared->add('up.grantee_id = :grantee_user_id', array('grantee_user_id' => $user->id));
				$shared->add('up.grantee_type = :grantee_type_user', array('grantee_type_user' => GRANTEE_USER));
				$inner_where->add($shared);

				// Does the user's company have access?
				$client = new QueryWhere();
				$client->add('up.grantee_id = :grantee_client_id', array('grantee_client_id' => $user->info->works_for));
				$client->add('up.grantee_type = :grantee_type_group', array('grantee_type_group' => GRANTEE_GROUP));
				$inner_where->add($client);
				$outer_where->add($inner_where);
				$perm_where->add($outer_where);
				
				// Add the where clauses to the master condition
				$master_perm_where->add($perm_where);
			}

			// Replace the master permission where clause with ours, overriding any permissions previously set
			$query->where()->add($master_perm_where, array(), 'master_perm_where');
		}
	}

	function filter_post_call_create_default_permissions($unused, $post) {
		if( in_array($post->content_type, $this->perms_apply_to()) ) {
			// Grant access to the company of the user that created it
			// Get the user that created it
			$user = User::get($post->author_id);
			if( isset($user->info->works_for) ) { // If the user belongs to a company
				// Get the company that the user belongs to			
				$client = Posts::get(array('fetch_fn' => 'get_row', 'content_type' => 'client', 'id' => $user->info->works_for, 'ignore_permissions' => true));
				// Give the whole company full access
				$post->grant($client, 'full');
			} else {
				// Grant access to just this user (this is usually going to be the creation of a company post)
				$post->grant($user, 'full');
			}
		}
	}

	/**
	 * Implement $post->grant($whom, $what)
	 * @param null $unused This is unused
	 * @param Post $post The Post being granted permission to
	 * @param Post|User $whom To whom to grant the permission
	 * @param integer $what A bitmask
	 */
	function filter_post_call_grant($unused, $post, $whom, $what) {
		$what = $this->perm_bitmask($what);
		if($whom instanceof Post) {
			DB::update(
				'{user_permissions}',
				array(
					'access_mask' => $what->value,
				), array(
					'grantee_id' => $whom->id,
					'grantee_type' => GRANTEE_GROUP,
					'post_id' => $post->id,
				)
			);
		} elseif($whom instanceof User) {
			DB::update(
				'{user_permissions}',
				array(
					'access_mask' => $what->value,
				), array(
					'grantee_id' => $whom->id,
					'grantee_type' => GRANTEE_USER,
					'post_id' => $post->id,
				)
			);
		}
	}

	/**
	 * Implement $post->revoke($whom) to revoke permissions on an object
	 * @param null $unused This is unused
	 * @param Post $post The Post being granted permission to
	 * @param Post|User $whom To whom to grant the permission
	 */
	function filter_post_call_revoke($unused, $post, $whom) {
		if($whom instanceof Post) {
			DB::delete('{user_permissions}', array(
				'grantee_id' => $whom->id,
				'grantee_type' => GRANTEE_GROUP,
				'post_id' => $post->id,
			));
		} elseif($whom instanceof User) {
			DB::delete('{user_permissions}', array(
				'grantee_id' => $whom->id,
				'grantee_type' => GRANTEE_USER,
				'post_id' => $post->id,
			));
		}
	}

	/**
	 * Implement $post->get_permissions($whom) to retrieve the permissions for an object
	 * @param null $unused This is unused
	 * @param Post $post The Post being granted permission to
	 * @param Post|User $whom To whom to grant the permission
	 * @return Bitmask The permissions associated to the object for this user/group
	 */
	function filter_post_call_get_permissions($unused, $post, $whom = null) {
		if(empty($whom)) {
			$access_masks = DB::get_column(
				'SELECT access_mask FROM {user_permissions} up WHERE (
					(up.grantee_id = :grantee_user AND up.grantee_type = :grantee_user_type) OR
					(up.grantee_id = :grantee_group AND up.grantee_type = :grantee_group_type)
					) AND up.post_id = :post_id', array(
					'grantee_user' => User::identify()->id,
					'grantee_user_type' => GRANTEE_USER,
					'grantee_group' => User::identify()->info->works_for,
					'grantee_group_type' => GRANTEE_GROUP,
					'post_id' => $post->id
				)
			);
			
			$access_mask = 0;
			foreach($access_masks as $mask) {
				$access_mask = $access_mask | $mask;
			}
		} else {
			$access_mask = DB::get_value(
				'SELECT access_mask FROM {user_permissions} up WHERE up.grantee_id = :grantee_id AND up.grantee_type = :grantee_type AND up.post_id = :post_id',
				array(
					'grantee_id' => $whom->id,
					'grantee_type' => $whom instanceof User ? GRANTEE_USER : GRANTEE_GROUP,
					'post_id' => $post->id
				)
			);
		}
		
		$bitmask = new Bitmask(array('read', 'edit', 'delete'));
		$bitmask->value = $access_mask;
		return $bitmask;
	}

	public function filter_comments_get_paramarray($paramarray) {
		$paramarray['ignore_permissions'] = true;
		return $paramarray;
	}

	public function filter_post_actions($actions, $post) {
		if(User::identify()->can('superuser')) {
			switch($post->content_type) {
				case Post::type('invoice'):
				case Post::type('client'):
					$actions['permissions'] = array( 'url' => URL::get('admin_permissions', array('post_id' => $post->id)), 'title' => _t( 'Manage permissions for this item' ), 'label' => _t( 'Permissions' ), 'permission' => 'edit' );
					break;
			}
		}
		
		return $actions;
	}

	public function theme_route_admin_permissions(Theme $theme) {
		$sql = "SELECT up.*, u.*, p.*
				FROM {user_permissions} up
				LEFT JOIN {users} u
				ON u.id = up.grantee_id AND up.grantee_type = 1
				LEFT JOIN {posts} p
				ON p.id = up.grantee_id AND up.grantee_type = 2
				WHERE up.post_id = :post_id
		";

		$grants = array();
		$grant_data = DB::get_results( $sql, array('post_id' => Controller::get_var('post_id')) );

		foreach($grant_data as $grant) {
			switch($grant->grantee_type) {
				case GRANTEE_USER:
					$grant->grant_type_name = 'User';
					$grant->grantee = $grant->username;
					break;
				case GRANTEE_GROUP:
					$grant->grant_type_name = 'Client';
					$grant->grantee = $grant->title;
					break;
				default:
					Utils::debug('uh oh');
			}
			$grant->permissions = (string) $this->perm_bitmask(intval($grant->access_mask));
			$grants[] = $grant;
		}

		$theme->post = Post::get(Controller::get_var('post_id'));
		$theme->grants = $grants;
		$theme->display('permissions_admin');
	}

}

?>