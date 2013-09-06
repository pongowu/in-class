<?php 
	// hide the comments if the post is password protected
	if( post_password_required() ):
		echo 'Enter the password to see the comments on this post';
		return; // stop the rest of this file from running
	endif;

	// separate comments by type
	$comments_by_type = &separate_comments( $comments );
	// this takes all the comments for this posts and separates them into types and holds it in the array variable $comments_by_type
	// the & is a pass-by operator
	// count just the comments
	$comment_count = count($comments_by_type['comment']);
	// count just the trackbacks
	$trackback_count = count($comments_by_type['pings']);
 ?>

<section id="comments">
	<h3 id="comments_title"><?php echo $comment_count ?> Comments so far
	 | <?php echo $trackback_count ?> Sites Link Here |
		<?php if(comments_open()): ?>
	 <a href="#respond">Leave a Comment</a>
		<?php endif; // comments open?>
	</h3>
	<div class="commentlist">
		<?php wp_list_comments( array(
			'type' => 'comment',
			'style' => 'div',
			'avatar_size' => 70,
			'callback' => 'awesome_comment_callback', // defined in functions.php			
		) ); ?>
	</div>

	<?php // only show pagination if the option is set and the post has more than 1 page of comments
		if( get_option('page_comments') AND get_comment_pages_count() > 1):
	?>
	<div class="pagination">
		<?php previous_comments_link(); ?>
		<?php next_comments_link(); ?>
	</div>	
	<?php endif; //paginated?>

	<?php comment_form(); ?>
</section>

<section id="trackbacks">
	<h3>Sites that Link Here</h3>
	<ul>
		<?php wp_list_comments( array(
			'type' => 'pings', // pingbacks and trackbacks
		)); ?>
	</ul>
</section>			