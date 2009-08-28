<?php
global $bp, $current_user;
	
if ( bp_group_is_visible() && bp_groupblog_is_blog_enabled ( bp_group_id(false) ) ) : ?>
   
<div class="bp-widget">
	<h4><?php _e( 'Blog Posts', 'groupblog' ); ?> <span><a href="<?php bp_group_permalink(); ?>/blog"><?php _e( 'See All &rarr;', 'groupblog' ); ?></a></span></h4>

	<?php switch_to_blog( get_groupblog_blog_id() ); ?>
  <?php query_posts( 'showposts=5' );	?>

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

<div class="bp-widget">		
  	
  <?php	get_currentuserinfo(); ?>
	
	<?php	
	if ( ( is_user_member_of_blog($current_user->id) && ($current_user->user_level > 4) ) ) {
 		query_posts( 'post_status=pending' );
		if ( have_posts() ) {
			$count_posts = wp_count_posts();
			$pending_posts = $count_posts->pending;
	?>

	<h4><?php _e( 'Pending Posts', 'groupblog' ); ?> (<?php echo $pending_posts; ?>)</h4>
  
		<ul id="groupblog-pending-post-list" class="item-list">
		<?php	while ( have_posts() ) : the_post(); ?>
			<li>			
				<div class="blog-post-metadata">
					<?php echo bp_core_get_avatar ( $post_author, 1 ); ?>
				  <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
					<?php the_time('F j, Y ') ?>in <?php the_category(', '); ?><?php echo ' by ' . bp_core_get_userlink($post_author) ?>
          [<?php edit_post_link('Edit'); ?>]
          </div>
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
		
	<?php endif;?>
	
	<?php restore_current_blog(); ?>
		
</div>

<?php endif; ?> 
