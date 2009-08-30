<?php
/*
Plugin Name: BP Groupblog
Plugin URI: http://wordpress.org/extend/plugins/search.php?q=buddypress+groupblog
Description: Automates and links WPMU blogs groups controlled by the group creator.
Author: Rodney Blevins & Marius Ooms
Version: 1.1.3
License: (Groupblog: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html)
Site Wide Only: true
*/

define ( 'BP_GROUPBLOG_IS_INSTALLED', 1 );
define ( 'BP_GROUPBLOG_VERSION', '1.1.3' );
define ( 'BP_GROUPBLOG_DEFAULT_ADMIN_ROLE', 'administrator' );
define ( 'BP_GROUPBLOG_DEFAULT_MOD_ROLE', 'editor' );
define ( 'BP_GROUPBLOG_DEFAULT_MEMBER_ROLE', 'author' );

// Base groupblog component slug
if ( !defined( 'BP_GROUPBLOG_SLUG' ) )
define ( 'BP_GROUPBLOG_SLUG', 'group-blog' );

/**
 * Load the required groupblog component files.
 */
require ( WP_PLUGIN_DIR . '/bp-groupblog/bp-groupblog-cssjs.php' );
require ( WP_PLUGIN_DIR . '/bp-groupblog/bp-groupblog-classes.php' );
require ( WP_PLUGIN_DIR . '/bp-groupblog/bp-groupblog-templatetags.php' );

/**
 * Add language support.
 */
if ( file_exists( WP_PLUGIN_DIR . '/bp-groupblog/languages/' . get_locale() . '.mo' ) )
	load_textdomain( 'groupblog', WP_PLUGIN_DIR . '/bp-groupblog/languages/' . get_locale() . '.mo' );

/**
 * bp_groupblog_setup_globals()
 */
function bp_groupblog_setup_globals() {
	global $bp, $wpdb;
			
	$bp->groupblog->image_base = WP_PLUGIN_DIR . '/bp-groupblog/images';
	$bp->groupblog->slug = BP_GROUPBLOG_SLUG;
	$bp->groupblog->default_admin_role = BP_GROUPBLOG_DEFAULT_ADMIN_ROLE;
	$bp->groupblog->default_mod_role = BP_GROUPBLOG_DEFAULT_MOD_ROLE;
	$bp->groupblog->default_member_role = BP_GROUPBLOG_DEFAULT_MEMBER_ROLE;

}
add_action( 'plugins_loaded', 'bp_groupblog_setup_globals', 5 );	
add_action( 'admin_menu', 'bp_groupblog_setup_globals', 1 );

/**
 * bp_groupblog_setup_nav()
 */
function bp_groupblog_setup_nav() {
	global $bp, $current_blog;
	
	if ( $bp->current_component == $bp->groups->slug && $bp->is_single_item ) {

		$bp->groups->current_group->is_group_visible_to_member = ( 'public' == $bp->groups->current_group->status || $is_member ) ? true : false;
	
		$group_link = $bp->root_domain . '/' . $bp->groups->slug . '/' . $bp->groups->current_group->slug . '/';
		
		if ( bp_groupblog_is_blog_enabled( $bp->groups->current_group->id ) )
			bp_core_new_subnav_item(
				array(
					'name' => __( 'Blog', 'groupblog' ),
					'slug' => 'blog',
					'parent_url' => $group_link,
					'parent_slug' => $bp->groups->slug,
					'screen_function' => 'groupblog_screen_blog',
					'position' => 32,
					'item_css_id' => 'nav-group-blog'
				)
			);
	}
}
add_action( 'wp', 'bp_groupblog_setup_nav', 2 );
add_action( 'admin_menu', 'bp_groupblog_setup_nav', 2 );

/**
 * groupblog_edit_settings()
 *
 * Save the blog-settings accessible only by the group admin or mod.
 */
