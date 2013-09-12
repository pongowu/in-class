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
            
			<?php the_post_thumbnail('medium', array('class' => 'thumb alignright') ); ?>


            <div class="entry-content">
            	<?php //the_meta(); //show a list of all custom fields ?>
            	<strong>Price:
            	<?php 
            	// get just one key from the custom fields
            	echo get_post_meta($post->ID, 'price', true); ?>
            	</strong>

                <?php 
                // show a list of terms in the brand taxonomy, separated by commas
                the_terms( $post->ID, 'brand', '<p>Brand: ', ', ', '</p>' );
                // before, seperator between, after ?>

                <?php the_terms( $post->ID, 'feature', '<p>Features: ', ' | ', '</p>' ); ?>

                <?php the_content();   ?>
            </div>

            <div class="pagination">
            	<?php previous_post_link( '%link', 'Older: %title' ); ?>
            	<?php next_post_link( '%link', 'Newer: %title'); ?>
       		</div>
        
		
		 </article><!-- end post -->
      <?php 
	  endwhile;
	  else: ?>

	  <h2>Sorry, no product found</h2>

	  <?php endif; //END OF LOOP. ?>
	          
        
        
        
    </div><!-- end content -->
    
<?php get_sidebar('shop'); ?> 
<?php get_footer(); ?>  