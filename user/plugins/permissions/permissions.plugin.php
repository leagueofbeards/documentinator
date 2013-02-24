<?php
namespace Habari;
define('GRANTEE_USER', 1);
define('GRANTEE_GROUP', 2);

class MwPermsPlugin extends Plugin
{
	public function action_init() {
		DB::register_table('user_permissions');
	}

	public function action_plugin_activation( $file ) {
		// Let's make a permission table
		$sql = <<< ADD_PERMISSIONS_TABLE
CREATE TABLE {\$prefix}user_permissions (
  grantee_id INT UNSIGNED NOT NULL,
  grantee_type TINYINT UNSIGNED NOT NULL,
  post_id INT UNSIGNED NOT NULL,
  access_mask TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (grantee_id, grantee_type, post_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

ADD_PERMISSIONS_TABLE;

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

	public function filter_perm_bitmask($what) {
		return $this->perm_bitmask($what);
	}

	public function perms_apply_to() {
		$types = Post::list_active_post_types();
		$types = array_intersect_key(
			$types,
			array(
				'document' => 1
			)
		);
		
		$types = Plugins::filter('get_document_types', $types);
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
		}
		else {
			// Only join permissions if the user is not a superuser, who can see everything
			if(!$user->can('super_user')) {

				// Join the posts table to the permissions
				$query->join('INNER JOIN {user_permissions} up ON {posts}.id=up.post_id', array(), 'user_permissions__allowed');

				$query->select('up.access_mask');

				// Any of the conditions on $perm_where permit the user access to a post
				// @todo restrict access to line items based on the user's permission to the related invoice_id!
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
				$company = new QueryWhere();
				$company->add('up.grantee_id = :grantee_company_id', array('grantee_company_id' => $user->info->company));
				$company->add('up.grantee_type = :grantee_type_group', array('grantee_type_group' => GRANTEE_GROUP));
				$inner_where->add($company);

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
		if(in_array($post->content_type, $this->perms_apply_to())) {
			$user = User::get($post->author_id);
			if(isset($user->info->company)) {
				$company = Posts::get(array('fetch_fn' => 'get_row', 'content_type' => 'company', 'id' => $user->info->company, 'ignore_permissions' => true));
				$post->grant($company, 'full');
			} else {
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
				),
				array(
					'grantee_id' => $whom->id,
					'grantee_type' => GRANTEE_GROUP,
					'post_id' => $post->id,
				)
			);
		}
		elseif($whom instanceof User) {
			DB::update(
				'{user_permissions}',
				array(
					'access_mask' => $what->value,
				),
				array(
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
		}
		elseif($whom instanceof User) {
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
	function filter_post_call_get_permissions($unused, $post, $whom = null)	{
		$bitmask = new Bitmask(array('read', 'edit', 'delete'));
		if(empty($whom)) {
			$access_masks = DB::get_column(
				'SELECT access_mask FROM {user_permissions} up WHERE (
					(up.grantee_id = :grantee_user AND up.grantee_type = :grantee_user_type) OR
					(up.grantee_id = :grantee_group AND up.grantee_type = :grantee_group_type)
					) AND up.post_id = :post_id',
				array(
					'grantee_user' => User::identify()->id,
					'grantee_user_type' => GRANTEE_USER,
					'grantee_group' => User::identify()->info->company,
					'grantee_group_type' => GRANTEE_GROUP,
					'post_id' => $post->id
				)
			);
			$access_mask = 0;
			foreach($access_masks as $mask) {
				$access_mask = $access_mask | $mask;
			}
			if(User::identify()->can('super_admin')) {
				$access_mask = $bitmask->full;
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
		$bitmask->value = $access_mask;
		return $bitmask;
	}

	/**  TESTS **/
	public function filter_list_feature_steps($feature_steps) {
		$plugin_feature_steps = glob(dirname(__FILE__) . '/steps/step_*.php');
		$feature_steps = array_merge($feature_steps, $plugin_feature_steps);
		return $feature_steps;
	}

	public function filter_list_features($features) {
		$plugin_features = glob(dirname(__FILE__) . '/tests/features/*.feature');
		$features = array_merge($features, $plugin_features);
		return $features;
	}

	public function filter_list_step_definitions($step_files) {
		$plugin_step_files = glob(dirname(__FILE__) . '/tests/step_definitions/*.php');
		$step_files = array_merge($plugin_step_files, $step_files);
		return $step_files;
	}

	public function filter_comments_get_paramarray($paramarray) {
		$paramarray['ignore_permissions'] = true;
		return $paramarray;
	}

	public function filter_post_actions($actions, $post) {
		if(User::identify()->can('superuser')) {
			switch($post->content_type) {
				case Post::type('invoice'):
				case Post::type('company'):
					$actions['permissions'] = array( 'url' => URL::get('admin_permissions', array('post_id' => $post->id)), 'title' => _t( 'Manage permissions for this item' ), 'label' => _t( 'Permissions' ), 'permission' => 'edit' );
					break;
			}
		}
		return $actions;
	}
}

?>