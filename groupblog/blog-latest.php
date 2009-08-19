<?php
global $bp, $current_user;
	
if ( bp_group_is_visible() && bp_groupblog_is_blog_enabled ( $bp->groups->current_group->id ) ) : ?>

	<?php switch_to_blog( groups_get_groupmeta( $bp->groups->current_group->id, 'groupblog_blog_id' ) ); ?>
	<?php query_posts( 'showposts=5' );	?>
	     
	<div class="info-group">
		<h4><?php _e( 'Blog Posts', 'groupblog' ); ?> <span><a href="<?php echo $bp->root_domain . '/' . $bp->groups->slug . '/' . $bp->groups->current_group->slug ?>/blog"><?php _e( 'See All &rarr;', 'groupblog' ); ?></a></span></h4>
	
		<?php if ( have_posts() ) : ?>
		
			<ul id="groupblog-post-list" class="item-list">	
			<?php while ( have_posts() ) : the_post(); $post_author = get_the_author_id(); ?>
	    	<li>
					<div class="blog-post-metadata">
						<?php echo bp_core_get_avatar ( $post_author, 1 ); ?>
					  <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
		        <?php the_time('F j, Y ') ?>in <?php the_category(', '); ?><?php echo ' by ' . bp_core_get_userlink($post_author) ?>
	          <?php edit_post_link('[Edit]', '<span>', '</span>'); ?>
					</div>
		      
		      <div class="blog-post-content">
		        <?php the_excerpt() ?>
		      </div>
		      
					<p class="blog-post-footer">
						<?php if ( the_tags() ) : ?>
							<?php _e( 'Tags: ', 'buddypress' ); ?><?php the_tags( '<span class="tags">', ', ', '</span>' ); ?>
						<?php endif; ?>
						<span class="comments">
							<a href="<?php the_permalink(); ?>#comments"><?php comments_number( __('No Comments'), __('1 Comment'), __('% Comments') ); ?> &raquo;</a>
						</span>
					</p>				
				</li>
			<?php endwhile;?>
			</ul>
	</div>
	  	
	<?php	get_currentuserinfo(); ?>
	
	<?php	
	if ( ( is_user_member_of_blog($current_user->id) && ($current_user->user_level > 4) ) ) {
			query_posts( 'post_status=pending' );
		if ( have_posts() ) {
			$count_posts = wp_count_posts();
			$pending_posts = $count_posts->pending;
	?>
	
		<div class="info-group">
		
			<h4><?php _e( 'Pending Posts', 'groupblog' ); ?> (<?php echo $pending_posts; ?>)</h4>
		  
				<ul id="groupblog-pending-post-list" class="item-list">
				<?php	while ( have_posts() ) : the_post(); ?>
					<li>			
						<div class="blog-post-metadata">
							<?php echo bp_core_get_avatar ( $post_author, 1 ); ?>
						  <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
							<?php the_time('F j, Y ') ?>in <?php the_category(', '); ?><?php echo ' by ' . bp_core_get_userlink($post_author) ?>
		          <?php edit_post_link('[Edit]'); ?>
						</div>
		                
						<div class="pending-posts-admin">
		       	 <span><?php edit_post_link('edit'); ?></span>
		          	<?php
									$publish_link = site_url() . '/' . $bp->current_component . '/' . $bp->current_item . '/publish-post/';
									$publish_link .= bp_groupblog_group_blog_id( bp_group_id(false) ) . '/';
									$publish_link .= get_the_ID();
								?>
		          	<span>| <a href="<?php echo $publish_link; ?>"><?php _e( 'publish', 'groupblog' ); ?></a></span>
		     	  </div>
		     	 
					</li>
				<?php endwhile; ?>
				</ul>
				<?php } ?>		
			<?php } ?>
			
			<?php else: ?>
			
				<div id="message" class="info">
					<p><?php _e( 'No posts have been made yet to this group blog.', 'buddypress' ); ?></p>
				</div>
		
		</div>
			
	<?php endif;?>
		
	<?php restore_current_blog(); ?>

<?php endif; ?> 
