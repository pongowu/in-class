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
            <div class="postmeta"> 
                <span class="author"> Posted by: <?php the_author(); ?> </span> 
                <span class="date"> <?php the_date(); ?> </span> 
                <span class="num-comments"> 
			<?php comments_number('No comments yet', 'One comment', '% comments'); ?></span> 
                <span class="categories"> 
					<?php the_category(); ?>                
                </span> 
                <span class="tags">
					<?php the_tags(); ?>
				</span> 
            </div><!-- end postmeta --> 

            <?php if( has_post_thumbnail() ): // this is a conditional tag ?>
                <div class="image-container">
            <?php the_post_thumbnail('thumbnail', array( 'class' => 'thumb' ) ); // this now shows the image in the post ?>           
                </div>
            <?php endif; ?>

            <div class="entry-content">
                <?php
                awesome_content();
                ?>
            </div>
       
        
		<?php comments_template(); ?>
		 </article><!-- end post -->
      <?php 
	  endwhile;
	  else: ?>

	  <h2>Sorry, no posts found</h2>

	  <?php endif; //END OF LOOP. ?>
	          
        
        <div id="nav-below" class="pagination"> 
        	<?php //TODO: Make pagination functional ?>
            <a href="/blog/page/2/" >&larr; View Older Posts</a> 
        </div><!-- end #nav-below --> 
        
    </div><!-- end content -->
    
<?php get_sidebar(); ?> 
<?php get_footer(); ?>  