
<?php get_header() ?>

<?php if ( bp_has_groups( 'type=single-group&slug=' . bp_get_groupblog_slug() ) ) : while ( bp_groups() ) : bp_the_group(); ?>

	<?php locate_template( array( 'userbar.php' ), true ) /* Load the user navigation */ ?>
	<?php load_template( STYLESHEETPATH . '/optionsbar.php' ) /* Load the currently displayed object navigation */ ?>

	<div class="content-header">

	</div>
	
	<div id="content">
		
		<?php do_action( 'template_notices' ) // (error/success feedback) ?>

		<?php do_action( 'bp_before_group_content' ) ?>

		<div class="left-menu">
			<?php locate_template( array( 'groups/single/menu.php' ), true ) ?>
			<?php get_sidebar(); ?>
		</div>

		<div class="main-column">
			<div class="inner-tube">

				<?php do_action( 'bp_before_group_name' ) ?>
		
				<div id="group-name">
					<h1><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></h1>
					<p class="status"><?php bp_group_type() ?></p>
				</div>
				
				<?php do_action( 'bp_after_group_name' ) ?>
				
				<?php if ( !bp_group_is_visible() ) : ?>
					
					<?php do_action( 'bp_before_group_status_message' ) ?>
					
					<div id="message" class="info">
						<p><?php bp_group_status_message() ?></p>
					</div>
					
					<?php do_action( 'bp_after_group_status_message' ) ?>
					
				<?php endif; ?>

				<?php if ( bp_groupblog_is_blog_enabled( bp_group_id(false) ) && bp_group_is_visible() ) : ?>
				
					<?php do_action( 'bp_before_attachment' ) ?>
					
					<div class="bp-widget page" id="attachments-page">
					
						<h4><?php _e( 'Blog', 'groupblog' ) ?></h4>
			  		
							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
								<?php do_action( 'bp_before_blog_post' ) ?>
			
								<?php $attachment_link = get_the_attachment_link($post->ID, true, array(450, 800)); // This also populates the iconsize for the next line ?>
								<?php $_post = &get_post($post->ID); $classname = ($_post->iconsize[0] <= 128 ? 'small' : '') . 'attachment'; // This lets us style narrow icons specially ?>
							
								<div class="post" id="post-<?php the_ID(); ?>">
							
									<h2><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php echo get_the_title($post->post_parent); ?></a> &raquo; <a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h2>
							
									<div class="entry">
										<p class="<?php echo $classname; ?>"><?php echo $attachment_link; ?><br /><?php echo basename($post->guid); ?></p>
			
										<?php the_content( __('<p class="serif">Read the rest of this entry &raquo;</p>', 'groupblog' ) ); ?>
			
										<?php wp_link_pages( array( 'before' => __( '<p><strong>Pages:</strong> ', 'groupblog' ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
									</div>
								
								</div>
			
								<?php do_action( 'bp_after_blog_post' ) ?>
			
							<?php comments_template(); ?>
			
							<?php endwhile; else: ?>
			
								<p><?php _e( 'Sorry, no attachments matched your criteria.', 'groupblog' ) ?></p>
			
							<?php endif; ?>
					</div>
			
					<?php do_action( 'bp_after_attachment' ) ?>
				
				<?php endif; ?>
				
			</div>
			
		</div>

		<?php do_action( 'bp_after_group_content' ) ?>

	</div>

<?php endwhile; endif; ?>

<?php get_footer() ?>