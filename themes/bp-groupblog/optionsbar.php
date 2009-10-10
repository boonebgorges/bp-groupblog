<?php do_action( 'bp_before_options_bar' ) ?>

<div id="optionsbar">
	
	<h3><?php bp_group_name() ?></h3>

	<?php do_action( 'bp_inside_before_options_bar' ) ?>	

	<p class="avatar">
		<?php bp_group_avatar( 'type=thumb' ); ?>
	</p>
		
	<ul id="options-nav">
		<li id="li-subnav-group-home"><a id="group-home" href="<?php bp_group_permalink() ?>/home"><?php _e( 'Home', 'groupblog' ) ?></a></li>
		<?php if ( bp_group_is_admin() || bp_group_is_mod() ) : ?>
  		<li id="li-subnav-group-admin"><a id="group-admin" href="<?php bp_group_permalink() ?>/admin"><?php _e( 'Admin', 'groupblog' ) ?></a></li>
    <?php endif; ?>		
		<li id="li-subnav-group-blog" class="current"><a id="group-blog" href="<?php bp_group_permalink() ?>/blog"><?php _e( 'Blog', 'groupblog' ) ?></a></li>
		<?php if ( bp_group_is_forum_enabled() && function_exists( 'bp_forums_setup') ) : ?>
			<li id="li-subnav-group-forum"><a id="group-forum" href="<?php bp_group_permalink() ?>/forum"><?php _e( 'Forum', 'groupblog' ) ?></a></li>
		<?php endif; ?>
		<?php if ( bp_group_is_wire_enabled() && function_exists( 'bp_wire_get_post_list') ) : ?>
			<li id="li-subnav-group-wire"><a id="group-wire" href="<?php bp_group_permalink() ?>/wire"><?php _e( 'Wire', 'groupblog' ) ?></a></li>
		<?php endif; ?>
		<li id="li-subnav-group-members"><a id="group-members" href="<?php bp_group_permalink() ?>/members"><?php _e( 'Members', 'groupblog' ) ?></a></li>
		<?php if ( bp_group_is_member() ) : ?>
			<li id="li-subnav-group-invite"><a id="group-invite" href="<?php bp_group_permalink() ?>/send-invites"><?php _e( 'Send Invites', 'groupblog' ) ?></a></li>
		<?php endif; ?>
	</ul>
	
	<?php do_action( 'bp_inside_after_options_bar' ) ?>

</div>

<?php do_action( 'bp_after_options_bar' ) ?>