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
				range_text text,
				range_coords varchar(255),
				PRIMARY KEY (`id`),
				KEY `user_id` (`user_id`),
				KEY `post_id` (`post_id`)
				) DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;";

		DB::dbdelta($sql);
	}

	private function get_selections($vars) {
		$return = array();
		$selections = DB::get_results( "SELECT id, user_id, post_id, range_text, range_coords FROM {annotations} WHERE post_id = ?", array($vars['post_id']) );
		foreach( $selections as $selection ) {
			$ret = array(
						'id' => $selection->id,
						'user_id' => $selection->user_id,
						'post_id' => $selection->post_id,
						'range_text' => $selection->range_text,
						'range_coords' => $selection->range_coords,
					);
			$return[] = $ret;
		}
		
		echo json_encode( $return );
	}

	private function save_selection($args) {
		DB::insert( DB::table('annotations'), $args );
	}

	public function action_auth_ajax_save_selection($data) {
		$vars = $data->handler_vars;
		$args = array( 'post_id' => $vars['post_id'], 'user_id' => $vars['user_id'], 'range_text' => $vars['range_text'] );
		$this->save_selection( $args );
	}
	
	public function action_auth_ajax_get_selections($data) {
		$vars = $data->handler_vars;
		return $this->get_selections( $vars );
	}
}
?>