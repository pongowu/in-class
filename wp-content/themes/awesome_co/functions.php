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

/**
* add menu area support
*/

add_action('init', 'awesome_menu_areas');
function awesome_menu_areas(){
	register_nav_menus( array('main_menu' => 'Top Navigation Bar', 'utility_menu' => 'Utility Area', 'footer_menu' => 'Footer Menu Area',) );
} // even if you only have one menu, you still have to set it as an array. This sets up the registration of the menu area in the admin panel.

/**
* add widget support. create 4 widget areas
*/

 add_action('widgets_init', 'awesome_sidebars');
 function awesome_sidebars(){
 	register_sidebar( array(
 		'name' => 'Blog Sidebar',
 		'id' => 'blog-sidebar',
 		'description' => 'This appears beside all blog and archive views',
 		'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
 		'after_widget' => '</section>',
 		'before_title' => '<h3 class="widget-title">',
 		'after_title' => '</h3>',
 		) );

 	register_sidebar( array(
 		'name' => 'Home Area',
 		'id' => 'home-area',
 		'description' => 'This appears near the bottom of the home page, after the content',
 		'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
 		'after_widget' => '</section>',
 		'before_title' => '<h3 class="widget-title">',
 		'after_title' => '</h3>',
 		) );

 	register_sidebar( array(
 		'name' => 'Page Sidebar',
 		'id' => 'page-sidebar',
 		'description' => 'This appears beside all static pages',
 		'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
 		'after_widget' => '</section>',
 		'before_title' => '<h3 class="widget-title">',
 		'after_title' => '</h3>',
 		) );

 	register_sidebar( array(
 		'name' => 'Footer Area',
 		'id' => 'footer-area',
 		'description' => 'This appears at the bottom of every view',
 		'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
 		'after_widget' => '</section>',
 		'before_title' => '<h3 class="widget-title">',
 		'after_title' => '</h3>',
 		) );
 }

/**
* the callback for adding html to the comments (in comments_template.php). snippet in Codex
*/
function awesome_comment_callback($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment;
		extract($args, EXTR_SKIP);

		if ( 'div' == $args['style'] ) {
			$tag = 'div';
			$add_below = 'comment';
		} else {
			$tag = 'li';
			$add_below = 'div-comment';
		}
?>
		<<?php echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
		<?php if ( 'div' != $args['style'] ) : ?>
		<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
		<?php endif; ?>

		<?php // EDIT BELOW THIS POINT ?>
		<div class="comment-author vcard">
			<?php if ($args['avatar_size'] != 0) echo get_avatar( $comment, $args['avatar_size'] ); ?>
			<span class="fn"><?php comment_author_link(); ?></span>
		</div>

<?php if ($comment->comment_approved == '0') : ?>
		<em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.') ?></em>
		<br />
<?php endif; ?>

		<?php comment_text() ?>

		<div class="comment-meta commentmetadata">
			<span class="comment-date"><?php comment_date('F j, Y'); ?></span>
			<span class="comment-link"><a href="<?php comment_link(); ?>">Link</a></span>
			<span class="edit-comment"><?php edit_comment_link(); ?></span>



			<div class="reply">
			<?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => 
			$depth, 'max_depth' => $args['max_depth']))) ?>
			</div>
		</div>	<!-- comment-meta -->	

		
			<?php if ( 'div' != $args['style'] ) : ?>
		</div>
			<?php endif; ?>
<?php
        }






 /* Dimox Breadcrumbs
 * http://dimox.net/wordpress-breadcrumbs-without-a-plugin/
 * Since ver 1.0
 * Add this to any template file by calling dimox_breadcrumbs()
 * Changes: MC added taxonomy support
 */
