<?php /*
Plugin Name: Latest News with thumbnails
Plugin URI: http://wordpress.melissacabral.com
Description: Displays a configurable number of posts in a widget
Version: 1
Author: Melissa Cabral
Author URI: http://melissacabral.com
License: GPLv3
*/

/**
 * attach stylesheet
 * @since 0.1
 */
add_action('wp_enqueue_scripts', 'rad_latest_news_stylesheet');
function rad_latest_news_stylesheet(){
	$style_path = plugins_url('rad-latest-news-widget.css', __FILE__);
	wp_register_style('rad-news-style', $style_path);
	wp_enqueue_style('rad-news-style');
}

//tell wordpress that our widget needs to exist
function rad_register_latest_news_widget(){
	register_widget('Rad_Latest_News_Widget');	
}
add_action('widgets_init', 'rad_register_latest_news_widget');

//Our new widget is a copy of the WP_Widget object class
class Rad_Latest_News_Widget extends WP_Widget{
	//give the first function the same name as the class
	function Rad_Latest_News_Widget(){
		//Widget Settings
		$widget_settings = array(
			'classname' => 'latest-news-widget',
			'description' => 'displays any number of recent posts with pictures'			
		);
		//Widget Control Settings
		$control_settings = array(
			//necessary for multiple instances of the widget to work. WP will append a unique number to the end of the ID base
			'id_base' => 'latest-news-widget',
			'width' => 300
		);
		//WP_Widget(id-base, name, widget ops, control ops)
		$this->WP_Widget('latest-news-widget', 'Latest News', $widget_settings, $control_settings);
	}
	//Front end display (always use 'widget')
	function widget( $args,  $instance ){
		//args contains all the settings defined when the widget area was registered 
		//(see theme functions.php)
		extract($args); 
		
		// get all that data from the instance
		$title = $instance['title'];
		$number = $instance['number'];
		$show_excerpt = $instance['show_excerpt'];
		//make this title filter-able
		$title = apply_filters( 'widget-title', $title ); 
		//Widget output begins
		

		// set up a new instance of the WP_Query object
		$news_query = new WP_Query(array(
				'showposts' => $number,
				'ignore_sticky_posts' => 1,
			));

		if($news_query->have_posts() ):

			echo $before_widget;
			echo $before_title . $title . $after_title;

	?>	
		<ul>
			
			<?php while($news_query->have_posts() ): 
				$news_query->the_post(); ?>
			<li>
				<a href="<?php the_permalink(); ?>" class="thumbnail-link">
					<?php the_post_thumbnail( 'thumbnail'); ?>
				</a>
				<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				<?php if($show_excerpt == 1): ?>
					<p><?php the_excerpt(); ?></p>
				<?php endif; ?>
			</li>
			<?php endwhile; ?>

		</ul>
	<?php	
		echo $after_widget;

		endif; // have posts

		// clean up after our custom loop variables
		wp_reset_postdata();
	}
	
	//handle saves and widget locations. always use 'update'
	function update( $new_instance, $old_instance ){
		$instance = $old_instance;
		
		//strip evil scripts from all fields
		$instance['title'] = wp_filter_nohtml_kses($new_instance['title']);
		$instance['number'] = wp_filter_nohtml_kses($new_instance['number']);
		$instance['show_excerpt'] = wp_filter_nohtml_kses($new_instance['show_excerpt']);
		//more fields go here
	
		return $instance;
		
	}
	
	//optional function for the admin form
	function form( $instance ){
		//set up default settings for each field
		$defaults = array(
			'title' => 'Simple!',
			'number' => 5,
			'show_excerpt' => 1,
		);
		
		//merge defaults with the form values
		$instance = wp_parse_args( (array) $instance, $defaults );
		
		//HTML time!
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input type="text" 
			name="<?php echo $this->get_field_name('title') ?>" 
			id="<?php echo $this->get_field_id('title');?>" 
			value="<?php echo $instance['title'] ?>" >
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>">Number of Posts:</label>
			<input type="number" 
			name="<?php echo $this->get_field_name('number') ?>" 
			id="<?php echo $this->get_field_id('number');?>" 
			value="<?php echo $instance['number'] ?>" >
		</p>
		<p>
			<input type="checkbox" 
			name="<?php echo $this->get_field_name('show_excerpt') ?>" 
			id="<?php echo $this->get_field_id('show_excerpt');?>" 
			value="1"
			<?php checked( $instance['show_excerpt'], 1); ?> >

			<label for="<?php echo $this->get_field_id('show_excerpt'); ?>">Show the excerpt?</label>
		</p>
		<?php
	}

	
}




