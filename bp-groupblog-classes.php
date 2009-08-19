<?php

class BP_Groupblog {
	var $id;
	var $enable_group_blog;
	var $group_id;
	var $group_blog_id;
	var $group_blog_posts;
	var $gb_bj_enable;
	var $gb_bj_default_role;
	var $gb_bj_default_moderator_role;
	var $update;
	var $message;
	
	/**
	 * bp_groupblog()
	 *
	 * This is the constructor, it is auto run when the class is instantiated.
	 * It will either create a new empty object if no ID is set, or fill the object
	 * with a row from the table if an ID is provided.
	 */
	function bp_groupblog( $id = null, $group_blog_id = null ) {
		if ( $id || $group_blog_id ) {
			$this->group_id = $id;
			$this->populate( $id, $group_blog_id );
			$this->message .= "Group ID: " . $id;
		}			
	}
	
	/**
	 * populate()
	 *
	 * This method will populate the object with a row from the database, based on the
	 * ID passed to the constructor.
	 */
	function populate( $id, $blog_id ) {
		global $wpdb, $bp, $creds;
		
		if ( $id ) {
			if ( $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bp->groupblog->table_name} WHERE group_id = %d", $this->group_id ) ) ) {
				$this->enable_group_blog = $row->enable_group_blog;
				$this->group_blog_id = $row->group_blog_id;
				$this->gb_bj_enable = $row->gb_bj_enable;
				$this->gb_bj_default_role = $row->gb_bj_default_role;
				$this->gb_bj_default_moderator_role = $row->gb_bj_default_moderator_role;
				$this->update = 1;
				$this->message .= " ...Populating";
			}
		} else {
			if ( $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bp->groupblog->table_name} WHERE group_blog_id = %d", $blog_id ) ) ) {
				$this->group_id = $row->group_id;
				$this->enable_group_blog = $row->enable_group_blog;
				$this->group_blog_id = $blog_id;
				$this->gb_bj_enable = $row->gb_bj_enable;
				$this->gb_bj_default_role = $row->gb_bj_default_role;
				$this->gb_bj_default_moderator_role = $row->gb_bj_default_moderator_role;
				$this->update = 1;
				$this->message .= " ...Populating by group_blog_id";
			}
		}
	}

	/**
	 * save()
	 *
	 * This method will save an object to the database. It will dynamically switch between
	 * INSERT and UPDATE depending on whether or not the object already exists in the database.
	 */
	function save() {
		global $wpdb, $bp;
		
		if ( $this->update == 1 ) {
			$this->message .= "...Updating..." . $this->gb_bj_enable . ' ' . $this->gb_bj_default_role . ' ' . $this->gb_bj_default_moderator_role;
			// Update
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$bp->groupblog->table_name} SET enable_group_blog = %d, gb_bj_enable = %d, gb_bj_default_role = '" . $this->gb_bj_default_role . "', gb_bj_default_moderator_role = '" . $this->gb_bj_default_moderator_role . "' WHERE group_id = %d", $this->enable_group_blog, $this->gb_bj_enable, $this->group_id ) );
			$this->message .= $wpdb->prepare( "UPDATE {$bp->groupblog->table_name} SET enable_group_blog = %d, group_blog_id = %d, gb_bj_enable = %d, gb_bj_default_role = %d, gb_bj_default_moderator_role = %d WHERE group_id = %d", $this->enable_group_blog, $this->group_blog_id, $this->group_id, $this->gb_bj_enable, $this->gb_bj_default_role, $this->gb_bj_default_moderator_role ) . ' ' . $result;
		} else {
			$this->message .= "...Saving..." . $this->gb_bj_enable . $this->gb_bj_default_role . $this->gb_bj_default_moderator_role;
			// Save
			$result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$bp->groupblog->table_name} ( enable_group_blog, group_blog_id, group_id, gb_bj_enable, gb_bj_default_role, gb_bj_default_moderator_role ) VALUES ( %d, %d, %d, %d, %d, %d )", $this->enable_group_blog, $this->group_blog_id, $this->group_id, $this->gb_bj_enable, $this->gb_bj_default_role, $this->gb_bj_default_moderator_role ) );
			$this->id = $wpdb->insert_id;
		}
		
		//bp_core_add_message ( $this->message );
		return $result;
	}

	/**
	 * delete()
	 *
	 * This method will delete the corresponding row for an object from the database.
	 */
	function delete() {
		global $wpdb, $bp;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->example->table_name} WHERE id = %d", $this->id ) );
	}
}

/**
 * Group API
 *
 * http://codex.buddypress.org/developer-docs/group-extension-api/
 */
class BP_Groupblog_Extension extends BP_Group_Extension {	
	
	//var $enable_nav_item = $this->enable_nav_item();
		  
	function bp_groupblog_extension() {
		global $bp;
	
		$this->name = 'Group Blog';
		$this->slug = 'group-blog';
		
		$this->enable_create_step = true;
		$this->create_step_position = 15;
		
		$this->enable_edit_item = true;
		
		$this->nav_item_name = 'Blog';
		$this->nav_item_position = 30;
		$this->enable_nav_item = false;
		$this->template_file = 'groupblog/blog';
	}
	
	function create_screen() {
		global $bp, $groupblog_create_screen;
		
		if ( !bp_is_group_creation_step( $this->slug ) )
			return false;
					
		$groupblog_create_screen = true;
						
		bp_groupblog_signup_blog();
		
		echo '<input type="hidden" name="groupblog-group-id" value="' . $bp->groups->current_group->id . '" />';
		echo '<input type="hidden" name="groupblog-create-save" value="groupblog-create-save" />';
							
		wp_nonce_field( 'groups_create_save_' . $this->slug );
	}

	function create_screen_save() {	
	}

	function edit_screen() {
		global $bp;
		
		if ( !bp_is_group_admin_screen( $this->slug ) )
			return false;
				  											
		bp_groupblog_signup_blog();
									
	}

	function edit_screen_save() {
	}
	
	function display() {
	}
	
	function widget_display() {
	}

	/*
	function enable_nav_item() {
		global $bp;
	
		if ( groups_get_groupmeta( $bp->groups->current_group->id, 'groupblog_enable_blog' ) )
			return true;
		else
			return false;
	}
	*/
	
}
bp_register_group_extension( 'BP_Groupblog_Extension' );

?>