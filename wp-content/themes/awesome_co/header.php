<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />

<title><?php bloginfo('name'); ?> - <?php bloginfo('description'); ?></title>

<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_directory' ); ?>/styles/reset.css" />

<?php 
//Necessary in <head> for JS and plugins to work. 
//I like it before style.css loads so the theme stylesheet is more specific than all others.
wp_head();  ?>

<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<!-- HTML5 shiv -->
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->

</head>

<body <?php body_class(); ?> >
<div id="wrapper" class="clearfix"> 
	<header role="banner">
		<h1 class="site-name"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="AwesomeCo" rel="home"> 
			<?php bloginfo('name'); ?> 
		</a></h1>
		<h2 class="site-description"> <?php bloginfo('description'); ?> </h2>
		
		<?php get_search_form(); ?>
		
		<?php wp_nav_menu( array( 
			'theme_location' => 'utility_menu',
			'container' => 'false', // false makes it not wrap any tags around it
			'menu_class' => 'utilities', // add the css class
		 ) ); ?>
		
		<?php wp_nav_menu( array( 
			'theme_location' => 'main_menu',
			'container' => 'nav', // the theme location is the area you made in admin, and container lets you set what html tag you need.	
		 ) ); ?>
	</header>    <!-- end header -->

	<?php 
	// show a large featured image on top of the front page, and a shorter banner on all other views
	if( is_front_page() ):
		the_post_thumbnail( 'awesome-home-banner' );
	elseif(is_singular() AND 'product' != get_post_type() ): 
		the_post_thumbnail( 'awesome-interior-banner' );
	endif;
	?>

	<?php 
	// breadcrumbs
	if('dimox_breadcrumbs()'){
		dimox_breadcrumbs();
	}
	 ?>