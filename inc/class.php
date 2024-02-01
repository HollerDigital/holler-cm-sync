<?php

class Sendy_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base {
	/**
	 * Get Name
	 *
	 * Return the action name
	 *
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'holler_cm ';
	}

	/**
	 * Get Label
	 *
	 * Returns the action label
	 *
	 * @access public
	 * @return string
	 */
	public function get_label() {
		return __( 'Campaign Monitor', 'text-domain' );
	}
  
  
	/**
	 * Run
	 *
	 * Runs the action after submit
	 *
	 * @access public
	 * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
	 * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
	 */
	public function run( $record, $ajax_handler ) {
		// Get Form Settings  
		$settings = $record->get( 'form_settings' );
		
		// Get Options
		$cm_options = get_option('holler_signup_cm_settings');
		
		//  Make sure that there is a Sendy installation url
    /*
		if ( empty( $settings['bblasso_id'] ) ) {
			return;
		}
		
    */
				
		// Get submitetd Form data
		$raw_fields = $record->get( 'fields' );
		
		// Normalize the Form Data
		$fields = [];
		foreach ( $raw_fields as $id => $field ) {
			$fields[ $id ] = $field['value'];
		}
				
	 
         // $pid =  $settings['bblasso_id'];
 
           if(strlen(trim($cm_options['api_key'])) > 0 ) {
  		
  		$result = false;
  		
      require_once 'campaignmonitor/csrest_general.php';
      require_once 'campaignmonitor/csrest_subscribers.php';
    require_once 'campaignmonitor/csrest_clients.php';
    
    	$wrap = new CS_REST_Subscribers($cm_options['list'], $cm_options['api_key']);
    		
    	$subscribe = $wrap->add(array(
  			'EmailAddress' => $fields["email"],
  			'Name' => $fields[ 'name' ],
  			'ConsentToTrack' => 'yes',
        'Resubscribe' => true
      ));
  
  		if($subscribe->was_successful()) {
        $res =  "Subscribed with code ".$result->http_status_code;
        error_log($res,0);
        $result = true;
  
      } else {
        $res =  'Failed with code '.$result->http_status_code."\n";
        error_log($res,0);
        $result = false;
      }
  	}
  	
  	return $result;
   

	}

	/**
	 * Register Settings Section
	 *
	 * Registers the Action controls
	 *
	 * @access public
	 * @param \Elementor\Widget_Base $widget
	 */
	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			'section_holler_cm',
			[
				'label' => __( 'Campaign Monitor', 'text-domain' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		
		$widget->add_control(
  			'holler_cm_list',
			[
				'label' => __( 'List', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'separator' => 'before',
				'description' => __( 'the list id you want to subscribe a user to. This encrypted & hashed id can be found under View all lists section named ID.', 'text-domain' ),
			]
		);


		$widget->end_controls_section();

	}

	/**
	 * On Export
	 *
	 * Clears form settings on export
	 * @access Public
	 * @param array $element
	 */
	public function on_export( $element ) {
		unset(
			$element['holler_cm_list']
		);
	}
}

