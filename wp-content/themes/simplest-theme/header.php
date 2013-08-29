<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="description" content="A description about your site" />
	<title><?php bloginfo('name'); ?> - <?php bloginfo('description'); ?></title>
	<link rel="stylesheet" href="path/to/css/reset.css" media="screen" />
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url') ?>" media="screen" />

<!--[if IE]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<?php wp_head(); // hook necessary for plugin css and js to work. also gives the admin bar ?>
</head>

<body class="home">
	<header>
		<h1><a href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a></h1>
		<h2><?php bloginfo('description'); ?></h2>

		<?php get_search_form(); ?>
		
		<ul class="utilities">
			<?php wp_list_pages( array(
				'title_li' => '',
				'include' => '2, 146',
				) ); ?>
		</ul>
		<nav>
			<ul class="nav">
				<?php wp_list_pages( array(
					'title_li' => '',
					'depth' => 1, 
					'exclude' => '2, 146',
				) ); ?>
			</ul>
		</nav>
	</header>