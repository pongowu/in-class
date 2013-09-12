<?php get_header(); ?>
    
    <div id="content">

    <h2 class="archive-title">All products featuring <?php single_cat_title(); ?></h2>    
	<?php 
	//THE LOOP.
	if( have_posts() ): 
		while( have_posts() ):
		the_post(); ?>
	
        <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
            <?php the_post_thumbnail('thumbnail', array( 'class' => 'thumbnail' ) );?> 

            <h2 class="entry-title"> <a href="<?php the_permalink(); ?>"> 
				<?php the_title(); ?> 
			</a></h2>
            

            <div class="entry-excerpt">
                <?php the_excerpt(); ?>
            </div>
       
        </article><!-- end post -->
      <?php 
	  endwhile;
	  else: ?>

	  <h2>Sorry, no products found</h2>

	  <?php endif; //END OF LOOP. ?>
	          
        
        <div id="nav-below" class="pagination"> 
            <?php 
            // use the pagenavi plugin only if it exists (plugin is installed and active)
            if(function_exists('wp_pagenavi')){
                wp_pagenavi();
            } else {
             ?>
        	<?php next_posts_link('&laquo; Older Posts'); ?>
            <?php previous_posts_link('Newer Posts &raquo;'); ?>
             <?php } ?>

        </div><!-- end #nav-below --> 
        
    </div><!-- end content -->
    
<?php get_sidebar('shop'); ?> 
<?php get_footer(); ?>  