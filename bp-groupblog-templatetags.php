<?php

class BP_Groupblog_Template {
	var $current_friendship = -1;
	var $blog_count;
	var $blog_posts;
	var $blog_post;
	
	var $in_the_loop;
	
	var $pag_page;
	var $pag_num;
	var $pag_links;
	
	var $group_id;
	
	function bp_groupblog_template( $group_id ) {
		global $bp;
		
		$this->pag_page = isset( $_REQUEST['page'] ) ? intval( $_REQUEST['page'] ) : 1;
		$this->pag_num = isset( $_REQUEST['num'] ) ? intval( $_REQUEST['num'] ) : 10;

		// Friendship Requests
		$this->blog_posts = ''; // Example: bp_groupblog_get_blog_posts( $bp->displayed_user->id );
		$this->total_blog_post_count = ''; // Example: $this->blog_posts['total'];

		$this->item_count = count($this->blog_posts);
		
		$this->pag_links = paginate_links( array(
			'base' => add_query_arg( 'fpage', '%#%' ),
			'format' => '',
			'total' => ceil($this->total_blog_post_count / $this->pag_num),
			'current' => $this->pag_page,
			'prev_text' => '&laquo;',
			'next_text' => '&raquo;',
			'mid_size' => 1
		));
	}
	
	function has_blog_posts() {
		if ( $this->item_count )
			return true;
		
		return false;
	}
	
	function next_blog_post() {
		$this->current_blog_post++;
		$this->blog_post = $this->blog_posts[$this->current_blog_post];
		
		return $this->blog_post;
	}
	
	function rewind_blog_posts() {
		$this->current_blog_post = -1;
		if ( $this->item_count > 0 ) {
			$this->blog_post = $this->blog_posts[0];
		}
	}
	
	function user_blog_posts() { 
		if ( $this->current_blog_post + 1 < $this->item_count ) {
			return true;
		} elseif ( $this->current_blog_post + 1 == $this->item_count ) {
			do_action('loop_end');
			// Do some cleaning up after the loop
			$this->rewind_blog_posts();
		}

		$this->in_the_loop = false;
		return false;
	}
	
	function the_blog_post() {
		global $blog_post, $bp;

		$this->in_the_loop = true;
		$this->blog_post = $this->next_blog_post();
				
		if ( 0 == $this->current_blog_post ) // loop has just started
			do_action('loop_start');
	}
}

function bp_groupblog_has_blog_posts() {
	global $bp, $blog_posts_template;

	$blog_posts_template = new BP_groupblog_Template( $bp->displayed_user->id );
	
	return $blog_posts_template->has_blog_posts();
}

function bp_groupblog_the_blog_post() {
	global $blog_posts_template;
	return $blog_posts_template->the_blog_post();
}

function bp_groupblog_blog_posts() {
	global $blog_posts_template;
	return $blog_posts_template->user_blog_posts();
}

function bp_groupblog_blog_post_name() {
	global $blog_posts_template;
	
	echo ''; // Example: $blog_posts_template->blog_post->name;
}

function bp_groupblog_blog_post_pagination() {
	global $blog_posts_template;
	
	echo $blog_posts_template->pag_links;
}

function bp_groupblog_show_enabled( $group_id ) {

  if  ( groups_get_groupmeta ( $group_id, 'groupblog_enable_blog' ) == '1' ) {
		echo ' checked="checked"';
  }

}

function bp_groupblog_is_blog_enabled( $group_id ) {
  $groupblog = new BP_Groupblog ( $group_id );
  
  if  ( groups_get_groupmeta ( $group_id, 'groupblog_enable_blog' ) == '1' ) {
  	return true;
  } else {
  	return false;
  }
}

function bp_groupblog_blog_exists( $group_id ) {

  if  ( !groups_get_groupmeta ( $group_id, 'groupblog_blog_id' ) == '' ) {
  	return true;
  } else {
  	return false;
  }

}

function bp_groupblog_show_selected( $group_id, $current_blog_id ) {
	$groupblog = new BP_Groupblog ( $group_id );
			
	if ( $groupblog->group_blog_id == $current_blog_id )
		echo ' selected="selected"';
}

function bp_groupblog_silent_add( $group_id ) {

  if  ( !groups_get_groupmeta ( $group_id, 'groupblog_silent_add' ) == '' ) {
  	return true;
  } else {
  	return false;
  }
	
}  

/*
 * groupblog_blog_id()
 * 
 * Echos the blog id of the current group's blog unless
 * $group_id is explicitly passed in.
 * 
 */
function groupblog_blog_id( $group_id = '' ) {   
  echo get_groupblog_blog_id( $group_id );
}
	function get_groupblog_blog_id( $group_id = '' ) {
		global $bp;
		
		if (  $group_id == '' ) {
			$group_id = $bp->groups->current_group->id;
		}
			
		return groups_get_groupmeta( $group_id, 'groupblog_blog_id' );
	}  

/*
 * groupblog_group_id()
 * 
 * Echos the group id of the group associated with the blog id that is passed in.
 * 
 */
function groupblog_group_id( $blog_id ) {
	echo get_groupblog_group_id( $blog_id );	
}
	function get_groupblog_group_id( $blog_id ) {
		global $bp, $wpdb;
		
		if ( !isset( $blog_id ) )
			return;
		
		if ( $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'groupblog_blog_id' AND meta_value = %d", $blog_id ) ) ) {
			return $row->group_id;
		}	
	}

/*
 * bp_groupblog_id()
 * 
 * Echos the group id of the group associated with the blog id.
 * 
 */
function bp_groupblog_id() {
	echo bp_get_groupblog_id();
}
	function bp_get_groupblog_id() {
		global $current_blog;
		
		return apply_filters( 'bp_get_groupblog_id', get_groupblog_group_id( $current_blog->blog_id ) );
	}

/*
 * bp_groupblog_slug()
 * 
 * Echos the group slug of the group associated with the blog id.
 * 
 */	
function bp_groupblog_slug() {
	echo bp_get_groupblog_slug();
}
	function bp_get_groupblog_slug() {

		$group = new BP_Groups_Group( bp_get_groupblog_id(), false, false );		
		return apply_filters( 'bp_get_groupblog_slug', $group->slug );
	}

function bp_groupblog_forum() {
 echo bp_get_groupblog_forum();
}
	function bp_get_groupblog_forum() {
		global $bp;
	
		$forum_id = groups_get_groupmeta( bp_get_groupblog_id(), 'forum_id' );	
		return apply_filters( 'bp_get_groupblog_forum', $forum_id );
	}

/*
 * bp_groupblog_admin_form_action()
 *
 */
function bp_groupblog_admin_form_action( $page, $group = false ) {
	global $bp, $groups_template;

	if ( !$group )
		$group =& $groups_template->group;
	
	echo apply_filters( 'bp_groupblog_admin_form_action', bp_group_permalink( $group, false ) . '/admin/' . $page );
}
?>