function groupblog_edit_settings() {
	global $bp, $groupblog_blog_id, $errors;

	$group_id = $_POST['groupblog-group-id'];
	
	if ( !isset( $group_id ) )
	    $group_id = $bp->groups->current_group->id;
	
	//bp_core_add_message( "Trying to save setting. Save: " . $_POST['save'] . " Create new: " . $_POST['groupblog-create-new'] . " Group ID: " . $group_id . " Blog ID: " . $_POST['groupblog-blogid'] );
		
	if ( $bp->current_component == $bp->groups->slug && 'group-blog' == $bp->action_variables[0] ) {
		if ( $bp->is_item_admin || $bp->is_item_mod  ) {

			// If the edit form has been submitted, save the edited details
			if ( isset( $_POST['save'] ) ) {
				if ( !bp_groupblog_blog_exists( $bp->groups->current_group->id ) ) {
					if ( isset( $_POST['groupblog-enable-blog'] ) ) {
					    if ( $_POST['groupblog-create-new'] == 'yes' ) {
					        //Create a new blog and assign the blog id to the global $groupblog_blog_id
    						if ( !bp_groupblog_validate_blog_signup() ) {
    							$errors = $filtered_results['errors'];
    							bp_core_add_message ( $errors );
    							$group_id = '';
    						}
    					} else if ( $_POST['groupblog-create-new'] == 'no' ) {
    					    // They're using an existing blog, so we try to assign that to $groupblog_blog_id
    					    if ( !( $groupblog_blog_id = $_POST['groupblog-blogid'] ) ) {
    					        //They forgot to choose a blog, so send them back and make them do it!
        						bp_core_add_message( __( 'Please choose one of your blogs from the drop-down menu.' . $group_id, 'groupblog' ), 'error' );
        						if ( $bp->action_variables[0] == 'step' ) {
        							bp_core_redirect( $bp->loggedin_user->domain . $bp->groups->slug . '/create/step/' . $bp->action_variables[1] );
        						} else {
        							bp_core_redirect( site_url() . '/' . $bp->current_component . '/' . $bp->current_item . '/admin/group-blog' );
        						}
    					    }
    					}
					}
				} else {
				    // They already have a blog associated with the group, we're just saving other settings
					$groupblog_blog_id = groups_get_groupmeta ( $bp->groups->current_group->id, 'groupblog_blog_id' );
				}

				if ( !groupblog_edit_base_settings( $_POST['groupblog-enable-blog'], $_POST['groupblog-silent-add'], $_POST['default-administrator'], $_POST['default-moderator'], $_POST['default-member'], $bp->groups->current_group->id, $groupblog_blog_id ) ) {
					bp_core_add_message( __( 'There was an error creating your group blog, please try again.', 'groupblog' ), 'error' );
				} else {
					bp_core_add_message( __( 'Group details were successfully updated.', 'groupblog' ) );
				}

				do_action( 'groupblog_details_edited', $bp->groups->current_group->id );

				bp_core_redirect( site_url() . '/' . $bp->current_component . '/' . $bp->current_item . '/admin/group-blog' );
			}
		}
	}
}
add_action( 'wp', 'groupblog_edit_settings', 4 );

/**
 * groupblog_edit_base_settings()
 *
 * Updates the groupmeta with the blog_id and if it is enabled or not.
 */
function groupblog_edit_base_settings( $groupblog_enable_blog, $groupblog_silent_add = NULL, $groupblog_default_admin_role, $groupblog_default_mod_role, $groupblog_default_member_role, $group_id, $groupblog_blog_id = NULL ) {
	global $bp;
	
	if ( empty( $group_id ) )
		return false;

	groups_update_groupmeta ( $group_id, 'groupblog_enable_blog', $groupblog_enable_blog );
	groups_update_groupmeta ( $group_id, 'groupblog_blog_id', $groupblog_blog_id );
	groups_update_groupmeta ( $group_id, 'groupblog_silent_add', $groupblog_silent_add );
	
	groups_update_groupmeta ( $group_id, 'groupblog_default_admin_role', $groupblog_default_admin_role );
	groups_update_groupmeta ( $group_id, 'groupblog_default_mod_role', $groupblog_default_mod_role );
	groups_update_groupmeta ( $group_id, 'groupblog_default_member_role', $groupblog_default_member_role );
	
	do_action( 'groups_details_updated', $group->id );
	
	return true;
}

/**
 * bp_groupblog_create_screen_save()
 *
 * Saves the information from the BP group blog creation step.
 */

function bp_groupblog_create_screen_save() {
	global $bp;
	global $groupblog_blog_id, $groupblog_create_screen;
	
	if ( $bp->action_variables[0] == 'step' ) {
		$groupblog_create_screen = true;
	} else {
		$groupblog_create_screen = false;
	}
	
	$group_id = $_POST['groupblog-group-id'];
	
	if ( isset ($_POST['save'] ) && isset ($_POST['groupblog-create-save']) && isset($_POST['groupblog-enable-blog']) ) {
		if ( $_POST['groupblog-create-new'] == 'yes' ) {
			//Create a new blog and associate it with the group				
			if ( bp_groupblog_validate_blog_signup() ) {
				if ( !groupblog_edit_base_settings( $_POST['groupblog-enable-blog'], $_POST['groupblog-silent-add'], $groupblog_default_admin_role, $groupblog_default_mod_role, $groupblog_default_member_role, $group_id, $groupblog_blog_id ) ) {
					//bp_core_add_message( __( 'There was an error creating your group blog, please try again.' . $group_id, 'groupblog' ), 'error' );
				}
			}
		} else if ( $_POST['groupblog-create-new'] == 'no' ) {
			//User wants to use an existing blog
			if ( $_POST['groupblog-blogid'] != 0 ) {
				//If they have chosen a blog then we're okay
				if ( !groupblog_edit_base_settings( $_POST['groupblog-enable-blog'], $_POST['groupblog-silent-add'], $groupblog_default_admin_role, $groupblog_default_mod_role, $groupblog_default_member_role, $group_id, $_POST['groupblog-blogid'] ) ) {
				}			
			} else {
				//They forgot to choose a blog, so send them back and make them do it!
				bp_core_add_message( __( 'Please choose one of your blogs from the drop-down menu.' . $group_id, 'groupblog' ), 'error' );
				if ( $bp->action_variables[0] == 'step' ) {
					bp_core_redirect( $bp->loggedin_user->domain . $bp->groups->slug . '/create/step/' . $bp->action_variables[1] );
				} else {
					bp_core_redirect( site_url() . '/' . $bp->current_component . '/' . $bp->current_item . '/admin/group-blog' );
				}
			}
		}
	}
}
add_action( 'init', 'bp_groupblog_create_screen_save', 4 );

