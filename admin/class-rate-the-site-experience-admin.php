<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/thechetanvaghela/
 * @since      1.0.0
 *
 * @package    RTSE_Rate_The_Site_Experience
 * @subpackage RTSE_Rate_The_Site_Experience/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    RTSE_Rate_The_Site_Experience
 * @subpackage RTSE_Rate_The_Site_Experience/admin
 * @author     Chetan Vaghela <ckvaghela92@gmail.com>
 */
class RTSE_Rate_The_Site_Experience_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rate-the-site-experience-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rate-the-site-experience-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the dashboard widget
	 *
	 * @since    1.0.0
	 */
	public function rtse_add_dashboard_widgets() {
		wp_add_dashboard_widget('rtse_download_rating_widget', 'Rate the Site Experience', array($this,'rtse_download_ratings_function'));
	}

	/**
	 * Download rating sheet
	 *
	 * @since    1.0.0
	 */
	public function rtse_download_ratings_function( $post, $callback_args ) 
	{
		$download_path = $message = '';
		if(isset($_POST['rtse-download-ratings-sheet-btn']) && !empty($_POST['rtse-download-ratings-sheet-btn']))
		{
			if ( ! isset( $_POST['rtse_download_sheet_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['rtse_download_sheet_nonce'])), 'rtse_download_sheet' ) ) 
			{
				print 'Sorry, your nonce did not verify.';
				exit;
			}
			global $wpdb;
			$table_name = $wpdb->prefix . "rtse_details";

			$sql = $wpdb->prepare( "SELECT * FROM $table_name " );
			$result = $wpdb->get_results( $sql );
			$result = $wpdb->get_results( $sql, 'ARRAY_A' );

			$logText = '';
			
			if($result)
			{
				$logText .= 'ID'.",".'Ratings'.",".'ip_address'.",".'datetime'.",".'extra_details'."\n";
				foreach ($result as $key => $res) 
				{
					$logText .= $res['id'].",".$res['ratings'].",".$res['ip_address'].",".$res['datetime'].",".$res['extra_details']."\n";
				}
			}
			if(!empty($logText))
			{
				#$log_dir = ABSPATH . 'wp-content/uploads/rtse-ratings-csv/';
				$upload_dir   = wp_upload_dir();
				$log_dir = $upload_dir['basedir'] . '/rtse-ratings-csv/';
			
				$currentYear = date('Y');
				$currentMonth = date('m');     
				$currentDay = date('d');
				$logPath = $log_dir.''.$currentYear.'/'.$currentMonth.'/'.$currentDay;
				
				$logfilename = 'rtse-rating-csv-'.time();
				if (!file_exists($logPath)) {
					mkdir($logPath, 0777, true);
				}

				$logFile = fopen($logPath.'/'.$logfilename.'.csv', 'w');
				#$log_url = site_url(). '/wp-content/uploads/rtse-ratings-csv/';
				$log_url = $upload_dir['baseurl']. '/rtse-ratings-csv/';
				$logurlPath = $log_url.''.$currentYear.'/'.$currentMonth.'/'.$currentDay;
				$download_path = $logurlPath.'/'.$logfilename.'.csv';
				fwrite($logFile, $logText);
				fclose($logFile);
			}
			else
			{
				$message = '<p style="color:red;text-align: center;border: 1px solid;">'.esc_html__('No records found for download','rate-the-site-experience').'</p>';
			}
		}
		?>
		<form id="rtse-download-rating-form" method="post">
			<p><?php esc_html_e('Generate and download Ratings sheet.', 'rate-the-site-experience'); ?></p>
			<?php wp_nonce_field( 'rtse_download_sheet', 'rtse_download_sheet_nonce' ); ?>
			<input type="submit" Class="button button-primary" id="rtse-download-ratings-sheet-btn" name="rtse-download-ratings-sheet-btn" value="Download">
			<?php echo wp_kses_post	($message); ?>
			<?php 
			if($download_path)
			{
				echo '<a id="rtse-download-rating-sheet" href="'.esc_url($download_path).'" download>'.esc_html__('Download','rate-the-site-experience').'</a>';
			} 
			?>
		</form>
		<?php
	}

	public function rtse_dashboard_admin_menu() {
		add_menu_page('Rate The Site','Rate The Site','manage_options','rtse_dashboard_settings_page',array($this, 'rtse_dashboard_page_callback' ),'dashicons-star-filled');
	}

	public function rtse_dashboard_page_callback()
	{
		# define empty variables
		$form_msg = $label_value = $enable_value = $enable_widget = $prevent_value = $prevent_die ="";
			
		# if user can manage options
		if ( current_user_can('manage_options') ) {
			# submit form acrion
			if (isset($_POST['rtse-save-form-settings'])) {
				# verifing nonce
				if ( ! isset( $_POST['rtse_dashboard_field_nonce'] ) || ! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['rtse_dashboard_field_nonce'])), 'rtse_dashboard_action_nonce' ) ) {
					# form data not saved message
					$form_msg = '<b style="color:red;">Sorry, your nonce did not verify.</b>';
				} else {
					# Enable comment validation option
					if (isset($_POST['rtse-enable'])) {
						$enable_value = sanitize_text_field($_POST['rtse-enable']);
						$enable_value = !empty($enable_value) ? $enable_value : "no";
						# update Enable comment value option value
						update_option('rtse-enable', $enable_value);

						if (isset($_POST['rtse-enable-pages'])) {
							$enable_on = sanitize_text_field($_POST['rtse-enable-pages']);
							$enable_on = !empty($enable_on) ? $enable_on : "front";
							# update Enable comment value option value
							update_option('rtse-enable-pages', $enable_on);
						}
					
						
						if (isset($_POST['rtse-widget-settings']) && !empty($_POST['rtse-widget-settings'])) {
							$rtse_widget_post_settings = !empty( $_POST['rtse-widget-settings'] ) ? array_map('sanitize_text_field',$_POST['rtse-widget-settings']) : array();
							$rtse_widget_post_settings = array_map( 'esc_attr', $rtse_widget_post_settings );
							update_option('rtse-widget-settings', $rtse_widget_post_settings);
						}
						
						if (isset($_POST['rtse-widget-content']) && !empty($_POST['rtse-widget-content'])) {
							$rtse_widget_post_content = !empty( $_POST['rtse-widget-content'] ) ? array_map('sanitize_text_field',$_POST['rtse-widget-content']) : array();
							$rtse_widget_post_content = array_map( 'esc_attr', $rtse_widget_post_content );
							update_option('rtse-widget-content', $rtse_widget_post_content);
						}
						
						if (isset($_POST['rtse-thankyou-widget-content']) && !empty($_POST['rtse-thankyou-widget-content'])) {
							$rtse_thankyou_widget_post_content = !empty( $_POST['rtse-thankyou-widget-content'] ) ? array_map('sanitize_text_field',$_POST['rtse-thankyou-widget-content']) : array();
							$rtse_thankyou_widget_post_content = array_map( 'esc_attr', $rtse_thankyou_widget_post_content );
							update_option('rtse-thankyou-widget-content', $rtse_thankyou_widget_post_content);
						}

						if (isset($_FILES['rtse-widget-content-logo']) && !empty($_FILES['rtse-widget-content-logo'])) 
						{	
							if(empty($_FILES['rtse-widget-content-logo']['error']))
							{
								if(current_user_can('upload_files')) 
								{
									$mimeType = ['png','gif','jpg','jpeg'];
									$filename = sanitize_file_name($_FILES['rtse-widget-content-logo']['name']);
									$temp = explode(".", $filename);
									$extension = end($temp);
									if(in_array($extension,$mimeType) )
									{
										require_once( ABSPATH . 'wp-admin/includes/image.php' );
										require_once( ABSPATH . 'wp-admin/includes/file.php' );
										require_once( ABSPATH . 'wp-admin/includes/media.php' );
										$logn_attachment_id = media_handle_upload('rtse-widget-content-logo', 0);
											
										if (is_wp_error($logn_attachment_id)) 
										{
											$form_msg = '<b style="color:red;">Sorry, error in uploading media.</b><br/>';
										}
										else 
										{
											update_option('rtse-widget-content-logo', sanitize_text_field($logn_attachment_id));
										}
									}
									else
									{
										$form_msg = '<b style="color:red;">Sorry, You can not upload this extension media.</b><br/>';
									}
								}
								else
								{
									$form_msg = '<b style="color:red;">Sorry, You do not have permission to upload media.</b><br/>';
								}
							}
							else
							{
								update_option('rtse-widget-content-logo', '');
							}
						}
						else
						{
							update_option('rtse-widget-content-logo', '');
						}
						if(isset($_POST['rtse-logo-img-id']) && !empty($_POST['rtse-logo-img-id']))
						{
							update_option('rtse-widget-content-logo', sanitize_text_field($_POST['rtse-logo-img-id']));
						}
					}
					# form data saved message
					$form_msg = '<b style="color:green;">Settings Saved.</b><br/>';
				}
			}
		}
		# get value of enable validation
		$enable_widget = esc_attr(get_option('rtse-enable'));
		$get_enable_widget = !empty($enable_widget) ? $enable_widget : "no";
		
		$enable_widget_on_page = esc_attr(get_option('rtse-enable-pages'));
		$enable_widget_on_page = !empty($enable_widget_on_page) ? $enable_widget_on_page : "front";

		$rtse_logo_image_id = esc_attr(get_option('rtse-widget-content-logo'));

		$rtse_widget_settings = get_option('rtse-widget-settings');
		$seconds_to_open = isset($rtse_widget_settings['seconds_to_open']) ? $rtse_widget_settings['seconds_to_open'] : '';
		$number_of_days_submit = isset($rtse_widget_settings['number_of_days_submit']) ? $rtse_widget_settings['number_of_days_submit'] : '';
		$number_of_days_decline = isset($rtse_widget_settings['number_of_days_decline']) ? $rtse_widget_settings['number_of_days_decline'] : '';

		$rtse_widget_content = get_option('rtse-widget-content');
		$widget_heading = isset($rtse_widget_content['heading']) ? $rtse_widget_content['heading'] : '';
		$widget_description = isset($rtse_widget_content['description']) ? $rtse_widget_content['description'] : '';
		$widget_button_text = isset($rtse_widget_content['button_text']) ? $rtse_widget_content['button_text'] : '';
		$widget_satisfied_text = isset($rtse_widget_content['satisfied_text']) ? $rtse_widget_content['satisfied_text'] : '';
		$widget_not_satisfied_text = isset($rtse_widget_content['not_satisfied_text']) ? $rtse_widget_content['not_satisfied_text'] : '';

		$rtse_thankyou_widget_content = get_option('rtse-thankyou-widget-content');
		$thankyou_widget_heading = isset($rtse_thankyou_widget_content['heading']) ? $rtse_thankyou_widget_content['heading'] : '';
		$thankyou_widget_description = isset($rtse_thankyou_widget_content['description']) ? $rtse_thankyou_widget_content['description'] : '';
		?>
		<!-- cfv Settings -->
		<div class="wrap">
			<h2><?php esc_html_e('Rate The Site Experience','rate-the-site-experience'); ?></h2>
			<div id="rtse-setting-container">
				<div id="rtse-body">
					<div id="rtse-body-content">
						<div class="">
							<br/><?php echo wp_kses_post($form_msg); ?><hr/><br/>
							<form method="post" enctype="multipart/form-data">
								<!-- Enable Site experience -->
								<table>
									<tr valign="top">
										<th scope="row">
											<label for="rtse-enable"><?php esc_html_e('Enable? &nbsp;&nbsp;&nbsp;','rate-the-site-experience'); ?></label></th>
										<td>	
											<?php $yes_checked = ($enable_widget == "yes") ? 'checked="checked"' : "";?>
											<?php $no_checked = ($enable_widget == "no") ? 'checked="checked"' : "";?>
											<input type="radio" name="rtse-enable" id="enable-yes" value="yes" <?php echo esc_attr($yes_checked); ?> ><label for="enable-yes"><?php esc_html_e('Yes','rate-the-site-experience'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
											<input type="radio" name="rtse-enable" id="enable-no" value="no" <?php echo esc_attr($no_checked); ?>><label for="enable-no"><?php esc_html_e('No','rate-the-site-experience'); ?></label>
										</td>
									</tr>
								</table>
								<span><?php esc_html_e('Enable Site Rating experience.','rate-the-site-experience'); ?></span>
								<br/><hr><br/>
								<!-- Enable Site experience end -->
								<?php 
								$enable_setting_wrap = 'display:none;';
								if(!empty($enable_widget) && $enable_widget == "yes")
								{
									$enable_setting_wrap = '';
								}
								?>
								<div id="rtse-widget-setting-wrap"  style="<?php echo esc_html($enable_setting_wrap); ?>">
									<!-- widget content -->
									<h3><?php esc_html_e('Widget Setting','rate-the-site-experience'); ?></h3>
									<table>
										<tr valign="top">
											<th scope="row">
												<label for="rtse-enable-pages"><?php esc_html_e('Show widget on','rate-the-site-experience'); ?></label></th>
											<td>	
												<?php $frontpage_checked = ($enable_widget_on_page == "front") ? 'checked="checked"' : "";?>
												<?php $allpages_checked = ($enable_widget_on_page == "all") ? 'checked="checked"' : "";?>
												<input type="radio" name="rtse-enable-pages" id="enable-frontpage" value="front" <?php echo esc_attr($frontpage_checked); ?> ><label for="enable-frontpage"><?php esc_html_e('Front Page','rate-the-site-experience'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
												<input type="radio" name="rtse-enable-pages" id="enable-allpages" value="all" <?php echo esc_attr($allpages_checked); ?>><label for="enable-allpages"><?php esc_html_e('All Pages','rate-the-site-experience'); ?></label>
											</td>
										</tr>

										<tr valign="top">
												<th scope="row"><label for="rtse-widget-settings-sto"><?php esc_html_e('Seconds to open','rate-the-site-experience'); ?></label></th>
												<td>
													<input type="number" name="rtse-widget-settings[seconds_to_open]" id="rtse-widget-settings-sto" value="<?php echo esc_attr($seconds_to_open); ?>" />
													<span><?php esc_html_e('Set a seconds to open a widget after page load.','rate-the-site-experience'); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row"><label for="rtse-widget-settings-nods"><?php esc_html_e('Number of Days Submit','rate-the-site-experience'); ?></label></th>
												<td>
													<input type="number" name="rtse-widget-settings[number_of_days_submit]" id="rtse-widget-settings-nods" value="<?php echo esc_attr($number_of_days_submit); ?>" />
													<span><?php esc_html_e('set a number of day to display widget after submited.','rate-the-site-experience'); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row"><label for="rtse-widget-settings-nodd"><?php esc_html_e('Number of Days Decline','rate-the-site-experience'); ?></label></th>
												<td>
													<input type="number" name="rtse-widget-settings[number_of_days_decline]" id="rtse-widget-settings-nodd" value="<?php echo esc_attr($number_of_days_decline); ?>" />
													<span><?php esc_html_e('set a number of day to display widget after declined.','rate-the-site-experience'); ?></span>
												</td>
											</tr>
									</table>

									<br/><hr>

									<!-- widget content -->
									<h3><?php esc_html_e('Widget Content','rate-the-site-experience'); ?></h3>
									<table>
											<tr valign="top">
												<th scope="row"><label for="rtse-widget-logo"><?php esc_html_e('Logo ','rate-the-site-experience'); ?></label></th>
												<td>
													<?php 
													$logo_select_image_wrap = '';
													if(!empty($rtse_logo_image_id))
													{ 	
														$logo_img = wp_get_attachment_image_src($rtse_logo_image_id);
														if(isset($logo_img[0]) && !empty($logo_img[0]))
														{
															$logo_img_src = $logo_img[0];
															$logo_select_image_wrap = 'display:none;';
														}
													}  
													?>
													<div class="rtse-logo-img-select-wrap" style="<?php echo esc_attr($logo_select_image_wrap); ?>">
														<input type="file" name="rtse-widget-content-logo" class="rtse-widget-content-logo" accept="image/png, image/gif, image/jpeg">
													</div>
													<?php
													if(!empty($logo_img_src))
													{	?>
														<br/>
														<div class="rtse-logo-img-preview-wrap">
															<img src="<?php echo esc_url($logo_img_src); ?>" class="rtse-logo-img-preview" width="100" height="100">
															<input type="hidden" name="rtse-logo-img-id" value="<?php echo esc_attr($rtse_logo_image_id); ?>">
															<br/>
															<a href="javascript:void(0)" class="rtse-remove-btn rtse-remove-logo-img"><?php esc_html_e('Remove','rate-the-site-experience'); ?></a>
														</div>
														<?php
													}
													?>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row"><label for="rtse-widget-heading"><?php esc_html_e('Heading ','rate-the-site-experience'); ?></label></th>
												<td>
													<input type="text" name="rtse-widget-content[heading]" id="rtse-widget-heading" value="<?php echo esc_html($widget_heading); ?>" />
												</td>
											</tr>
											<tr valign="top">
												<th scope="row"><label for="rtse-widget-description"><?php esc_html_e('Description ','rate-the-site-experience'); ?></label></th>
												<td>
													<textarea name="rtse-widget-content[description]" id="rtse-widget-description"><?php echo esc_html($widget_description); ?></textarea>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row"><label for="rtse-widget-button_text"><?php esc_html_e('Button Text','rate-the-site-experience'); ?></label></th>
												<td>
													<input type="text" name="rtse-widget-content[button_text]" id="rtse-widget-button_text" value="<?php echo esc_html($widget_button_text); ?>" />
												</td>
											</tr>
											<tr valign="top">
												<th scope="row"><label for="rtse-widget-satisfied_text"><?php esc_html_e('Satisfied Text','rate-the-site-experience'); ?></label></th>
												<td>
													<input type="text" name="rtse-widget-content[satisfied_text]" id="rtse-widget-satisfied_text" value="<?php echo esc_html($widget_satisfied_text); ?>" />
												</td>
											</tr>
											<tr valign="top">
												<th scope="row"><label for="rtse-widget-not_satisfied_text"><?php esc_html_e('Not Satisfied Text','rate-the-site-experience'); ?></label></th>
												<td>
													<input type="text" name="rtse-widget-content[not_satisfied_text]" id="rtse-widget-not_satisfied_text" value="<?php echo esc_html($widget_not_satisfied_text); ?>" />
												</td>
											</tr>
									</table>
									<!-- widget content end -->
									<br/><hr>
									<!-- widget content -->
									<h3><?php esc_html_e('Thank you Content','rate-the-site-experience'); ?></h3>
									<table>
										<tr valign="top">
												<th scope="row"><label for="rtse-thankyou-widget-heading"><?php esc_html_e('Heading ','rate-the-site-experience'); ?></label></th>
												<td>
													<input type="text" name="rtse-thankyou-widget-content[heading]" id="rtse-thankyou-widget-heading" value="<?php echo esc_html($thankyou_widget_heading); ?>" />
												</td>
											</tr>
											<tr valign="top">
												<th scope="row"><label for="rtse-thankyou-widget-description"><?php esc_html_e('Description ','rate-the-site-experience'); ?></label></th>
												<td>
													<textarea name="rtse-thankyou-widget-content[description]" id="rtse-thankyou-widget-description"><?php echo esc_html($thankyou_widget_description); ?></textarea>
												</td>
											</tr>
									</table>
								</div>

								<br/><hr>

								<?php wp_nonce_field( 'rtse_dashboard_action_nonce', 'rtse_dashboard_field_nonce' ); ?>
								<?php  submit_button( 'Save Settings', 'primary', 'rtse-save-form-settings'  ); ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
		<?php
	}
}
