<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/thechetanvaghela/
 * @since      1.0.0
 *
 * @package    RTSE_Rate_The_Site_Experience
 * @subpackage RTSE_Rate_The_Site_Experience/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    RTSE_Rate_The_Site_Experience
 * @subpackage RTSE_Rate_The_Site_Experience/public
 * @author     Chetan Vaghela <ckvaghela92@gmail.com>
 */
class RTSE_Rate_The_Site_Experience_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in RTSE_Rate_The_Site_Experience_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The RTSE_Rate_The_Site_Experience_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rate-the-site-experience-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in RTSE_Rate_The_Site_Experience_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The RTSE_Rate_The_Site_Experience_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		
		$enable_widget = esc_attr(get_option('rtse-enable'));
		$enable_widget = !empty($enable_widget) ? $enable_widget : "no";
		
		if(!empty($enable_widget) && $enable_widget == "yes")
		{
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rate-the-site-experience-public.js', array( 'jquery' ), $this->version, false );
		
			$rtse_widget_settings = get_option('rtse-widget-settings');
			$seconds_open = $days_submit = $days_decline = '';
			if(!empty($rtse_widget_settings))
			{
				$seconds_to_open = isset($rtse_widget_settings['seconds_to_open']) ? $rtse_widget_settings['seconds_to_open'] : '';
				$number_of_days_submit = isset($rtse_widget_settings['number_of_days_submit']) ? $rtse_widget_settings['number_of_days_submit'] : '';
				$number_of_days_decline = isset($rtse_widget_settings['number_of_days_decline']) ? $rtse_widget_settings['number_of_days_decline'] : '';

				if(!empty($seconds_to_open) && is_numeric($seconds_to_open))
				{
					$seconds_open =$seconds_to_open;
				}
				if(!empty($number_of_days_submit) && is_numeric($number_of_days_submit))
				{
					$days_submit =$number_of_days_submit;
				}
				if(!empty($number_of_days_decline) && is_numeric($number_of_days_decline))
				{
					$days_decline = $number_of_days_decline;
				}
			}

			wp_localize_script(
				$this->plugin_name,
				'rtse_frontend_ajax_object',
				array(
					'ajaxurl' => admin_url('admin-ajax.php'),
					'seconds_to_open' => $seconds_open,
					'number_of_days_submit' => $days_submit,
					'number_of_days_decline' => $days_decline,
				)
			);
		}
	}

	/**
	 * Add popup to the footer
	 *
	 * @since    1.0.0
	 */
	public function rtse_wp_footer()
	{

		# get value of enable validation
		$enable_widget = esc_attr(get_option('rtse-enable'));
		$enable_widget = !empty($enable_widget) ? $enable_widget : "no";
		
		$rtse_widget_content = get_option('rtse-widget-content');
		
		$rtse_logo_image_id = esc_attr(get_option('rtse-widget-content-logo'));
		
		if(!empty($enable_widget) && $enable_widget == "yes")
		{
			$enable_widget_on_page = esc_attr(get_option('rtse-enable-pages'));
			$enable_widget_on_page = !empty($enable_widget_on_page) ? $enable_widget_on_page : "front";
			
			$show_on_all = false;
			if($enable_widget_on_page == "front" && is_front_page())
			{
				$show_on_all = true;
			}
			else if($enable_widget_on_page == "all")
			{
				$show_on_all = true;
			}
			else
			{
				$show_on_all = false;
			}

			if(!empty($rtse_widget_content) && $show_on_all)
			{ 
				if(!isset($_COOKIE['rtse-hide-rating-widget']) || $_COOKIE['rtse-hide-rating-widget'] == 0 )
				{
					if(!empty($rtse_widget_content))
					{
						$heading = isset($rtse_widget_content['heading']) ? $rtse_widget_content['heading'] : '';
						$description = isset($rtse_widget_content['description']) ? $rtse_widget_content['description'] : '';
						$button_text = isset($rtse_widget_content['button_text']) && !empty($rtse_widget_content['button_text']) ? $rtse_widget_content['button_text'] : 'Submit';
						$not_satisfied_text = isset($rtse_widget_content['not_satisfied_text']) ? $rtse_widget_content['not_satisfied_text'] :'';
						$satisfied_text = isset($rtse_widget_content['satisfied_text']) ? $rtse_widget_content['satisfied_text'] :'';

						if(!empty($heading) || !empty($description) || !empty($not_satisfied_text)|| !empty($satisfied_text)|| !empty($rtse_logo_image_id) || !empty($button_text))
						{	?>
							<div class="rtse-rating-widget" id="rtse-rating-widget">
								<div class="rtse-rating-widget-inner">
									<span class="rtse-rating-widget-close-btn">
										<svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
											<rect width="35" height="35" rx="17.5" fill="#D9D9D9"/>
											<path d="M18.8379 17.5L24.9019 11.4364C25.0325 11.3057 25.0325 11.094 24.9019 10.9633L24.0371 10.098C23.9744 10.0354 23.889 10 23.8004 10C23.7116 10 23.6264 10.0354 23.5637 10.098L17.4998 16.1617L11.436 10.098C11.3103 9.97235 11.088 9.9725 10.9627 10.098L10.098 10.9633C9.96733 11.094 9.96733 11.3057 10.098 11.4364L16.1619 17.5L10.098 23.5636C9.96733 23.6943 9.96733 23.906 10.098 24.0367L10.9629 24.902C11.0256 24.9646 11.1108 25 11.1996 25C11.2884 25 11.3734 24.9646 11.4361 24.902L17.5 18.8383L23.5638 24.902C23.6266 24.9646 23.7119 25 23.8005 25C23.8892 25 23.9745 24.9646 24.0373 24.902L24.9021 24.0367C25.0326 23.906 25.0326 23.6943 24.9021 23.5636L18.8379 17.5Z" fill="black"/>
										</svg>
									</span>
									<div class="rtse-widget-content">
										<?php 
										if(!empty($rtse_logo_image_id))
										{ 	
											$logo_img = wp_get_attachment_image_src($rtse_logo_image_id);
											if(isset($logo_img[0]) && !empty($logo_img[0]))
											{
												$logo_img_src = $logo_img[0];
												?>
												<div class="rtse-rating-widget-logo">
													<img src="<?php echo esc_url($logo_img_src); ?>" title="<?php esc_html_e('Rate the Site','rate-the-site-experience'); ?>" alt="<?php esc_html_e('Rate the Site','rate-the-site-experience'); ?>">
												</div>
												<?php 
											}
										} ?>
										<?php 
										if(!empty($heading))
										{ ?>
											<h2><?php echo esc_html($heading); ?></h2>
											<?php 
										}
										if(!empty($description))
										{	?>
											<p><?php echo esc_html($description); ?></p>
											<?php 
										}		
										?>
										<div class="li-rtse-satisfied" id="li-rtse-satisfied">
											<ul class="ul-rtse-satisfied">
												<li class="rtse-color-red">1</li>
												<li class="rtse-color-red">2</li>
												<li class="rtse-color-red">3</li>
												<li>4</li>
												<li>5</li>
												<li>6</li>
												<li>7</li>
												<li class="rtse-color-green">8</li>
												<li class="rtse-color-green">9</li>
												<li class="rtse-color-green">10</li>
											</ul>
											<?php 
											if(!empty($not_satisfied_text) || !empty($satisfied_text))
											{ ?>
												<div class="rtse-satiedfied-texts">
													<?php 
													if(!empty($not_satisfied_text))
													{	?>
														<span class="rtse-color-red"><?php echo esc_html($not_satisfied_text); ?></span>
														<?php
													} 
													if(!empty($satisfied_text))
													{ ?>
														<span class="rtse-color-green"><?php echo esc_html($satisfied_text); ?></span>
														<?php 
													} ?>

												</div>
												<?php 
											} ?>
										</div>
									</div>
									<div class="rtse-submit-btn">
										<a href="javascript:void(0);" id="rtse-submit-btn">
										<?php wp_nonce_field( 'rtse_save_rating', 'rtse_save_rating_nonce' ); ?>
											<?php printf( esc_html__( '%s.', 'rate-the-site-experience' ),esc_html($button_text)); ?>
										</a>
									</div>
									<div id="RTSEPleaseWaitMsgPopup">
										<span  class="rtse-wait-msg"><?php esc_html_e('Please Wait...','rate-the-site-experience'); ?></span>
									</div>
								</div>
							</div>
							<?php 
						}
					}
				}
				$rtse_thankyou_widget_content = get_option('rtse-thankyou-widget-content');
				if(!empty($rtse_thankyou_widget_content))
				{
					$heading = isset($rtse_thankyou_widget_content['heading']) ? $rtse_thankyou_widget_content['heading'] : 'Thank you for your feedback';
					$content = isset($rtse_thankyou_widget_content['description']) ? $rtse_thankyou_widget_content['description'] : '';
					if(!empty($heading) || !empty($content))
					{	?>
						<div class="rtse-success-widget" id="rtse-success-widget">
							<div class="rtse-success-widget-inner">					
								<div class="rtse-success-content">
									<h2><?php printf( esc_html__( '%s.', 'rate-the-site-experience' ),esc_html($heading)); ?></h2>
									<?php 
									if($content)
									{ ?>
										<p><?php printf( esc_html__( '%s.', 'rate-the-site-experience' ),esc_html($content)); ?></p>
										<?php 
									} ?>
								</div>
								<div class="rtse-widget-success-close-btn"><span id="rtse-widget-success-close-btn"><?php echo esc_html__('Close','rate-the-site-experience'); ?></span></div>
							</div>
						</div>
						<?php
					}
				}
			}
		} 
	}

	/**
	 * Create table
	 *
	 * @since    1.0.0
	 */
	public function rtse_create_table_for_rtse_details() {
		global $wpdb;
		$table_name = $wpdb->prefix . "rtse_details";
		$sql = $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name );
		$result = $wpdb->get_var( $sql );
		if ($result != $table_name) 
		{
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_name (
					id bigint(20) NOT NULL AUTO_INCREMENT,
					ratings text NOT NULL,
					datetime text NOT NULL,
					ip_address text NOT NULL,
					extra_details longtext,
					PRIMARY KEY  (id)
					)".$charset_collate.";";
			//reference to upgrade.php file
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			$a = dbDelta( $sql );
			//print_r($sql);
			
		}
	}

	/**
	 * Save rating callback
	 *
	 * @since    1.0.0
	 */
	public function rtse_save_ratings_callback()
	{

		if ( ! isset( $_POST['rtse_save_rating_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['rtse_save_rating_nonce'])), 'rtse_save_rating' ) ) 
		{
			$return = array(
				'status' => 'error',
				'msg'  => 'Sorry, your nonce did not verify.'
			);
			echo wp_json_encode($return);
			die();
		}
		$ratings = isset($_POST["ratings"]) ? sanitize_text_field($_POST["ratings"]) : '';
		$status = 'error';
		$msg = 'Something went wrong!';
		if(!empty($ratings))
		{
			//$datetime = time(); // timestamp
			$datetime = date('Y-m-d H:i:s');
			$ip_address = rtse_get_client_ip();
			$extra_details = '';

			global $wpdb;
			$table_name = $wpdb->prefix . "rtse_details";

			$result_check = $wpdb->insert(sanitize_text_field($table_name), array(
				'ratings' => sanitize_text_field($ratings),
				'datetime' => sanitize_text_field($datetime),
				'ip_address' => sanitize_text_field($ip_address),
				'extra_details' => sanitize_text_field($extra_details),
			));
			if($result_check)
			{
				$status = 'success';
				$msg = 'success';
			}
		}
		$return = array(
			'status' => $status,
			'msg'  => $msg
		);
		echo wp_json_encode($return);
		die();
	}
}


# get Client IP
function rtse_get_client_ip() {
    $ipaddress = '';
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key)
    {
        if (array_key_exists($key, $_SERVER) === true)
        {
            foreach (array_map('trim', explode(',',  esc_html(sanitize_text_field($_SERVER[$key])))) as $ip)
            {
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false)
                {
                    return $ip;
                }
            }
        }
    }
    //return $ipaddress;
}
