<?php
/**
 * Plugin Name: t4mpsr balance widhet
 * Description: desc
 * Version: 0.14
 * Author: Fisher
 * Author uri: http://blog.fisher.hu
 */


add_action('show_user_profile', 't4mpsr_extra_user_profile_fields' );
add_action('edit_user_profile', 't4mpsr_extra_user_profile_fields' );
add_action('personal_options_update', 't4mpsr_save_extra_user_profile_fields' );
add_action('edit_user_profile_update', 't4mpsr_save_extra_user_profile_fields' );
add_action('wp_dashboard_setup', 'add_t4mpsr_widget' ); 

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
	$current_user = wp_get_current_user();
	$url = get_the_author_meta( 't4mpsrurl', $current_user->ID );
	$key = get_the_author_meta( 't4mpsrkey', $current_user->ID );

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
		$t4mpsr_data = explode("\n", $response['body']);
		$tb=$t4mpsr_data[0];
		$gb=$t4mpsr_data[1];
		$gs=$t4mpsr_data[2];
		print('<p><strong>A te egyenleged:</strong> ' . number_format_i18n($tb) . '</p>');
		print('<p><strong>A teljes egyenleg: </strong> ' . number_format_i18n($gb) . '</p>');
		print('<p><strong>Tartalék: </strong> ' . number_format_i18n($gs) . '</p>');
		
	}
}

function add_t4mpsr_widget() {
	wp_add_dashboard_widget('t4mpsr_widget','hu3 egyenleg', 't4mpsr_widget');
}



?>
