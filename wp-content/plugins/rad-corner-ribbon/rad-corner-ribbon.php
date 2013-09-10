<?php 
/*
Plugin Name: RSS Feed Corner Ribbon
Description: Adds a promotional ribbon to the corner of the site
Plugin URI: http://plugin-support-site.com
Author: Annette Whitney
Version: 0.1
License: GPLv3 or higher
*/
 
 /**
* HTML output of the ribbon
*/
function rad_ribbon_output(){
	// only show the ribbon on the front page and the blog page
	if( is_front_page() OR is_home() ):

	
?>
<!--begin rad corner ribbon by whatever -->
<a href=" <?php bloginfo('rss2_url'); ?> " id="rad-corner-ribbon">
	<img src=" <?php echo plugins_url('images/corner-ribbon.png', __FILE__); ?>" alt="Subscribe to RSS Feed" />
</a>	
<?php  
	endif; // front page or blog page
}
add_action('wp_footer', 'rad_ribbon_output');

/**
* add the stylesheet
*/
function rad_ribbon_stylesheet(){
	// only display on front page and blog
	if( is_front_page() OR is_home() ):

		// get the path to the css file
		$css_path = plugins_url( 'css/corner-ribbon.css', __FILE__ );
		// tell wp that it exists
		wp_register_style( 'rad-ribbon-css', $css_path);
		// put the stypesheet in the head of the theme
		wp_enqueue_style ('rad-ribbon-css');

	endif; //end of the front page/blog page
}
add_action('wp_enqueue_scripts', 'rad_ribbon_stylesheet');

