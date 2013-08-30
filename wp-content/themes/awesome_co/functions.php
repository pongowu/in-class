<?php 
// don't close this PHP. this page only contains php & runs before the header.php starts
// activate post featured images

add_theme_support('post-thumbnails'); // adds image to the page
add_theme_support('custom-background'); // adds a customizable background area in the admin panel under apperance
add_theme_support('post-formats', array('aside', 'gallery', 'image', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat') );
// only use the post formats you want

add_editor_style();
// it gives us the ability to style that editor-style.css to control the edit screen


// add custom image sizes

add_image_size('awesome-home-banner', 960, 330, true);

add_image_size('awesome-interior-banner', 960, 113, true);

// change the default exceprt length
function awesome_excerpt_length(){
	return 25;
}
// use a hook to trigger this function at the right time
add_filter('excerpt_length', 'awesome_excerpt_length');

// change the [..] at the end of the excerpts
function awesome_readmore(){
	return '<a href="'. get_permalink() .'" class="readmore">Read More</a>'; // get_permalink doesn't echo. the_permalink does.
}
add_filter('excerpt_more', 'awesome_readmore');

// improve user experience when replying to comments. for nested comments
function awesome_comment_reply(){
	if( is_singular() AND comments_open() AND get_option('thread_comments') ):
		wp_enqueue_script( 'comment-reply' );
	endif;
	// is_singular is a combination of is_single and is_page.
}
add_action('wp_print_scripts', 'awesome_comment_reply');



/**
* Helper function for showing content in any view.
* will show excerpts on all archives of the blog. 
* (this is a doc-block comment block)
* @since 0.1 (this is the version of the page)
*/

function awesome_content(){
	if( is_single() OR is_page() OR has_post_format('gallery') ): // this shows full content or just an exceprt. single is just a post
            the_content(); 
        else:
            the_excerpt(); 
        endif;
}