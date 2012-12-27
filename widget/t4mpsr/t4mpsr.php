<?php
/**
 * Plugin Name: t4mpsr balance widhet
 * Description: desc
 * Version: 0.0
 * Author: Fisher
 * Author uri: http://blog.fisher.hu
 */


add_action( 'widgets_init', 't4mpsr_widget' );


function t4mpsr_widget() {
	register_widget( 'T4MPSR_Widget' );
}

class T4MPSR_Widget extends WP_Widget {

	function T4MPSR_Widget() {
		$widget_ops = array( 'classname' => 't4mpsr', 'description' => __('Display the tenant balance ', 't4mpsr') );

		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 't4mpsr-widget' );
		
		$this->WP_Widget( 't4mpsr-widget', __('t4mpsr Widget', 't4mpsr'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );

		//Our variables from the widget settings.
		$title = apply_filters('widget_title', $instance['title'] );
		$url = $instance['url'];
		$key = $instance['key'];

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;


		$response = wp_remote_post( $url, array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => array( 'menuitem' => 'remoteinfo', 'key' => $key ),
			'cookies' => array()
			)
		);

		if( is_wp_error( $response ) ) {
		    echo 'Something went wrong!';
		} else {
		   print_r( $response['body']);
		}
		// Display the widget title 


//		if ( $show_info )
//			printf( $name );

		
		echo $after_widget;
	}

	//Update the widget 
	 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['url'] = strip_tags( $new_instance['url'] );
		$instance['key'] = strip_tags( $new_instance['key'] );
		return $instance;
	}

	
	function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array( 'title' => __('balance', 't4mpsr'), 'url' => __('http://hu3.hu/bubu/baba', 't4mpsr'));
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		//Widget Title: Text Input.
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 't4mpsr'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		//Text Input.
		<p>
			<label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e('App URI:', 't4mpsr'); ?></label>
			<input id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" value="<?php echo $instance['url']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'key' ); ?>"><?php _e('App key:', 't4mpsr'); ?></label>
			<input id="<?php echo $this->get_field_id( 'key' ); ?>" name="<?php echo $this->get_field_name( 'key' ); ?>" value="<?php echo $instance['key']; ?>" style="width:100%;" />
		</p>
	<?php
	}
}

?>
