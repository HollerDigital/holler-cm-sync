<?php
  
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
****
**** Add Admin Menu For Settings
****
**/

//$cm_options = get_option('holler_signup_cm_settings');

// register the plugin settings
function holler_signup_register_settings() {
	// register our option
	register_setting( 'holler_signup_cm_settings_group', 'holler_signup_cm_settings' );
}

add_action( 'admin_init', 'holler_signup_register_settings', 100 );


function holler_signup_settings_menu() {
	// add settings page
	add_options_page(__('Campaign Monitor', 'holler'), __('Campaign Monitor', 'holler'), 'manage_options', 'pippin-campaign-monitor', 'holler_signup_settings_page');
}
add_action('admin_menu', 'holler_signup_settings_menu', 10);


function holler_signup_settings_page() {
	
	global $cm_options;
	$cm_options = get_option('holler_signup_cm_settings');
		
	?>
	<div class="wrap">
		<h2><?php _e('Campaign Monitor Settings', 'holler'); ?></h2>
		<form method="post" action="options.php" class="holler_options_form">
			<?php settings_fields( 'holler_signup_cm_settings_group' ); ?>
			<table class="form-table">
				<tr>
					<th scop="row">
						<label for="holler_signup_cm_settings[client_id]"><?php _e( 'API Client ID', 'holler' ); ?></label>		
					</th>		
					<td>	
						<input class="regular-text" type="text" id="holler_signup_cm_settings[client_id]" style="width: 300px;" name="holler_signup_cm_settings[client_id]" value="<?php if(isset($cm_options['client_id'])) { echo esc_attr($cm_options['client_id']); } ?>"/>
						<span class="description"><?php _e('Enter your Client ID for your newsletter lists.', 'holler'); ?></span>
					</td>			
				</tr>
				<tr valign="top">
					<th scop="row">
						<label for="holler_signup_cm_settings[api_key]"><?php _e( 'Campaign Monitor API Key', 'holler' ); ?></label>	
					</th>		
					<td>		
						<input class="regular-text" type="text" id="holler_signup_cm_settings[api_key]" style="width: 300px;" name="holler_signup_cm_settings[api_key]" value="<?php if(isset($cm_options['api_key'])) { echo esc_attr($cm_options['api_key']); } ?>"/>
						<span class="description"><?php _e('Enter your Campaign Monitor API key to enable a newsletter signup option with the registration form.', 'holler'); ?></span>
					</td>			
				</tr>
				<tr>
					<th scop="row">
						<label for="holler_signup_cm_settings[list]"><?php _e( 'Email List', 'holler' ); ?></label>	
					</th>	
					<td>
						<?php $lists = holler_get_campaign_monitor_lists(); ?>
						<select id="holler_signup_cm_settings[list]" name="holler_signup_cm_settings[list]">
							<?php
								if($lists) :
									foreach($lists as $id => $list_name) :
										echo '<option value="' . $id . '"' . selected($cm_options['list'], $id, false) . '>' . $list_name . '</option>';
									endforeach;
								else :
							?>
							<option value="no list"><?php _e('no lists', 'holler'); ?></option>
						<?php endif; ?>
						</select>
						<span class="description"><?php _e('Choose the list to subscribe users to', 'holler'); ?></span>
					</td>
				</tr>		
			</table>
			<?php submit_button(); ?>
			
		</form>
	</div><!--end .wrap-->
	<?php
}


// get an array of all campaign monitor subscription lists
function holler_get_campaign_monitor_lists() {
		
	global $cm_options;
	
	if(strlen(trim($cm_options['api_key'])) > 0 && strlen(trim($cm_options['api_key'])) > 0 ) {
		
		$lists = array();
 
  require_once 'campaignmonitor/csrest_general.php';
  require_once 'campaignmonitor/csrest_subscribers.php';
	require_once 'campaignmonitor/csrest_clients.php';

		$wrap = new CS_REST_Clients($cm_options['client_id'], $cm_options['api_key']);

		$result = $wrap->get_lists();
		
		if($result->was_successful()) {
			foreach($result->response as $list) {
				$lists[$list->ListID] = $list->Name;
			}
			return $lists;
		}	
	}
	return array(); // return a blank array if the API key is not set
}