function dimox_breadcrumbs(){
  /* === OPTIONS === */
	$text['home']     = 'Home'; // text for the 'Home' link
	$text['category'] = 'Archive by Category "%s"'; // text for a category page
	$text['tax'] 	  = 'Archive for "%s"'; // text for a taxonomy page
	$text['search']   = 'Search Results for "%s" Query'; // text for a search results page
	$text['tag']      = 'Posts Tagged "%s"'; // text for a tag page
	$text['author']   = 'Articles Posted by %s'; // text for an author page
	$text['404']      = 'Error 404'; // text for the 404 page

	$showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
	$showOnHome  = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
	$delimiter   = ' &raquo; '; // delimiter between crumbs
	$before      = '<span class="current">'; // tag before the current crumb
	$after       = '</span>'; // tag after the current crumb
	/* === END OF OPTIONS === */

	global $post;
	$homeLink = get_bloginfo('url') . '/';
	$linkBefore = '<span typeof="v:Breadcrumb">';
	$linkAfter = '</span>';
	$linkAttr = ' rel="v:url" property="v:title"';
	$link = $linkBefore . '<a' . $linkAttr . ' href="%1$s">%2$s</a>' . $linkAfter;

	if (is_home() || is_front_page()) {

		if ($showOnHome == 1) echo '<div id="crumbs"><a href="' . $homeLink . '">' . $text['home'] . '</a></div>';

	} else {

		echo '<div id="crumbs" xmlns:v="http://rdf.data-vocabulary.org/#">' . sprintf($link, $homeLink, $text['home']) . $delimiter;

		
		if ( is_category() ) {
			$thisCat = get_category(get_query_var('cat'), false);
			if ($thisCat->parent != 0) {
				$cats = get_category_parents($thisCat->parent, TRUE, $delimiter);
				$cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
				$cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
				echo $cats;
			}
			echo $before . sprintf($text['category'], single_cat_title('', false)) . $after;

		} elseif( is_tax() ){
			$thisCat = get_category(get_query_var('cat'), false);
			if ($thisCat->parent != 0) {
				$cats = get_category_parents($thisCat->parent, TRUE, $delimiter);
				$cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
				$cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
				echo $cats;
			}
			echo $before . sprintf($text['tax'], single_cat_title('', false)) . $after;
		
		}elseif ( is_search() ) {
			echo $before . sprintf($text['search'], get_search_query()) . $after;

		} elseif ( is_day() ) {
			echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
			echo sprintf($link, get_month_link(get_the_time('Y'),get_the_time('m')), get_the_time('F')) . $delimiter;
			echo $before . get_the_time('d') . $after;

		} elseif ( is_month() ) {
			echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
			echo $before . get_the_time('F') . $after;

		} elseif ( is_year() ) {
			echo $before . get_the_time('Y') . $after;

		} elseif ( is_single() && !is_attachment() ) {
			if ( get_post_type() != 'post' ) {
				$post_type = get_post_type_object(get_post_type());
				$slug = $post_type->rewrite;
				printf($link, $homeLink . '/' . $slug['slug'] . '/', $post_type->labels->singular_name);
				if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;
			} else {
				$cat = get_the_category(); $cat = $cat[0];
				$cats = get_category_parents($cat, TRUE, $delimiter);
				if ($showCurrent == 0) $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
				$cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
				$cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
				echo $cats;
				if ($showCurrent == 1) echo $before . get_the_title() . $after;
			}

		} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
			$post_type = get_post_type_object(get_post_type());
			echo $before . $post_type->labels->singular_name . $after;

		} elseif ( is_attachment() ) {
			$parent = get_post($post->post_parent);
			$cat = get_the_category($parent->ID); $cat = $cat[0];
			$cats = get_category_parents($cat, TRUE, $delimiter);
			$cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
			$cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
			echo $cats;
			printf($link, get_permalink($parent), $parent->post_title);
			if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;

		} elseif ( is_page() && !$post->post_parent ) {
			if ($showCurrent == 1) echo $before . get_the_title() . $after;

		} elseif ( is_page() && $post->post_parent ) {
			$parent_id  = $post->post_parent;
			$breadcrumbs = array();
			while ($parent_id) {
				$page = get_page($parent_id);
				$breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
				$parent_id  = $page->post_parent;
			}
			$breadcrumbs = array_reverse($breadcrumbs);
			for ($i = 0; $i < count($breadcrumbs); $i++) {
				echo $breadcrumbs[$i];
				if ($i != count($breadcrumbs)-1) echo $delimiter;
			}
			if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;

		} elseif ( is_tag() ) {
			echo $before . sprintf($text['tag'], single_tag_title('', false)) . $after;

		} elseif ( is_author() ) {
	 		global $author;
			$userdata = get_userdata($author);
			echo $before . sprintf($text['author'], $userdata->display_name) . $after;

		} elseif ( is_404() ) {
			echo $before . $text['404'] . $after;
		}

		if ( get_query_var('paged') ) {
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
			echo __('Page') . ' ' . get_query_var('paged');
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
		}

		echo '</div>';

	}
} // end dimox_breadcrumbs()