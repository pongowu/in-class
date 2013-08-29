<?php get_header(); ?>
    
    <div id="content">
	<?php 
	//THE LOOP.
	if( have_posts() ): 
		while( have_posts() ):
		the_post(); ?>
	
        <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
                      

            <div class="entry-content">
                <?php
                the_content();                 
                ?>
            </div>
       
         </article><!-- end post -->
      <?php 
	  endwhile;
	  else: ?>

	  <h2>Sorry, no posts found</h2>

	  <?php endif; //END OF LOOP. ?>
	          
              
    </div><!-- end content -->
    
<?php get_sidebar(); ?> 
<?php get_footer(); ?>  