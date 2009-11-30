<?php
/**
 * Acknowledgement:
 * Deanna Schneider's cets_blog_defaults plugin was a great help
 * in developing this feature for bp-groupblog. Her plugin can be
 * found at http://wpmudev.org/project/New-Blog-Defaults.
 */
 
function bp_groupblog_blog_defaults( $blog_id ) {
	global $bp, $wp_rewrite;
			
	switch_to_blog( $blog_id );

	// only apply defaults to groupblog blogs
	if ( bp_is_groups_component() ) {
		
		// get the site options 
		$options = get_site_option( 'bp_groupblog_blog_defaults_options' );
		
		foreach( $options as $key => $value )
			update_option( $key, $value );	
	
		// override default themes
		if ( ! empty( $options['theme'] ) ) {
			// we want something other than the default theme
			$values = explode( "|", $options['theme'] );
			switch_theme( $values[0], $values[1] );	
		}

		// groupblog bonus options
		if ( strlen( $options['default_cat_name'] ) > 0 ) {
			global $wpdb;
			$cat = $options['default_cat_name'];
			$slug = str_replace( ' ', '-', strtolower( $cat ) ); 
			$results = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->terms SET name = %s, slug = %s  WHERE term_id = 1", $cat, $slug ) );				
		}
		if ( strlen( $options['default_link_cat'] ) > 0 ) {
			global $wpdb;
			$cat = $options['default_link_cat'];
			$slug = str_replace( ' ', '-', strtolower( $cat ) ); 
			$results = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->terms SET name = %s, slug = %s  WHERE term_id = 2", $cat, $slug ) );	
		}
		if ( isset( $options['delete_first_post'] ) && $options['delete_first_post'] == 1 ) {
			global $wpdb;
			$statement = "UPDATE $wpdb->posts SET post_status = 'draft'  WHERE id = 1";	
			$results = $wpdb->query( $statement );
		}		
		if ( isset( $options['delete_first_comment'] ) && $options['delete_first_comment'] == 1 ) {
			wp_delete_comment( 1 );
		}
		if ( $options['delete_blogroll_links'] == 1 ) {
		 	wp_delete_link( 1 ); //delete Wordpress.com blogroll link
    	wp_delete_link( 2 ); //delete Wordpress.org blogroll link
		}
		
	}
	
	restore_current_blog();			
}

function bp_groupblog_update_defaults() {
	
	// create an array to hold the chosen options
	$newoptions = array();
	$newoptions['theme'] = $_POST['theme'];
	
	// groupblog bonus options
	$newoptions['default_cat_name'] = $_POST['default_cat_name'];
	$newoptions['default_link_cat'] = $_POST['default_link_cat'];
	if ( $_POST['delete_first_post'] == 1 )
		$newoptions['delete_first_post'] = 1;
	else
		$newoptions['delete_first_post'] = 0;
	if ( $_POST['delete_first_comment'] == 1 )
		$newoptions['delete_first_comment'] = 1;
	else
		$newoptions['delete_first_comment'] = 0;
	if ( $_POST['delete_blogroll_links'] == 1 )
		$newoptions['delete_blogroll_links'] = 1;
	else
		$newoptions['delete_blogroll_links'] = 0;
	
	// override the site option
	update_site_option ('bp_groupblog_blog_defaults_options', $newoptions); 
		
	$options = get_site_option('bp_groupblog_blog_defaults_options');				
}
		
