<?php 
/*
Template Name: Automagic Sitemap

will automatically generate a sitemap using all pages, posts, and categories on your site
*/
 ?>

<?php get_header(); ?>
    
    <div id="content">
	<?php 
	//THE LOOP.
	if( have_posts() ): 
		while( have_posts() ):
		the_post(); ?>
	
        <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
            <h2 class="entry-title"> <a href="<?php the_permalink(); ?>"> 
				<?php the_title(); ?> 
			</a></h2>
            
            <div class="entry-content">
                <div class="onethird">
                	<h3>Pages:</h3>
                	<ul>
                	<?php wp_list_pages( array(
                		'title_li' => '',
                		) ); ?>
                	</ul>
                </div>
                <div class="onethird">
                	<h3>Blog Posts:</h3>
                	<ul>
                	<?php wp_get_archives( array(
                		'type' => 'alpha', // each post, in alpha order by title
                	) ); ?>	
                	</ul>	

                </div>
                <div class="onethird">
                	<h3>RSS Feeds</h3>
                	<ul>
                		<li><a href=" <?php bloginfo('rss2_url'); ?> ">Blog Posts Feed</a></li>
                		<li><a href=" <?php bloginfo('comments_rss2_url'); ?>">Comments Feed</a></li>
                	</ul>
                	<h3>Blog Categories:</h3>
                	<ul>
                	<?php wp_list_categories( array(
                		'title_li' => '', // instead of an image for the feed, use the 'feed' => 'rss'
                		'feed_image' => get_bloginfo( 'template_directory' ) . '/images/icon_feed.png', // the bloginfo echos, so using get_bloginfo doesn't	
                	) );?>	
                	</ul>	
                </div>
            </div>
       
        
		<?php comments_template(); ?>
		 </article><!-- end post -->
      <?php 
	  endwhile;
	  else: ?>

	  <h2>Sorry, no posts found</h2>

	  <?php endif; //END OF LOOP. ?>
	          
       
    </div><!-- end content -->
    
<?php get_footer(); ?>  