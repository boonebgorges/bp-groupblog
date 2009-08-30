<?php

/**
 * bp_groupblog_add_js()
 */
function bp_groupblog_add_js() {
  global $bp;
  
	if ( $bp->current_component == $bp->groups->slug && ( ('group-blog' == $bp->action_variables[0]) || ('group-blog' == $bp->action_variables[1]) ) )
	  if ( file_exists( STYLESHEETPATH . '/groupblog/js/general.js' ) )
			wp_enqueue_script( 'bp-groupblog-js', STYLESHEETPATH . '/groupblog/js/general.js' );
		else
		  wp_enqueue_script( 'bp-groupblog-js', WP_PLUGIN_URL . '/bp-groupblog/groupblog/js/general.js' );
}
add_action( 'template_redirect', 'bp_groupblog_add_js', 1 );

/**
 * bp_groupblog_add_screen_css()
 */
function bp_groupblog_add_screen_css() {

  if ( file_exists( STYLESHEETPATH . '/groupblog/css/screen.css' ) )
  	wp_enqueue_style( 'bp-groupblog-screen', STYLESHEETPATH . '/groupblog/css/screen.css' );
  	  else
  	wp_enqueue_style( 'bp-groupblog-screen', WP_PLUGIN_URL . '/bp-groupblog/groupblog/css/screen.css' );
}
add_action( 'wp_print_styles', 'bp_groupblog_add_screen_css' );

?>