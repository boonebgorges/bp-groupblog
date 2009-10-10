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
				
					<?php do_action( 'bp_before_blog_page' ) ?>
				
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
						<div class="bp-widget" id="blog-page">					
							<h4><?php the_title(); ?></h4>
			
							<div class="post" id="post-<?php the_ID(); ?>">
								
								<div class="entry">
									
									<?php the_content( __( '<p class="serif">Read the rest of this page &raquo;</p>', 'groupblog' ) ); ?>
			
									<?php wp_link_pages( array( 'before' => __( '<p><strong>Pages:</strong> ', 'groupblog' ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
									<?php edit_post_link( __( 'Edit this entry.', 'groupblog' ), '<p>', '</p>'); ?>
									
								</div>
								
							</div>						
						</div>
						
					<?php endwhile; endif; ?>
					
					<?php do_action( 'bp_after_blog_page' ) ?>
				
				<?php endif; ?>
				
			</div>
			
		</div>

		<?php do_action( 'bp_after_group_content' ) ?>

	</div>

<?php endwhile; endif; ?>

<?php get_footer() ?>