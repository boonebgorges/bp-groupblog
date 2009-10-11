<?php
	
if ( bp_group_is_visible() && bp_groupblog_is_blog_enabled ( bp_group_id(false) ) ) : ?>

	<?php switch_to_blog( get_groupblog_blog_id() ); ?>
		   
	<div class="bp-widget">
	
		<h4><?php _e( 'Blog Posts', 'groupblog' ); ?> <span><a href="<?php bp_group_permalink(); ?>/blog"><?php _e( 'See All &rarr;', 'groupblog' ); ?></a></span></h4>
	
	  <?php query_posts( 'showposts=5' );	?>
		<?php if ( have_posts() ) : ?>
		
			<ul id="groupblog-post-list" class="item-list">
			
			<?php while ( have_posts() ) : the_post(); ?>
			
	    	<li>
					<div class="blog-post-metadata">
						<?php echo bp_core_get_avatar ( $post->post_author, 1 ); ?>
					  <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
		        <?php the_time('F j, Y') ?> <?php _e( 'in', 'groupblog' ) ?> <?php the_category(', '); ?> <?php printf( __( 'by %s', 'groupblog' ), bp_core_get_userlink( $post->post_author ) ) ?> <?php edit_post_link('[Edit]', '<span>', '</span>'); ?>
					</div>
		      
		      <div class="blog-post-content">
		        <?php the_excerpt() ?>
		      </div>
		      
					<p class="blog-post-footer">
						<?php if ( the_tags() ) : ?>
							<?php _e( 'Tags:', 'groupblog' ); ?> <?php the_tags( '<span class="tags">', ', ', '</span>' ); ?>
						<?php endif; ?>
						<span class="comments">
							<a href="<?php the_permalink(); ?>#comments"><?php comments_number( __( 'No Comments', 'groupblog' ), __( '1 Comment', 'groupblog' ), __( '% Comments', 'groupblog' ) ); ?> &raquo;</a>
						</span>
					</p>				
				</li>
				
			<?php endwhile;?>
			
			</ul>
			
		<?php endif; ?>
			
	</div>
	
	<?php restore_current_blog(); ?>
		
<?php endif; ?> 