/**
 * bp_groupblog_show_blog_form( $blogname = '', $blog_title = '', $errors = '' )
 *
 * Displays the blog signup form and takes the privacy settings from the 
 * group privacy settings, where "private & hidden" equal "private".
 */
function bp_groupblog_show_blog_form( $blogname = '', $blog_title = '', $errors = '' ) {
	global $bp, $groupblog_create_screen, $current_site;
	?>
	
	<div id="blog-details-fields">
	<?php $blog_id = get_groupblog_blog_id(); ?>
    <?php if ( !$groupblog_create_screen && !( $blog_id == '' ) ) { ?>
		<?php //We're showing the admin form ?>
		<?php $blog_details = get_blog_details( get_groupblog_blog_id(), true ); ?>
	    <label for="blog_title"><strong><?php _e( 'Blog Title:', 'groupblog' ) ?></strong></label> 
	    <?php if ( $errmsg = $errors->get_error_message('blog_title') ) { ?>
	      <p class="error"><?php echo $errmsg ?></p>
	    <?php } ?>
	    <p><?php echo $blog_details->blogname; ?></p>    
	    <input name="blog_title" type="hidden" id="blog_title" value="<?php echo $blog_details->blogname; ?>" />
    
	    <label for="blogname"><strong><?php _e( 'Blog Address:', 'groupblog' ) ?></strong></label>
	    <?php if ( $errmsg = $errors->get_error_message('blogname') ) { ?>
	      <p class="error"><?php echo $errmsg ?></p>
	    <?php }
	    // Since WordPress does not allow '-' or '_' in a blog name, we cut those out
	    $baddies = array ( '-', '_' );
	    $blog_address = str_replace ( $baddies, '', $bp->groups->current_group->slug );
		?>

		<p><em><?php echo $blog_details->siteurl; ?> </em></p>
		<input name="blogname" type="hidden" id="blogname" value="<?php echo $blog_details->siteurl; ?>" maxlength="50" />

		<?php $bp->groups->current_group->status == 'public' ? $group_public = '1' : $group_public = '0'; ?>
		<input type="hidden" id="blog_public" name="blog_public" value="<?php echo $group_public ?>" />
		<input type="hidden" id="groupblog_create_screen" name="groupblog_create_screen" value="<?php echo $groupblog_create_screen; ?>" />

	<?php } else { ?>
		<?php //Showing the create screen form ?>		
    
		<p><input<?php if ( !bp_groupblog_is_blog_enabled( bp_group_id(false) ) ) { ?> disabled="true"<?php } ?> type="radio" value="no" name="groupblog-create-new" /><span>&nbsp;<?php _e( 'Use one of your own blogs:', 'groupblog' ); ?>&nbsp;</span>
	    <select<?php if ( !bp_groupblog_is_blog_enabled( bp_group_id(false) ) ) { ?> disabled="true"<?php } ?> name="groupblog-blogid" id="groupblog-blogid">
	      <option value="0"><?php _e( 'choose a blog', 'groupblog' ) ?></option>
				  <?php 
				  $user_blogs = get_blogs_of_user( get_current_user_id() );
	        //print_r ($user_blogs);
	          foreach ($user_blogs AS $user_blog) { ?>
	            <option value="<?php echo $user_blog->userblog_id; ?>"><?php echo $user_blog->blogname; ?></option>
	          <?php } ?>
	   	</select>
    </p>
    		
		<p><input<?php if ( !bp_groupblog_is_blog_enabled( bp_group_id(false) ) ) { ?> disabled="true"<?php } ?> type="radio" value="yes" name="groupblog-create-new" checked="checked" /><span>&nbsp;<?php _e( 'Or, create a new blog', 'groupblog' ); ?></span></p>
		
		<ul id="groupblog-details">
		  <li>
				<label class="groupblog-label" for="blog_title"><strong><?php _e( 'Blog Title:', 'groupblog' ) ?></strong></label>	
				<?php if ( $errmsg = $errors->get_error_message('blog_title') ) { ?>
					<span class="error"><?php echo $errmsg ?></span>
				<?php } ?>
				<span><?php echo $bp->groups->current_group->name; ?></span>
				<input name="blog_title" type="hidden" id="blog_title" value="<?php echo $bp->groups->current_group->name; ?>" />
	    </li>
	    
	    <li>
				<label class="groupblog-label" for="blogname"><strong><?php _e( 'Blog Address:', 'groupblog' ) ?></strong></label>
				<?php if ( $errmsg = $errors->get_error_message('blogname') ) { ?>
					<span class="error"><?php echo $errmsg ?></span>
				<?php }
				// Since WordPress does not allow '-' or '_' in a blog name, we cut those out
				$baddies = array ( '-', '_' );
				$blog_address = str_replace ( $baddies, '', $bp->groups->current_group->slug );
		
				/* 
				* If we're re-directing from bp_groupblog_validate_blog_signup(), it means that there was a problem
				* creating the blog either because the name already exists, or it doesn't have enough characters, or
				* because it only contains numbers.
				*
				* We're simply appending the letter 'r' and a random number on to the end of the blogname.
				* The reason we add the 'r' is because WordPress does not allow blognames with only numbers.
				* I.e., if someone creates a group called '42' for some reason.  This is allowed by BuddyPress, but
				* WordPress will send us back an error, since we will be trying to create a blog with the blogname '42'.
				* To-Do: put this in a function?
				*/
				if ( isset ( $_GET['create_error'] ) ) {
					$blog_address .= 'r' . rand();
				}
				?>
		
				<span><em><?php echo 'http://' . $current_site->domain . $current_site->path . $blog_address ?></em></span>
				<input name="blogname" type="hidden" id="blogname" value="<?php echo $blog_address; ?>" maxlength="50" />
			</li>
    </ul>

		<?php $bp->groups->current_group->status == 'public' ? $group_public = '1' : $group_public = '0'; ?>
		<input type="hidden" id="blog_public" name="blog_public" value="<?php echo $group_public ?>" />
		<input type="hidden" id="groupblog_create_screen" name="groupblog_create_screen" value="<?php echo $groupblog_create_screen; ?>" />

	<?php } ?>
		
</div>
<?php
do_action('signup_blogform', $errors);
}

