<?php
/**
 * Plugin Name: t4mpsr balance widhet
 * Description: desc
 * Version: 0.13
 * Author: Fisher
 * Author uri: http://blog.fisher.hu
 */


add_action( 'widgets_init', 't4mpsr_widget' );
add_action('show_user_profile', 't4mpsr_extra_user_profile_fields' );
add_action('edit_user_profile', 't4mpsr_extra_user_profile_fields' );
add_action( 'personal_options_update', 't4mpsr_save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 't4mpsr_save_extra_user_profile_fields' );

function t4mpsr_extra_user_profile_fields( $user ) {
?>
  <h3><?php _e("t4mpsr settings", "blank"); ?></h3>
  <table class="form-table">
    <tr>
      <th><label for="phone"><?php _e("URL"); ?></label></th>
      <td>
        <input type="text" name="t4mpsrurl" id="t4mpsrurl" class="regular-text" 
            value="<?php echo esc_attr( get_the_author_meta( 't4mpsrurl', $user->ID ) ); ?>" /><br />
        <span class="description"><?php _e("Enter the t4mpsr site url."); ?></span>
    </td>
    </tr>
    <tr>
      <th><label for="phone"><?php _e("Key"); ?></label></th>
      <td>
        <input type="text" name="t4mpsrkey" id="t4mpsrkey" class="regular-text" 
            value="<?php echo esc_attr( get_the_author_meta( 't4mpsrkey', $user->ID ) ); ?>" /><br />
        <span class="description"><?php _e("Enter the t4mpsr site key."); ?></span>
        </td>
    </tr>
  </table>
<?php
}

function t4mpsr_save_extra_user_profile_fields( $user_id ) {
  $saved = false;
  if ( current_user_can( 'edit_user', $user_id ) ) {
    update_user_meta( $user_id, 't4mpsrurl', $_POST['t4mpsrurl'] );
    update_user_meta( $user_id, 't4mpsrkey', $_POST['t4mpsrkey'] );
    $saved = true;
  }
  return true;
}
/* " - mc syntax highlight restore */

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
		$url = get_the_author_meta( 't4mpsrurl', $user->ID );
		$key = get_the_author_meta( 't4mpsrkey', $user->ID );
		$title = 'Balance';
		
		if ( is_user_logged_in()) {

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
		   print_r($response['body']);
		   
		}
		// Display the widget title 


//		if ( $show_info )
//			printf( $name );

		
		echo $after_widget;
		}
	}
	}

?>
