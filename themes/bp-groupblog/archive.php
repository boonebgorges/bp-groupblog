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
				
					<?php do_action( 'bp_before_archive' ) ?>
					
					<div class="bp-widget page" id="blog-archives">
					
						<h4><?php _e( 'Blog', 'groupblog' ) ?></h4>
						
						<?php if ( have_posts() ) : ?>
			
							<h3><?php printf( __( 'You are browsing the archive for %1$s.', 'groupblog' ), wp_title( false, false ) ); ?></h3>
			
							<div class="navigation">
								
								<div class="alignleft"><?php next_posts_link( __( '&laquo; Previous Entries', 'groupblog' ) ) ?></div>
								<div class="alignright"><?php previous_posts_link( __( 'Next Entries &raquo;', 'groupblog' ) ) ?></div>
							
							</div>
			
							<?php while (have_posts()) : the_post(); ?>
			
								<?php do_action( 'bp_before_blog_post' ) ?>
							
								<div class="post">
									
									<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'groupblog' ) ?> <?php the_title(); ?>"><?php the_title(); ?></a></h3>
									<small><?php the_time('F j, Y') ?></small>
			
									<div class="entry">
										<?php the_content() ?>
									</div>
			
									<p class="postmetadata"><?php _e( 'Posted in', 'groupblog' ) ?> <?php the_category(', ') ?> | <?php edit_post_link( __( 'Edit', 'groupblog' ), '', ' | '); ?>  <?php comments_popup_link( __( 'No Comments &#187;', 'groupblog' ), __( '1 Comment &#187;', 'groupblog' ), __( '% Comments &#187;', 'groupblog' ) ); ?></p>
			
								</div>
			
								<?php do_action( 'bp_after_blog_post' ) ?>
			
							<?php endwhile; ?>
			
							<div class="navigation">
								
								<div class="alignleft"><?php next_posts_link( __( '&laquo; Previous Entries', 'groupblog' ) ) ?></div>
								<div class="alignright"><?php previous_posts_link( __( 'Next Entries &raquo;', 'groupblog' ) ) ?></div>
							
							</div>
			
						<?php else : ?>
			
							<h2 class="center"><?php _e( 'Not Found', 'groupblog' ) ?></h2>
							<?php locate_template( array( 'searchform.php' ), true ) ?>
						
						<?php endif; ?>
			
					</div>
			
					<?php do_action( 'bp_after_archive' ) ?>
				
				<?php endif; ?>
				
			</div>
			
		</div>

		<?php do_action( 'bp_after_group_content' ) ?>

	</div>

<?php endwhile; endif; ?>

<?php get_footer() ?>