<?php

/**
 * 1.5 functions
 */

if ( !function_exists( 'bp_core_admin_hook' ) ) :
	function bp_core_admin_hook() {
		// Groupblog requires multisite, so we don't need any more logic
		return apply_filters( 'bp_core_admin_hook', 'network_admin_menu' );
	}
endif;

if ( !function_exists( 'bp_is_action_variable' ) ) :
	function bp_is_action_variable( $value, $position = false ) {
		global $bp;

		if ( false === $position ) {
			$is_action_variable = !empty( $bp->action_variables ) && in_array( $value, $bp->action_variables );
		} else {
			$is_action_variable = !empty( $bp->action_variables ) && isset( $bp->action_variables[$position] ) && $value == $bp->action_variables[$position];
		}

		return apply_filters( 'bp_is_action_variable', $is_action_variable );
	}
endif;

if ( !function_exists( 'bp_is_current_action' ) ) :
	function bp_is_current_action( $action ) {
		global $bp;

		return apply_filters( 'bp_is_current_action', $action == $bp->current_action );
	}
endif;

if ( !function_exists( 'bp_is_groups_component' ) ) :
	function bp_is_groups_component() {
		global $bp;

		return apply_filters( 'bp_is_groups_component', $bp->groups->slug == $bp->current_component );
	}
endif;

if ( !function_exists( 'bp_get_current_group_id' ) ) :
	function bp_get_current_group_id() {
		global $bp;

		return apply_filters( 'bp_get_current_group_id', isset( $bp->groups->current_group->id ) ? $bp->groups->current_group->id : 0 );
	}
endif;

if ( !function_exists( 'bp_is_user' ) ) :
	function bp_is_user() {
		return bp_is_member();
	}
endif;

?>