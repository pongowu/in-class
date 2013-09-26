<?php /*
Plugin Name: Simple Widget with one field 
Plugin URI: http://wordpress.melissacabral.com
Description: A starting point for learning about widgets
Version: 1
Author: Melissa Cabral
Author URI: http://melissacabral.com
License: GPL
*/

//tell wordpress that our widget needs to exist
function rad_register_widget(){
	register_widget('Rad_Simple_Widget');	
}
add_action('widgets_init', 'rad_register_widget');

//Our new widget is a copy of the WP_Widget object class
class Rad_Simple_Widget extends WP_Widget{
	//give the first function the same name as the class
	function Rad_Simple_Widget(){
		//Widget Settings
		$widget_ops = array(
			'classname' => 'simple-widget',
			'description' => 'the simplest widget with a title'			
		);
		//Widget Control Settings
		$control_ops = array(
			//necessary for multiple instances of the widget to work. WP will append a unique number to the end of the ID base
			'id_base' => 'simple-widget',
			'width' => 300
		);
		//WP_Widget(id-base, name, widget ops, control ops)
		$this->WP_Widget('simple-widget', 'Simplest Widget Ever', $widget_ops, $control_ops);
	}
	//Front end display (always use 'widget')
	function widget( $args,  $instance ){
		//args contains all the settings defined when the widget area was registered 
		//(see theme functions.php)
		extract($args); 
		
		//make this title filter-able
		$title = apply_filters( 'widget-title', $instance['title'] ); 
		//Widget output begins
		echo $before_widget;
		
		//show the title if the user filled one in
		if($title):
			echo $before_title . $title . $after_title;
		endif;
		
		echo 'Demons Run EXTERMINATE! Spoilers! Bow ties are cool Spoilers!';
		
		echo $after_widget;
	}
	
	//handle saves and widget locations. always use 'update'
	function update( $new_instance, $old_instance ){
		$instance = $old_instance;
		
		//strip evil scripts from all fields
		$instance['title'] = wp_filter_nohtml_kses($new_instance['title']);
		//more fields go here
	
		return $instance;
		
	}
	
	//optional function for the admin form
	function form( $instance ){
		//set up default settings for each field
		$defaults = array(
			'title' => 'Simple!'
		);
		
		//merge defaults with the form values
		$instance = wp_parse_args( (array) $instance, $defaults );
		
		//HTML time!
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input type="text" name="<?php echo $this->get_field_name('title') ?>" id="<?php echo $this->get_field_id('title');
			?>" style="width:100%" value="<?php echo $instance['title'] ?>" />
		</p>
		<?php
	}

	
}




