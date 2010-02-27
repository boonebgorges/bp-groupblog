<?php do_action( 'bp_before_group_blog_content' ) ?>

	<?php if ( bp_group_is_visible() && bp_groupblog_is_blog_enabled ( bp_get_group_id() ) ) : ?>

		<?php switch_to_blog( get_groupblog_blog_id() ); ?>			  
	
		<?php 
			if ( file_exists( locate_template( array( 'groupblog/pages.php' ) ) ) )
    		locate_template( array( 'groupblog/pages.php' ), true );
  		else
    		load_template( WP_PLUGIN_DIR . '/bp-groupblog/groupblog/pages.php' ); 
    		
			if ( file_exists( locate_template( array( 'groupblog/posts.php' ) ) ) )
    		locate_template( array( 'groupblog/posts.php' ), true );
  		else
    		load_template( WP_PLUGIN_DIR . '/bp-groupblog/groupblog/posts.php' );
    		
    	restore_current_blog();
    	
 			if ( file_exists( locate_template( array( 'groupblog/activity.php' ) ) ) )
    		locate_template( array( 'groupblog/activity.php' ), true );
  		else
    		load_template( WP_PLUGIN_DIR . '/bp-groupblog/groupblog/activity.php' );   	
    ?>

	<?php elseif ( !bp_group_is_visible() ) : ?>
		<?php /* The group is not visible, show the status message */ ?>

		<?php do_action( 'bp_before_group_status_message' ) ?>

		<div id="message" class="info">
			<p><?php bp_group_status_message() ?></p>
		</div>

		<?php do_action( 'bp_after_group_status_message' ) ?>
									
	<?php endif;?>
			
<?php do_action( 'bp_after_group_blog_content' ) ?>			