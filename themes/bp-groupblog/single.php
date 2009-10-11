<?php get_header() ?>

<?php if ( bp_has_groups( 'type=single-group&slug=' . bp_get_groupblog_slug() ) ) : while ( bp_groups() ) : bp_the_group(); ?>

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
				
					<?php do_action( 'bp_before_blog_single_post' ) ?>
					
					<div class="bp-widget" id="blog-single">					
						<h4><?php _e( 'Blog', 'groupblog' ) ?></h4>
	
						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
							<div class="item-options">
							
								<div class="alignleft"><?php next_posts_link( __( '&laquo; Previous Entries', 'groupblog' ) ) ?></div>
								<div class="alignright"><?php previous_posts_link( __( 'Next Entries &raquo;', 'groupblog' ) ) ?></div>
							
							</div>
											
							<div class="post" id="post-<?php the_ID(); ?>">
								
								<?php do_action( 'bp_before_blog_post' ) ?>
						
								<h3><a href="<?php echo get_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent link to', 'groupblog' ) ?> <?php the_title(); ?>"><?php the_title(); ?></a></h3>
			
								<div class="entry">
									
									<?php the_content( __( '<p class="serif">Read the rest of this entry &raquo;</p>', 'groupblog' ) ); ?>
			
									<?php wp_link_pages(array('before' => __( '<p><strong>Pages:</strong> ', 'groupblog' ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
			
								</div>
			
								<?php do_action( 'bp_after_blog_post' ) ?>
								
							</div>						
						
							<?php comments_template(); ?>
				
						<?php endwhile; else: ?>
			
							<p><?php _e( 'Sorry, no posts matched your criteria.', 'groupblog' ) ?></p>
			
						<?php endif; ?>
			
					</div>
							
					<?php do_action( 'bp_after_blog_single_post' ) ?>
				
				<?php endif; ?>
				
			</div>
			
		</div>

		<?php do_action( 'bp_after_group_content' ) ?>

	</div>

<?php endwhile; endif; ?>

<?php get_footer() ?>