/**
 * bp_groupblog_validate_blog_form()
 *
 * This function validates that the blog does not exist already, illegal names, etc...
 */
function bp_groupblog_validate_blog_form() {

	require_once( ABSPATH . WPINC . '/registration.php' );

	$user = '';
	if ( is_user_logged_in() )
		$user = wp_get_current_user();

	return wpmu_validate_blog_signup($_POST['blogname'], $_POST['blog_title'], $user);
}

/**
 * bp_groupblog_signup_blog($blogname = '', $blog_title = '', $errors = '')
 *
 * This function is called from the template and initiates the blog creation.
 */
function bp_groupblog_signup_blog($blogname = '', $blog_title = '', $errors = '') {
	global $current_user, $current_site, $groupblog_create_screen;
	global $bp;
			
	if ( ! is_wp_error($errors) ) {
		$errors = new WP_Error();
	}

	// allow definition of default variables
	$filtered_results = apply_filters('signup_blog_init', array('blogname' => $blogname, 'blog_title' => $blog_title, 'errors' => $errors ));
	$blogname = $filtered_results['blogname'];
	$blog_title = $filtered_results['blog_title'];
	$errors = $filtered_results['errors'];
		
	if ( !isset ( $groupblog_create_screen ) ) {
		$groupblog_create_screen = false;
	}

  if ( !$groupblog_create_screen ) { ?>
	<h2><?php _e( 'Group Blog', 'groupblog' ) ?></h2>

	<form id="setupform" method="post" action="<?php bp_groupblog_admin_form_action( 'group-blog' ); ?>">
		<input type="hidden" name="stage" value="gimmeanotherblog" />
		<?php do_action( "signup_hidden_fields" ); ?>
	<?php } ?>
			
		<div class="checkbox">
			<label><input type="checkbox" name="groupblog-enable-blog" id="groupblog-enable-blog" value="1"<?php bp_groupblog_show_enabled( bp_group_id(false) ) ?>/> <?php _e( 'Enable group blog', 'groupblog' ); ?></label>	
		</div>
		
		<?php bp_groupblog_show_blog_form($blogname, $blog_title, $errors); ?>
						
		<div id="groupblog-member-options">
		
			<h3><?php _e( 'Member Options', 'groupblog' ) ?></h3>
			
			<p><?php _e( 'Enable blog posting to allow adding of group members to the blog with the roles set below. <br />When disabled, all members will temporarily be set to subscribers, disabling posting.', 'groupblog' ); ?></p>
				
			<div class="checkbox">	
				<label><input<?php if ( !bp_groupblog_is_blog_enabled( bp_group_id(false) ) ) { ?> disabled="true"<?php } ?> type="checkbox" name="groupblog-silent-add" id="groupblog-silent-add" value="1"<?php if ( bp_groupblog_silent_add( bp_group_id(false) ) ) { ?> checked="checked"<?php } ?>/> <?php _e( 'Enable member blog posting', 'groupblog' ); ?></label>
			</div>
		
			<?php
			// Assign our default roles to variables.
			// If nothing has been saved in the groupmeta yet, then we assign our own defalt values.			
			if ( !( $groupblog_default_admin_role = groups_get_groupmeta ( $bp->groups->current_group->id, 'groupblog_default_admin_role' ) ) ) {
				$groupblog_default_admin_role = $bp->groupblog->default_admin_role;
			}
			if ( !( $groupblog_default_mod_role = groups_get_groupmeta ( $bp->groups->current_group->id, 'groupblog_default_mod_role' ) ) ) {
				$groupblog_default_mod_role = $bp->groupblog->default_mod_role;
			}
			if ( !( $groupblog_default_member_role = groups_get_groupmeta ( $bp->groups->current_group->id, 'groupblog_default_member_role' ) ) ) {
				$groupblog_default_member_role = $bp->groupblog->default_member_role;
			}
			?>
		
			<label><strong><?php _e( 'Default Administrator Role:', 'groupblog' ); ?></strong></label>
			<input type="radio"<?php if ( $groupblog_default_admin_role == 'administrator' ) {?> checked="checked"<?php } ?> value="administrator" name="default-administrator"<?php if ( !bp_groupblog_silent_add( bp_group_id(false) ) || !bp_groupblog_is_blog_enabled( bp_group_id(false) ) ) { ?> disabled="true"<?php } ?> /><span>&nbsp;<?php _e( 'Administrator', 'groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio"<?php if ( $groupblog_default_admin_role == 'editor' ) {?> checked="checked"<?php } ?> value="editor" name="default-administrator"<?php if ( !bp_groupblog_silent_add( bp_group_id(false) ) || !bp_groupblog_is_blog_enabled( bp_group_id(false) ) ) { ?> disabled="true"<?php } ?> /><span>&nbsp;<?php _e( 'Editor', 'groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio"<?php if ( $groupblog_default_admin_role == 'author' ) {?> checked="checked"<?php } ?> value="author" name="default-administrator"<?php if ( !bp_groupblog_silent_add( bp_group_id(false) ) || !bp_groupblog_is_blog_enabled( bp_group_id(false) ) ) { ?> disabled="true"<?php } ?> /><span>&nbsp;<?php _e( 'Author', 'groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio"<?php if ( $groupblog_default_admin_role == 'contributor' ) {?> checked="checked"<?php } ?> value="contributor" name="default-administrator"<?php if ( !bp_groupblog_silent_add( bp_group_id(false) ) || !bp_groupblog_is_blog_enabled( bp_group_id(false) ) ) { ?> disabled="true"<?php } ?> /><span>&nbsp;<?php _e( 'Contributor', 'groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio"<?php if ( $groupblog_default_admin_role == 'subscriber' ) {?> checked="checked"<?php } ?> value="subscriber" name="default-administrator"<?php if ( !bp_groupblog_silent_add( bp_group_id(false) ) || !bp_groupblog_is_blog_enabled( bp_group_id(false) ) ) { ?> disabled="true"<?php } ?> /><span>&nbsp;<?php _e( 'Subscriber', 'groupblog' ); ?>&nbsp;&nbsp;</span>
			
			<label><strong><?php _e( 'Default Moderator Role:', 'groupblog' ); ?></strong></label>
			<input type="radio"<?php if ( $groupblog_default_mod_role == 'administrator' ) {?> checked="checked"<?php } ?> value="administrator" name="default-moderator"<?php if ( !bp_groupblog_silent_add( bp_group_id(false) ) || !bp_groupblog_is_blog_enabled( bp_group_id(false) ) ) { ?> disabled="true"<?php } ?> /><span>&nbsp;<?php _e( 'Administrator', 'groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio"<?php if ( $groupblog_default_mod_role == 'editor' ) {?> checked="checked"<?php } ?> value="editor" name="default-moderator"<?php if ( !bp_groupblog_silent_add( bp_group_id(false) ) || !bp_groupblog_is_blog_enabled( bp_group_id(false) ) ) { ?> disabled="true"<?php } ?> /><span>&nbsp;<?php _e( 'Editor', 'groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio"<?php if ( $groupblog_default_mod_role == 'author' ) {?> checked="checked"<?php } ?> value="author" name="default-moderator"<?php if ( !bp_groupblog_silent_add( bp_group_id(false) ) || !bp_groupblog_is_blog_enabled( bp_group_id(false) ) ) { ?> disabled="true"<?php } ?> /><span>&nbsp;<?php _e( 'Author', 'groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio"<?php if ( $groupblog_default_mod_role == 'contributor' ) {?> checked="checked"<?php } ?> value="contributor" name="default-moderator"<?php if ( !bp_groupblog_silent_add( bp_group_id(false) ) || !bp_groupblog_is_blog_enabled( bp_group_id(false) ) ) { ?> disabled="true"<?php } ?> /><span>&nbsp;<?php _e( 'Contributor', 'groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio"<?php if ( $groupblog_default_mod_role == 'subscriber' ) {?> checked="checked"<?php } ?> value="subscriber" name="default-moderator"<?php if ( !bp_groupblog_silent_add( bp_group_id(false) ) || !bp_groupblog_is_blog_enabled( bp_group_id(false) ) ) { ?> disabled="true"<?php } ?> /><span>&nbsp;<?php _e( 'Subscriber', 'groupblog' ); ?>&nbsp;&nbsp;</span>
			
			<label><strong><?php _e( 'Default Member Role:', 'groupblog' ); ?></strong></label>
			<input type="radio"<?php if ( $groupblog_default_member_role == 'administrator' ) {?> checked="checked"<?php } ?> value="administrator" name="default-member"<?php if ( !bp_groupblog_silent_add( bp_group_id(false) ) || !bp_groupblog_is_blog_enabled( bp_group_id(false) ) ) { ?> disabled="true"<?php } ?> /><span>&nbsp;<?php _e( 'Administrator', 'groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio"<?php if ( $groupblog_default_member_role == 'editor' ) {?> checked="checked"<?php } ?> value="editor" name="default-member"<?php if ( !bp_groupblog_silent_add( bp_group_id(false) ) || !bp_groupblog_is_blog_enabled( bp_group_id(false) ) ) { ?> disabled="true"<?php } ?> /><span>&nbsp;<?php _e( 'Editor', 'groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio"<?php if ( $groupblog_default_member_role == 'author' ) {?> checked="checked"<?php } ?> value="author" name="default-member"<?php if ( !bp_groupblog_silent_add( bp_group_id(false) ) || !bp_groupblog_is_blog_enabled( bp_group_id(false) ) ) { ?> disabled="true"<?php } ?> /><span>&nbsp;<?php _e( 'Author', 'groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio"<?php if ( $groupblog_default_member_role == 'contributor' ) {?> checked="checked"<?php } ?> value="contributor" name="default-member"<?php if ( !bp_groupblog_silent_add( bp_group_id(false) ) || !bp_groupblog_is_blog_enabled( bp_group_id(false) ) ) { ?> disabled="true"<?php } ?> /><span>&nbsp;<?php _e( 'Contributor', 'groupblog' ); ?>&nbsp;&nbsp;</span>
			<input type="radio"<?php if ( $groupblog_default_member_role == 'subscriber' ) {?> checked="checked"<?php } ?> value="subscriber" name="default-member"<?php if ( !bp_groupblog_silent_add( bp_group_id(false) ) || !bp_groupblog_is_blog_enabled( bp_group_id(false) ) ) { ?> disabled="true"<?php } ?> /><span>&nbsp;<?php _e( 'Subscriber', 'groupblog' ); ?>&nbsp;&nbsp;</span>
			
			<div id="groupblog-member-roles">
				<label><strong><?php _e( 'A bit about the WPMU member roles:', 'groupblog' ); ?></strong></label>
				<ul id="groupblog-members">
					<li><?php _e( 'Administrator', 'groupblog' ); ?> - <?php _e( "Somebody who has access to all the administration features.", 'groupblog' ); ?></li>
					<li><?php _e( 'Editor', 'groupblog' ); ?> - <?php _e( "Somebody who can publish posts, manage posts as well as manage other people's posts, etc.", 'groupblog' ); ?></li>
					<li><?php _e( 'Author', 'groupblog' ); ?> - <?php _e( "Somebody who can publish and manage their own posts.", 'groupblog' ); ?></li>
					<li><?php _e( 'Contributor', 'groupblog' ); ?> - <?php _e( "Somebody who can write and manage their posts but not publish posts.", 'groupblog' ); ?></li>
					<li><?php _e( 'Subscriber', 'groupblog' ); ?> - <?php _e( "Somebody who can read comments/comment/receive news letters, etc.", 'groupblog' ); ?></li>
				</ul>
			</div>
			
		</div>

		<?php if ( !$groupblog_create_screen ) { ?>
		<p>
			<input id="save" type="submit" name="save" class="submit" value="<?php _e('Save Changes &raquo;', 'groupblog') ?>"/>
		</p>
	</form>
	<?php 
	} 
}

/**
 * bp_groupblog_validate_blog_signup()
 *
 * Final step before the blog gets created it needs to be validated
 */
function bp_groupblog_validate_blog_signup() {
	global $bp, $wpdb, $current_user, $blogname, $blog_title, $errors, $domain, $path;
	global $groupblog_blog_id;

	require_once( ABSPATH . WPINC . '/registration.php' );
	
	$current_user = wp_get_current_user();
	if( !is_user_logged_in() )
		die();
  
  // Re-validate user info.
	$result = bp_groupblog_validate_blog_form();
	extract($result);

	if ( $errors->get_error_code() ) {
		$message .= $errors->get_error_message('blogname');
		$message .= '<br />' . __( 'However, you may continue with the blog address as listed below.', 'groupblog' );
		$message .= '<br />' . __( 'We suggest adjusting the group name in group details following these requirements.', 'groupblog' );
		$message .= '<br />' . __( '- Only letters and numbers allowed.', 'groupblog' );
		$message .= '<br />' . __( '- Must be at least 4 characters.', 'groupblog' );
		$message .= '<br />' . __( '- Has to contain letters as well.', 'groupblog' );
		bp_core_add_message( $message, 'error' );

		//Hello Lost fan!
		if ( $bp->action_variables[0] == 'step' ) {
			bp_core_redirect( $bp->loggedin_user->domain . $bp->groups->slug . '/create/step/' . $bp->action_variables[1] . '/?create_error=4815162342' );
		} else {
			bp_core_redirect( site_url() . '/' . $bp->current_component . '/' . $bp->current_item . '/admin/group-blog/?create_error=4815162342' );
		}
	}

	$public = (int) $_POST['blog_public'];
	$meta = apply_filters('signup_create_blog_meta', array ('lang_id' => 1, 'public' => $public)); // depreciated
	$meta = apply_filters( "add_signup_meta", $meta );

	$groupblog_blog_id = wpmu_create_blog( $domain, $path, $blog_title, $current_user->id, $meta, $wpdb->siteid );
	
	$errors = $filtered_results['errors'];

	return true;
}

/**
 * Silently add a user to a group blog
 *
 * This next section contains functions to silently add a user to a group blog.
 * This code runs every time the site is loaded, but exits out if:
 *		- the group blogging function is not enabled
 * 		- the site being visited is not linked to a group
 *		- the user is not logged in
 *		- the logged in user is not a group member
 *		- the user has already been assigned a role
 *		- the user is an admin
 *
 * Inspired by and borrowing some code from Burt Adsit's plugin Community Blogs
 * Plugin URI: http://wordpress.org/extend/plugins/bp-community-blogs/
 * Author URI: http://buddypress.org/developers/burtadsit/
 */

/**
 * bp_groupblog_get_current_role()
 *
 * Retrieves the current role of the logged in user of the blog being viewed.
 */
function bp_groupblog_get_current_role() {
	global $bp, $blog_id, $current_blog;
	
	$blog_id = $current_blog->blog_id;
	
	// determine users role, if any, on this blog
	$roles = get_usermeta( $bp->loggedin_user->id, 'wp_' . $blog_id . '_capabilities' );
	
	// this seems to be the only way to do this
	if ( isset( $roles['subscriber'] ) ) 
		$user_role = 'subscriber'; 
	elseif	( isset( $roles['contributor'] ) )
		$user_role = 'contributor';
	elseif	( isset( $roles['author'] ) )
		$user_role = 'author';
	elseif ( isset( $roles['editor'] ) )
		$user_role = 'editor';
	elseif ( isset( $roles['administrator'] ) )
		$user_role = 'administrator';
	elseif ( is_site_admin() )
		$user_role = 'siteadmin';	
	else $user_role = 'norole';
	return $user_role;
}

/** 
 * Why do we have to repate these?
 * These are taken from bp-groups-classes.php.
 * For whatever reason we could not call them any other way.
 */
function bp_groupblog_check_is_admin( $user_id, $group_id ) {
	global $wpdb, $bp;
	
	if ( !$user_id )
		return false;
	
	return $wpdb->query( $wpdb->prepare( "SELECT id FROM {$bp->groups->table_name_members} WHERE user_id = %d AND group_id = %d AND is_admin = 1 AND is_banned = 0", $user_id, $group_id ) );
}

function bp_groupblog_check_is_mod( $user_id, $group_id ) {
	global $wpdb, $bp;
	
	if ( !$user_id )
		return false;
			
	return $wpdb->query( $wpdb->prepare( "SELECT id FROM {$bp->groups->table_name_members} WHERE user_id = %d AND group_id = %d AND is_mod = 1 AND is_banned = 0", $user_id, $group_id ) );
}

function bp_groupblog_check_is_member( $user_id, $group_id ) {
	global $wpdb, $bp;
	
	if ( !$user_id )
		return false;
	
	return $wpdb->query( $wpdb->prepare( "SELECT id FROM {$bp->groups->table_name_members} WHERE user_id = %d AND group_id = %d AND is_confirmed = 1 AND is_banned = 0", $user_id, $group_id ) );	
}

/**
 * bp_groupblog_join_this_blog()
 *
 * Set the user role based on the different variables.
 */
function bp_groupblog_join_this_blog() {
  require_once( ABSPATH . WPINC . '/registration.php'); // is this accessable already? dunno

  global $bp, $wpdb, $username, $blog_id, $userdata, $current_blog;

  if  ( groups_get_groupmeta ( $group_id, 'groupblog_enable_blog' ) == '1' )
  	return;

	if (!is_user_logged_in()) // do nothing
		return;
		
	$blog_id = $current_blog->blog_id;
	
	// If the blog being viewed isn't linked to a group, get the heck out of here!
	if ( !( $group_id = bp_groupblog_group_id ( $blog_id ) ) )
		return;
		
	// Setup some variables
	$groupblog_silent_add = groups_get_groupmeta ( $group_id, 'groupblog_silent_add' );
	$groupblog_default_member_role = groups_get_groupmeta ( $group_id, 'groupblog_default_member_role' );
	$groupblog_default_mod_role = groups_get_groupmeta ( $group_id, 'groupblog_default_mod_role' );
	$groupblog_default_admin_role = groups_get_groupmeta ( $group_id, 'groupblog_default_admin_role' );
	$groupblog_creator_role = 'admin';
	
	$group = new BP_Groups_Group ( $group_id );
		
	if ( $group->creator_id == $bp->loggedin_user->id ) {
	  return;
		//$default_role = $groupblog_creator_role;
	} else if ( bp_groupblog_check_is_admin ( $bp->loggedin_user->id, $group_id ) ) {
		$default_role = $groupblog_default_admin_role;
	} else if ( bp_groupblog_check_is_mod ( $bp->loggedin_user->id, $group_id ) ) {
		$default_role = $groupblog_default_mod_role;
	} else if ( bp_groupblog_check_is_member ( $bp->loggedin_user->id, $group_id ) ) {
		$default_role = $groupblog_default_member_role;
	} else {
		return;
	}	

	$user_role = bp_groupblog_get_current_role();
	
	if ($user_role == $default_role && $groupblog_silent_add == true) return false;
	
  if ( !is_user_member_of_blog($bp->loggedin_user->id, $blog_id) && $groupblog_silent_add == true ){
    add_user_to_blog($blog_id, $bp->loggedin_user->id, $default_role);
  }
  else if ( $groupblog_silent_add == true ) {
    $user = new WP_User($bp->loggedin_user->id);
    $user->set_role($default_role);
    wp_cache_delete($bp->loggedin_user->id, 'users' );
  }
  else if ( $groupblog_silent_add != true ) {
    $user = new WP_User($bp->loggedin_user->id);
    $user->set_role('subscriber');
    wp_cache_delete($bp->loggedin_user->id, 'users' );
  }
	// user_id, old role, new role
	do_action('bp_groupblog_upgrade_user',$bp->loggedin_user->id, $user_role, $default_role);
	
}
add_action( 'wp_head', 'bp_groupblog_join_this_blog', 99 );
add_action( 'admin_head', 'bp_groupblog_join_this_blog', 99 );

function bp_groupblog_remove_user( $group_id, $user_id = false ) {
  require_once( ABSPATH . WPINC . '/registration.php'); // is this accessable already? dunno

  global $bp, $wpdb, $username, $blog_id, $userdata, $current_blog;

  if (!is_user_logged_in()) // do nothing
    return;

  if ( !$user_id )
    $user_id = $bp->loggedin_user->id;
		
	$blog_id = get_groupblog_blog_id( $group_id );

  if ( !is_user_member_of_blog($user_id, $blog_id) )
	  return;

  switch_to_blog( $blog_id );
  $user = new WP_User( $user_id );
  $user->set_role('subscriber');
  wp_cache_delete($bp->loggedin_user->id, 'users' );	
}
add_action( 'groups_leave_group', 'bp_groupblog_remove_user' );

/**
 * groupblog_screen_blog()
 *
 * Load the template file to display the group blog contents.
 */
function groupblog_screen_blog() {
	global $bp, $wp;
		
	if ( $bp->current_component == $bp->groups->slug && 'blog' == $bp->current_action ) {
			
		add_action( 'bp_template_content', 'groupblog_screen_blog_content' );
		
		bp_core_load_template( 'plugin-template' );
	}
}

function groupblog_screen_blog_content() {
	global $bp, $wp;
	
  load_template( WP_PLUGIN_DIR . '/bp-groupblog/groupblog/blog.php' );
  
}

/**
 * groupblog_screen_blog_latest()
 *
 * Load the group blog latest on group home page, loaded through a template file.
 */
function groupblog_screen_blog_latest() {
	global $bp, $wp; 
	
	load_template( WP_PLUGIN_DIR . '/bp-groupblog/groupblog/blog-latest.php' );
	
}
add_action ('groups_custom_group_boxes', 'groupblog_screen_blog_latest');

/**
 * bp_groupblog_blog_publish()
 *
 * Allows publishing from the group itself when posts are unpublished.
 */
function groupblog_publish_post() {
	global $bp;
	
	if ( isset( $_GET['publish-post'] ) ) {
	  if ( $bp->is_item_admin || $bp->is_item_mod  ) {	
			$blog_ID = $_GET['blogid'];
			$post_ID = $_GET['postid'];
			switch_to_blog( $blog_ID );
			wp_publish_post( $post_ID );
			
			if ( !(get_post_status( $post_ID ) == 'published' ) ) {
			bp_core_add_message(  __('There was an error publishing the post. Please try again.', 'groupblog'), 'error' );
			bp_core_redirect( site_url() . '/' . $bp->groups->slug . '/' . $bp->current_item . '/group-blog' );
			} else {
			bp_core_add_message( __('The post was successfully published.', 'groupblog') );
			bp_core_redirect( site_url() . '/' . $bp->groups->slug . '/' . $bp->current_item . '/group-blog' );
			}
		}
	}
}
add_action( 'wp', 'groupblog_publish_post', 1 );

?>