function bp_groupblog_management_page() {
	global $wpdb;
	
	// only allow site admins to come here.
	if( is_site_admin() == false )
		wp_die( __( 'You do not have permission to access this page.', 'groupblog' ) );
		
	// process form submission    	
  if ( $_POST['action'] == 'update' ) {
		bp_groupblog_update_defaults();
		$updated = true;
  }

	// make sure we're using latest data
	$opt = get_site_option( 'bp_groupblog_blog_defaults_options' );
	?>
			
	<?php if ($updated) { ?>
  	<div id="message" class="updated fade">
  		<p><?php _e( 'Options saved.', 'groupblog' ) ?></p>
  	</div>
  <?php	} ?>
        	
	<div class="wrap" style="position: relative">
		<h2><?php _e( 'Groupblog Settings', 'groupblog' ) ?></h2>

		<form name="blogdefaultsform" action="" method="post">
		
			<?php 
			$themes = get_themes();
			$ct = current_theme_info();
	
			$allowed_themes = get_site_allowed_themes();
			if( $allowed_themes == false )
				$allowed_themes = array();
		
			$blog_allowed_themes = wpmu_get_blog_allowedthemes();
			if( is_array( $blog_allowed_themes ) )
				$allowed_themes = array_merge( $allowed_themes, $blog_allowed_themes );
	
			if( $blog_id != 1 )
				unset( $allowed_themes[ 'h3' ] );
			
			if( isset( $allowed_themes[ wp_specialchars( $ct->stylesheet ) ] ) == false )
				$allowed_themes[ wp_specialchars( $ct->stylesheet ) ] = true;
			
			reset( $themes );
			foreach( $themes as $key => $theme ) {
				if( isset( $allowed_themes[ wp_specialchars( $theme[ 'Stylesheet' ] ) ] ) == false ) {
					unset( $themes[ $key ] );
				}
			}
			reset( $themes );
			
			// get the names of the themes & sort them
			$theme_names = array_keys( $themes );
			natcasesort( $theme_names );
			?>
	
			<h3><?php _e( 'Default Theme', 'groupblog' ) ?></h3>
			<table class="form-table">
		  	<tr valign="top">
			  	<th><?php _e( 'Select the default theme:', 'groupblog' ) ?></th>
			    <td>
						<select name="theme" size="1">
		       	
		       	<?php
						foreach ( $theme_names as $theme_name ) {
							$template = $themes[$theme_name]['Template'];
							$stylesheet = $themes[$theme_name]['Stylesheet'];
							$title = $themes[$theme_name]['Title'];
							$selected = "";
							if( $opt[theme] == $template . "|" . $stylesheet ) {
								$selected = "selected = 'selected' ";
							}
							echo('<option value="' . $template . "|" . $stylesheet .  '"' . $selected . '>' . $title . "</option>");
						}
						?>
	
						</select>
	       	</td>
	      </tr>
	    </table>

      <h3><?php _e( 'Bonus Options', 'groupblog' ) ?></h3>
			<table class="form-table">
				<tr valign="top">
	        <th><?php _e( 'Default Post Category:', 'groupblog' ) ?><br /> <?php _e( '(Overwrites "Uncategorized")', 'groupblog' ) ?></th>
					<td>
						<input name="default_cat_name" type="text" id="default_cat_name" size="30" value="<?php echo($opt['default_cat_name']); ?>"  />
					</td>
				</tr>
				<tr valign="top">
		    	<th><?php _e( 'Default Link Category:', 'groupblog' ) ?><br /> <?php _e( '(Overwrites "Blogroll")', 'groupblog' ) ?></th>
					<td>
						<input name="default_link_cat" type="text" id="default_link_cat" size="30" value="<?php echo($opt['default_link_cat']); ?>"  />
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Set First Post to Draft', 'groupblog' ) ?><br /> <?php _e( '(Default Post "Hello World")', 'groupblog' ) ?></th>
					<td>
						<label for="delete_first_post">
		       		<input name="delete_first_post" type="checkbox" id="delete_first_post" value="1" <?php if ($opt['delete_first_post'] == 1) echo('checked="checked"'); ?> /> <?php _e( 'Yes', 'groupblog' ) ?>
		       	</label>
					</td>
				</tr>
		    <tr>
					<th><?php _e( 'Delete Initial Comment', 'groupblog' ) ?></th>
					<td>
						<label for="delete_first_comment">
			        <input name="delete_first_comment" type="checkbox" id="delete_first_comment" value="1" <?php if ($opt['delete_first_comment'] == 1) echo('checked="checked"'); ?> /> <?php _e( 'Yes', 'groupblog' ) ?>
			       </label>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Delete Blogroll Links', 'groupblog' ) ?></th>
					<td>
						<label for="delete_blogroll_links">
							<input name="delete_blogroll_links" type="checkbox" id="delete_blogroll_links" value="1" <?php if ($opt['delete_blogroll_links'] == 1) echo('checked="checked"'); ?> /> <?php _e( 'Yes', 'groupblog' ) ?>
						</label>
					</td>
				</tr>
			</table>
	    
	    <p class="submit">  
	    	<input type="hidden" name="action" value="update" />
	      <input type="submit" name="Submit" value="<?php _e( 'Save Changes', 'groupblog' ) ?>" />
	    </p>
	    
 		</form>
 		         
	</div>

<?php
}

// When a new blog is created, set the options 
add_action( 'wpmu_new_blog', 'bp_groupblog_blog_defaults' );
?>