<?php
/**
 * The Categories widget replaces the default WordPress Categories widget. This version gives total
 * control over the output to the user by allowing the input of all the arguments typically seen
 * in the wp_list_categories() function.
 *
 */
class The_Countdown_Widget extends WP_Widget {

	// Prefix for the widget.
	var $prefix;

	// Textdomain for the widget.
	var $textdomain;

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 0.6.0
	 */
	function __construct() {
	
		$this->prefix = 'the-countdown';
		$this->textdomain = 'the-countdown';
	
		// Give your own prefix name eq. your-theme-name-
		$prefix = '';
		
		// Set up the widget options
		$widget_options = array(
			'classname' => 'the-countdown',
			'description' => esc_html__( '[+] Advanced widget gives you total control over the countdown.', $this->textdomain )
		);

		// Set up the widget control options
		$control_options = array(
			'width' => 460,
			'height' => 350,
			'id_base' => $this->prefix
		);

		// Create the widget
		parent::__construct( $this->prefix, esc_attr__( 'The Countdown', $this->textdomain ), $widget_options, $control_options );
		
		// Load the widget stylesheet for the widgets admin screen
		add_action( 'load-widgets.php', array(&$this, 'load_scripts_styles') );
		add_action( 'admin_print_styles', array(&$this, 'admin_print_styles') );
		
		// Print the user costum style sheet
		if ( is_active_widget(false, false, $this->id_base) ) {
			wp_enqueue_style( $this->prefix, THE_COUNTDOWN_URL . 'css/tcp.css' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( $this->prefix, THE_COUNTDOWN_URL . 'js/jquery.countdown.min.js' );
			add_action( 'wp_head', array( &$this, 'custom_header') );
			add_action( 'wp_footer', array( &$this, 'custom_footer') );
		}
	}


	/**
	 * Push the widget stylesheet widget.css into widget admin page
	 * @since 0.6.0
	 */
	function load_scripts_styles() {
		wp_enqueue_style( 'total-dialog', THE_COUNTDOWN_URL . 'css/dialog.css', array( 'farbtastic', 'thickbox' ), THE_COUNTDOWN_VERSION );
		wp_register_script( 'total-dialog', THE_COUNTDOWN_URL . 'js/jquery.dialog.js', array( 'jquery', 'farbtastic', 'thickbox' ), THE_COUNTDOWN_VERSION );
		wp_enqueue_script( 'countdown-dialog', THE_COUNTDOWN_URL . 'js/jquery.countdown-dialog.js', array( 'total-dialog' ), THE_COUNTDOWN_VERSION );
	}
	
	
	/**
	 * Push the widget stylesheet widget.css into widget admin page
	 * @since 0.6.0
	 */	
	function admin_print_styles() {
		echo '<style type="text/css"> .tcpControls .timestamp { background-image: url(images/date-button.gif); background-position: left top; background-repeat: no-repeat; padding-left: 18px; }</style>';
	}
	
	
	/**
	 * Push the widget stylesheet widget.css into widget admin page
	 * @since 0.6.0
	 */		
	function custom_header() {
		$settings = $this->get_settings();
		foreach ($settings as $key => $setting){
			$widget_id = $this->id_base . '-' . $key;
			if( is_active_widget( false, $widget_id, $this->id_base ) ) {
				
				// Print the countdown script new Date(year, mth - 1, day, hr, min, sec)
				if ( !empty( $setting['until'] ) ) {
					echo '<style type="text/css">';
						if ( $setting['bg_color'] ) 		echo '#' . $this->id . '-wrapper .countdown_section {background-color: ' . $setting['bg_color'] . '}';
						if ( $setting['counter_image'] ) 	echo '#' . $this->id . '-wrapper .countdown_section {background-image: url(' . $setting['counter_image'] . '); }';
						if ( $setting['counter_color'] )  	echo '#' . $this->id . '-wrapper .countdown_amount {color: ' . $setting['counter_color'] . '}';
						if ( $setting['label_color'] ) 		echo '#' . $this->id . '-wrapper .countdown_section {color: ' . $setting['label_color'] . '}';
					echo '</style>';
					
					echo '<script type="text/javascript">';
						echo 'jQuery(document).ready(function($){';
							$countdown	 = '';
							$countdown	.= $setting['counter'] . ': theDate ,'; // until or since
							$countdown 	.= !empty($setting['expiryUrl']) ? 'expiryUrl: "' . $setting['expiryUrl'] . '",' : 'expiryUrl: "",';
							$countdown 	.= !empty($setting['expiryText']) ? 'expiryText: "' . $setting['expiryText'] . '",' : 'expiryText: "",';
							$countdown 	.= 'alwaysExpire: ' . $setting['alwaysExpire'] . ',';
							$countdown 	.= "format: '" . $setting['format'] . "',";
							$countdown 	.= 'compact: ' . $setting['compact'] . ',';
							$countdown 	.= 'tickInterval: ' . $setting['tickInterval'] . ',';
							$countdown 	.= "compactLabels: ['" . $setting['compactLabels'][0] . "', '" . $setting['compactLabels'][1] . "', '" . $setting['compactLabels'][2] . "', '" . $setting['compactLabels'][3] . "'],";
							$countdown	.= "labels: ['" . $setting['cLabels'][0] . "', '" . $setting['cLabels'][1] . "', '" . $setting['cLabels'][2] . "', '" . $setting['cLabels'][3] . "', '" . $setting['cLabels'][4] . "', '" . $setting['cLabels'][5] . "', '" . $setting['cLabels'][6] . "'],";
							$countdown 	.= "labels1: ['" . $setting['cLabels1'][0] . "', '" . $setting['cLabels1'][1] . "', '" . $setting['cLabels1'][2] . "', '" . $setting['cLabels1'][3] . "', '" . $setting['cLabels1'][4] . "', '" . $setting['cLabels1'][5] . "', '" . $setting['cLabels1'][6] . "']";
							
							echo 'var theDate = new Date("' . $setting['until'][0] . '/' . $setting['until'][1] . '/' . $setting['until'][2] . ' ' . $setting['until'][3] . ':' . $setting['until'][4] . '");';
							echo "$('#$widget_id-wrapper').countdown({ $countdown });";
						echo '});';
					echo '</script>' ."\n";
				}

				// Print the custom style and script
				if ( ! empty( $setting['header'] ) ) 
					echo $setting['header'] ."\n";
			}
		}
	}
		
	
	/**
	 * Custom footer
	 * @since 1.1.6
	 */		
	function custom_footer() {
		$settings = $this->get_settings();
		foreach ( $settings as $key => $setting )
			if( $setting['footer'] )
				echo $setting['footer'] ."\n";	
	}
	
	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 0.6.0
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Set up the arguments for wp_list_categories(). */
		$args = array(
			'title_icon'			=> $instance['title_icon'],
			'counter' 				=> $instance['counter'],
			'until' 				=> $instance['until'],
			'cLabels' 				=> $instance['cLabels'],
			'cLabels1' 				=> $instance['cLabels1'],
			'compactLabels' 		=> $instance['compactLabels'],
			'format' 				=> $instance['format'],
			'expiryUrl' 			=> $instance['expiryUrl'],
			'expiryText' 			=> $instance['expiryText'],
			'alwaysExpire' 			=> !empty( $instance['alwaysExpire'] ) ? true : false,
			'compact' 				=> !empty( $instance['compact'] ) ? true : false,
			'onExpiry' 				=> $instance['onExpiry'],
			'onTick' 				=> $instance['onTick'],
			'tickInterval' 			=> $instance['tickInterval'],
			'counter_image' 		=> $instance['counter_image'],
			'bg_color' 				=> $instance['bg_color'],
			'counter_color' 		=> $instance['counter_color'],
			'label_color' 			=> $instance['label_color'],
			'intro' 			=> $instance['intro'],
			'outro' 			=> $instance['outro'],
			'header' 			=> $instance['header'],
			'footer' 			=> $instance['footer'],
			'toggle_active'			=> $instance['toggle_active']
		);

		// Output the theme's widget wrapper
		echo $before_widget;		

		// If a title was input by the user, display it
		if ( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;
			

		// Print intro text if exist
		if ( ! empty( $instance['intro'] ) )
			echo '<p class="'. $this->id . '-intro-text intro-text">' . $instance['intro'] . '</p>';			
			
		echo '<div id="'. $this->id . '-wrapper"></div>';		
	
		// Print outro text if exist
		if ( ! empty( $instance['outro'] ) )
			echo '<p class="'. $this->id . '-outro-text outro-text">' . $instance['outro'] . '</p>';		

		// Close the theme's widget wrapper
		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 * @since 0.6.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Set the instance to the new instance. */
		$instance = $new_instance;

		$instance['title'] 				= strip_tags( $new_instance['title'] );
		$instance['title_icon']			= strip_tags( $new_instance['title_icon'] );
		$instance['counter'] 			= $new_instance['counter'];
		$instance['until'] 				= $new_instance['until'];
		$instance['cLabels'] 			= $new_instance['cLabels'];
		$instance['cLabels1'] 			= $new_instance['cLabels1'];
		$instance['compactLabels'] 		= $new_instance['compactLabels'];
		$instance['format'] 			= $new_instance['format'];
		$instance['expiryUrl'] 			= strip_tags( $new_instance['expiryUrl'] );
		$instance['expiryText'] 		= strip_tags( $new_instance['expiryText'] );
		$instance['alwaysExpire'] 		= ( isset( $new_instance['alwaysExpire'] ) ? 1 : 0 );
		$instance['compact'] 			= ( isset( $new_instance['compact'] ) ? 1 : 0 );
		$instance['onExpiry'] 			= $new_instance['onExpiry'];
		$instance['onTick'] 			= $new_instance['onTick'];
		$instance['tickInterval'] 		= strip_tags( $new_instance['tickInterval'] );
		$instance['counter_image'] 		= $new_instance['counter_image'];
		$instance['bg_color'] 			= $new_instance['bg_color'];
		$instance['counter_color'] 		= $new_instance['counter_color'];
		$instance['label_color'] 		= $new_instance['label_color'];
		$instance['intro'] 				= $new_instance['intro'];
		$instance['outro'] 				= $new_instance['outro'];
		$instance['header'] 			= $new_instance['header'];
		$instance['footer'] 			= $new_instance['footer'];
		$instance['toggle_active'] 		= $new_instance['toggle_active'];
		
		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 * @since 0.6.0
	 */
	function form( $instance ) {

		// Set up the default form values
		// date-time: mm jj aa hh mn
		$defaults = array(
			'title' 			=> esc_attr__( 'Countdown', $this->textdomain ),
			'title_icon'		=> '',
			'counter' 			=> 'until',
			'until' 			=> array( 0 => date('m'), 1 => date('j'), 2 => date('Y'), 3 => 16, 4 => 53 ),
			'cLabels' 			=> array( 0 => 'Years', 1 => 'Months', 2 => 'Weeks', 3 => 'Days', 4 => 'Hours', 5 => 'Minutes', 6 => 'Seconds' ),
			'cLabels1' 			=> array( 0 => 'Year', 1 => 'Month', 2 => 'Week', 3 => 'Day', 4 => 'Hour', 5 => 'Minute', 6 => 'Second' ),
			'compactLabels' 	=> array( 0 => 'y', 1 => 'm', 2 => 'w', 3 => 'd' ),
			'format' 			=> 'dHMS',
			'expiryUrl' 		=> '',
			'expiryText' 		=> '',
			'alwaysExpire' 		=> false,
			'compact' 			=> false,
			'onExpiry' 			=> '',
			'onTick' 			=> '',
			'tickInterval' 		=> 1,
			'bg_color' 			=> '#f6f7f6',
			'counter_image' 	=> '',
			'counter_color' 	=> '#444444',
			'label_color' 		=> '#444444',
			'intro' 		=> '',
			'outro' 		=> '',
			'header' 		=> '',
			'footer' 		=> '',
			'toggle_active'		=> array( 0 => true, 1 => false, 2 => false, 3 => false, 4 => false, 5 => false )
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );

		$tabs = array( 
			__( 'General', $this->textdomain ),  
			__( 'Format', $this->textdomain ),
			__( 'Advanced', $this->textdomain ),
			__( 'Custom', $this->textdomain ),
			__( 'Upgrade', $this->textdomain ),
			__( 'Support', $this->textdomain )
		);
		
		// Set the default value of each widget input
		global $wp_locale;
		$time_adj = current_time('timestamp');
		$counterList = array( 'until' => __( 'Until', $this->textdomain) , 'since' => __( 'Since', $this->textdomain  ));
		?>

		<div class="pluginName">The Countdown<span class="pluginVersion"><?php echo THE_COUNTDOWN_VERSION; ?></span></div>

		<div id="tcp-<?php echo $this->id ; ?>" class="total-options tabbable tabs-left">
			<ul class="nav nav-tabs">
				<?php foreach ($tabs as $key => $tab ) : ?>
					<li class="<?php echo $instance['toggle_active'][$key] ? 'active' : '' ; ?>"><?php echo $tab; ?><input type="hidden" name="<?php echo $this->get_field_name( 'toggle_active' ); ?>[]" value="<?php echo $instance['toggle_active'][$key]; ?>" /></li>
				<?php endforeach; ?>							
			</ul>
			<ul class="tab-content">
				<li class="tab-pane <?php if ( $instance['toggle_active'][0] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', $this->textdomain ); ?></label>
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
						</li>						
						<li>
							<div id ="until-<?php echo $this->id; ?>" class="curtime tc-curtime">
								<label><?php _e( 'Date Picker', $this->textdomain ); ?></label>
								<select class="smallfat" id="<?php echo $this->get_field_id( 'counter' ); ?>" name="<?php echo $this->get_field_name( 'counter' ); ?>">
									<?php foreach ( $counterList as $option_value => $option_label ) { ?>
										<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['counter'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
									<?php } ?>
								</select>
								<span class="timestamp"><span><?php echo $wp_locale->get_month_abbrev( $wp_locale->get_month( $instance['until'][0] ) ) . ' ' . $instance['until'][1] . ', ' . $instance['until'][2] . ' @ ' . $instance['until'][3] . ':' . $instance['until'][4]; ?></span></span>
								<a tabindex="4" class="edit-timestamp hide-if-no-js" href="#"><?php _e( 'Edit', $this->textdomain ); ?></a>
								<div class="hide-if-js timestampdiv">
									<div class="timestamp-wrap">
										<?php
											$month = "<select class='mm' name='" . $this->get_field_name( 'until' ) . "[]'>";
											for ( $i = 1; $i < 13; $i = $i +1 ) {
												$monthnum = zeroise($i, 2);
												$month .= "\t\t\t" . '<option value="' . $monthnum . '"';
												if ( $i == $instance['until'][0] )
													$month .= ' selected="selected"';
												/* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
												$month .= '>' . sprintf( __( '%1$s-%2$s' ), $monthnum, $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) ) . "</option>\n";
											}
											$month .= '</select>';
											echo $month;
										?>
										<input type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo $instance['until'][1]; ?>" name="<?php echo $this->get_field_name( 'until' ); ?>[]" class="jj" />, 
										<input type="text" autocomplete="off" tabindex="4" maxlength="4" size="4" value="<?php echo $instance['until'][2]; ?>" name="<?php echo $this->get_field_name( 'until' ); ?>[]" class="aa" /> @ 
										<input type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo $instance['until'][3]; ?>" name="<?php echo $this->get_field_name( 'until' ); ?>[]" class="hh"> : 
										<input type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo $instance['until'][4]; ?>" name="<?php echo $this->get_field_name( 'until' ); ?>[]" class="mn">

										<a class="save-timestamp hide-if-no-js button" href="#"><?php _e( 'OK', $this->textdomain ); ?></a>
										<a class="cancel-timestamp hide-if-no-js" href="#"><?php _e( 'Cancel', $this->textdomain ); ?></a>
									</div>
									
									<input type="hidden" value="11" name="ss" class="ss" />
									<input type="hidden" value="<?php echo esc_attr( $instance['until']['0'] ); ?>" name="hidden_mm" class="hidden_mm">
									<input type="hidden" value="<?php echo gmdate( 'd', $time_adj ); ?>" name="cur_mm" class="cur_mm">
									<input type="hidden" value="<?php echo esc_attr( $instance['until']['1'] ); ?>" name="hidden_jj" class="hidden_jj">
									<input type="hidden" value="<?php echo gmdate( 'm', $time_adj ); ?>" name="cur_jj" class="cur_jj">
									<input type="hidden" value="<?php echo esc_attr( $instance['until']['2'] ); ?>" name="hidden_aa" class="hidden_aa">
									<input type="hidden" value="<?php echo gmdate( 'Y', $time_adj ); ?>" name="cur_aa" class="cur_aa">
									<input type="hidden" value="<?php echo esc_attr( $instance['until']['3'] ); ?>" name="hidden_hh" class="hidden_hh">
									<input type="hidden" value="<?php echo gmdate( 'h', $time_adj ); ?>" name="cur_hh" class="cur_hh">
									<input type="hidden" value="<?php echo esc_attr( $instance['until']['4'] ); ?>" name="hidden_mn" class="hidden_mn">
									<input type="hidden" value="<?php echo gmdate( 'i', $time_adj ); ?>" name="cur_mn" class="cur_mn">
								</div>
								<span class="description"><?php _e( "new Date(year, mth - 1, day, hr, min, sec) - date/time to count up from or numeric for seconds offset, or string for unit offset(s): 'Y' years, 'O' months, 'W' weeks, 'D' days, 'H' hours, 'M' minutes, 'S' seconds.", $this->textdomain ); ?></span>							
							</div>	
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'expiryUrl' ); ?>"><?php _e( 'Expiry Url', $this->textdomain ); ?></label>
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'expiryUrl' ); ?>" name="<?php echo $this->get_field_name( 'expiryUrl' ); ?>" value="<?php echo esc_attr( $instance['expiryUrl'] ); ?>" />
							<span class="description"><?php _e( 'A URL to load upon expiry, replacing the current page', $this->textdomain ); ?></span>	
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'expiryText' ); ?>"><?php _e( 'Expiry Text', $this->textdomain ); ?></label>
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'expiryText' ); ?>" name="<?php echo $this->get_field_name( 'expiryText' ); ?>" value="<?php echo esc_attr( $instance['expiryText'] ); ?>" />
							<span class="description"><?php _e( 'Text to display upon expiry, replacing the countdown', $this->textdomain ); ?></span>	
						</li>
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][1] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id( 'format' ); ?>"><?php _e( 'Date Format', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Format for display - upper case for always, lower case only if non-zero, \'Y\' years, \'O\' months, \'W\' weeks, \'D\' days, \'H\' hours, \'M\' minutes, \'S\' seconds', $this->textdomain ); ?></span>	
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'format' ); ?>" name="<?php echo $this->get_field_name( 'format' ); ?>" value="<?php echo esc_attr( $instance['format'] ); ?>" />							
						</li>					
						<li>
							<label><?php _e( 'Countdown Labels', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'The display texts for the counters', $this->textdomain ); ?></span>
							<table>
								<tr>
									<td><span class="description"><?php _e( 'Years', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels' ); ?>[]" value="<?php echo $instance['cLabels'][0]; ?>" /></td>
									<td class="separator"></td>
									<td><span class="description"><?php _e( 'Year', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels1' ); ?>[]" value="<?php echo $instance['cLabels1'][0]; ?>" /></td>
								</tr>
								<tr>
									<td><span class="description"><?php _e( 'Months', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels' ); ?>[]" value="<?php echo $instance['cLabels'][1]; ?>" /></td>
									<td class="separator"></td>
									<td><span class="description"><?php _e( 'Month', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels1' ); ?>[]" value="<?php echo $instance['cLabels1'][1]; ?>" /></td>
								</tr>
								<tr>
									<td><span class="description"><?php _e( 'Weeks', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels' ); ?>[]" value="<?php echo $instance['cLabels'][2]; ?>" /></td>
									<td class="separator"></td>
									<td><span class="description"><?php _e( 'Week', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels1' ); ?>[]" value="<?php echo $instance['cLabels1'][2]; ?>" /></td>
								</tr>
								<tr>
									<td><span class="description"><?php _e( 'Days', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels' ); ?>[]" value="<?php echo $instance['cLabels'][3]; ?>" /></td>
									<td class="separator"></td>
									<td><span class="description"><?php _e( 'Day', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels1' ); ?>[]" value="<?php echo $instance['cLabels1'][3]; ?>" /></td>
								</tr>
								<tr>
									<td><span class="description"><?php _e( 'Hours', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels' ); ?>[]" value="<?php echo $instance['cLabels'][4]; ?>" /></td>
									<td class="separator"></td>
									<td><span class="description"><?php _e( 'Hour', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels1' ); ?>[]" value="<?php echo $instance['cLabels1'][4]; ?>" /></td>
								</tr>
								<tr>
									<td><span class="description"><?php _e( 'Minutes', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels' ); ?>[]" value="<?php echo $instance['cLabels'][5]; ?>" /></td>
									<td class="separator"></td>
									<td><span class="description"><?php _e( 'Minute', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels1' ); ?>[]" value="<?php echo $instance['cLabels1'][5]; ?>" /></td>
								</tr>
								<tr>
									<td><span class="description"><?php _e( 'Seconds', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels' ); ?>[]" value="<?php echo $instance['cLabels'][6]; ?>" /></td>
									<td class="separator"></td>
									<td><span class="description"><?php _e( 'Second', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels1' ); ?>[]" value="<?php echo $instance['cLabels1'][6]; ?>" /></td>
								</tr>
							</table>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'compactLabels' ); ?>"><?php _e( 'Compact Labels', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'The compact texts for the counters', $this->textdomain ); ?></span>
							<table>
								<tr>
									<td><span class="description"><?php _e( 'Year', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'compactLabels' ); ?>[]" value="<?php echo $instance['compactLabels'][0]; ?>" /></td>
								</tr>
								<tr>
									<td><span class="description"><?php _e( 'Month', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'compactLabels' ); ?>[]" value="<?php echo $instance['compactLabels'][1]; ?>" /></td>
								</tr>
								<tr>
									<td><span class="description"><?php _e( 'Week', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'compactLabels' ); ?>[]" value="<?php echo $instance['compactLabels'][2]; ?>" /></td>
								</tr>
								<tr>
									<td><span class="description"><?php _e( 'Day', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'compactLabels' ); ?>[]" value="<?php echo $instance['compactLabels'][3]; ?>" /></td>
								</tr>
							</table>							
						</li>
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][2] ) : ?>active<?php endif; ?>">
					<ul>					
						<li>
							<label for="<?php echo $this->get_field_id( 'alwaysExpire' ); ?>">
							<input class="checkbox" type="checkbox" <?php checked( $instance['alwaysExpire'], true ); ?> id="<?php echo $this->get_field_id( 'alwaysExpire' ); ?>" name="<?php echo $this->get_field_name( 'alwaysExpire' ); ?>" /><?php _e( 'Always Expire', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Check if you want to trigger onExpiry even if never counted down.', $this->textdomain ); ?></span>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'compact' ); ?>">
							<input class="checkbox" type="checkbox" <?php checked( $instance['compact'], true ); ?> id="<?php echo $this->get_field_id( 'compact' ); ?>" name="<?php echo $this->get_field_name( 'compact' ); ?>" /><?php _e( 'Compact Version', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'True to display in a compact format, false for an expanded one.', $this->textdomain ); ?></span>
						</li>	
						<li>
							<label for="<?php echo $this->get_field_id('onExpiry'); ?>"><?php _e( 'On Expiry', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Callback when the countdown expires, receives no parameters and \'this\' is the containing division.', $this->textdomain ); ?></span>
							<textarea name="<?php echo $this->get_field_name( 'onExpiry' ); ?>" id="<?php echo $this->get_field_id( 'onExpiry' ); ?>" rows="2" class="widefat"><?php echo esc_textarea($instance['onExpiry']); ?></textarea>
							
						</li>
						<li>
							<label for="<?php echo $this->get_field_id('onTick'); ?>"><?php _e( 'On Tick', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Callback when the countdown is updated, receives int[7] being the breakdown by period (based on format) and \'this\' is the containing division', $this->textdomain ); ?></span>
							<textarea name="<?php echo $this->get_field_name( 'onTick' ); ?>" id="<?php echo $this->get_field_id( 'onTick' ); ?>" rows="2" class="widefat"><?php echo esc_textarea($instance['onTick']); ?></textarea>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'tickInterval' ); ?>"><?php _e( 'Tick Interval', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Interval (seconds) between onTick callbacks', $this->textdomain ); ?></span>
							<input type="text" class="smallfat" id="<?php echo $this->get_field_id( 'tickInterval' ); ?>" name="<?php echo $this->get_field_name( 'tickInterval' ); ?>" value="<?php echo esc_attr( $instance['tickInterval'] ); ?>" />							
						</li>
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][3] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id('intro'); ?>"><?php _e( 'Intro Text', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'This option will display addtional text before the widget content and supports HTML.', $this->textdomain ); ?></span>
							<textarea name="<?php echo $this->get_field_name( 'intro' ); ?>" id="<?php echo $this->get_field_id( 'intro' ); ?>" rows="2" class="widefat"><?php echo esc_textarea( $instance['intro'] );?></textarea>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id('outro'); ?>"><?php _e( 'Outro Text', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'This option will display addtional text after widget and supports HTML.', $this->textdomain ); ?></span>
							<textarea name="<?php echo $this->get_field_name( 'outro' ); ?>" id="<?php echo $this->get_field_id( 'outro' ); ?>" rows="2" class="widefat"><?php echo esc_textarea( $instance['outro'] );?></textarea>
						</li>	
						<li>
							<label for="<?php echo $this->get_field_id('header'); ?>"><?php _e( 'Header', $this->textdomain );?></label>
							<span class="description"><?php _e( 'Print custom scripts or styles to the front page header.', $this->textdomain ); ?></span>
							<textarea name="<?php echo $this->get_field_name( 'header' ); ?>" id="<?php echo $this->get_field_id( 'header' ); ?>" rows="3" class="widefat"><?php echo htmlentities($instance['header']); ?></textarea>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id('footer'); ?>"><?php _e( 'Footer', $this->textdomain );?></label>
							<span class="description"><?php _e( 'Print custom scripts or styles to the front page header.', $this->textdomain ); ?></span>
							<textarea name="<?php echo $this->get_field_name( 'footer' ); ?>" id="<?php echo $this->get_field_id( 'footer' ); ?>" rows="3" class="widefat"><?php echo htmlentities($instance['footer']); ?></textarea>
						</li>				
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][4] ) : ?>active<?php endif; ?>">
					<ul>
						<li>							
							<p>
								<a href="http://goo.gl/jYQoM"><img class="tcimg" src="<?php echo THE_COUNTDOWN_URL . 'img/plugin.png'; ?>" alt="" /></a>
								Upgrade to <a target="_blank" href="http://goo.gl/jYQoM">The Countdown Pro</a> for more plugin options and customizations.
							<p>
							<p><label>Full Supports</label><span class="description">You will get full and easy supports for this plugin.</span></p>				
							<p><label>Custom Counter Background Color and Image</label><span class="description">Easy to use custom background color. Will use the default image if the post has no thumbnail attached.</span></p>							
							<p><label>Counter Color & Custom Layouts</label><span class="description">3 predifined custom layout, right thumbnail, left thumbnail or block thumbnail</span></p>							
							<p><label>Shortcode</label><span class="description">With content shortcode and tinyMCE button.</span></p>														
							<p><label>Content Manipulation</label><span class="description">Easy to create content manipulation to the content.</span></p>							
							<p><label>Custom Style & Script</label><span class="description">Easy to add your custom style and script for each selector.</span></p>														
							<p><label>Plugin Updates</label><span class="description">Notification for every available update.</span></p>
							<p><label>Plugin Addons</label>
								<span class="description">
									<a href="http://codecanyon.net/item/post-expiration-the-countdown-pro-addon/3509836?ref=zourbuth">Post Expiration</a> Modify your content with expiration mode. <br />
									<a href="http://goo.gl/Hzmgf">Post Recycle</a> (Free) – Cycling your posts content or build <strong>recurring events</strong>
								</span>
							</p>							
							<p><label>And Many More</label>
								<span class="description">
									- Lightbox for <a href="http://zourbuth.com/plugins/the-countdown-pro/?page_id=20">Vimeo</a> and <a href="http://zourbuth.com/plugins/the-countdown-pro/?page_id=15">Youtube</a><br />
									- Lightbox for <a href="http://zourbuth.com/plugins/the-countdown-pro/?page_id=34">Inline Content</a>, <a href="http://zourbuth.com/plugins/the-countdown-pro/?page_id=24">External Link</a> or <a href="http://zourbuth.com/plugins/the-countdown-pro/?page_id=39">Single Image</a><br />
									- <a href="http://zourbuth.com/plugins/the-countdown-pro/?page_id=44">Hide Content</a> after x seconds<br />
									- <a href="http://zourbuth.com/plugins/the-countdown-pro/?page_id=47">Show Content</a> after x seconds<br />
									- <a href="http://zourbuth.com/plugins/the-countdown-pro/?page_id=54">Redirect</a> to other site after x seconds<br />
									- Widget title icon<br />
								</span>
							</p>
							<style type="text/css">
								.total-options p {
									margin-top:0;
								}
								.tcimg { 
									border: 1px solid #DDDDDD;
									border-radius: 2px 2px 2px 2px;
									float: right;
									padding: 4px;
									margin-left: 8px;
								}
								.tcimg:hover { 
									border: 1px solid #cccccc;
								}
								.wp-core-ui .btnremium { 
									border-color: #CCCCCC;
									height: auto;
									margin-top: 9px;
									padding-bottom: 0;
									padding-right: 0;
								}
								.wp-core-ui .btnremium span {
									background: none repeat scroll 0 0 #FFFFFF;
									border-left: 1px solid #F2F2F2;
									display: inline-block;
									font-size: 18px;
									line-height: 25px;
									margin-left: 9px;
									padding: 0 9px;
									border-radius: 0 3px 3px 0;
								}
							</style>
							Visit the <a target="_blank" href="http://zourbuth.com/plugins/the-countdown-pro/"><strong>live preview</strong></a> page.<br />
							<a class="button btnremium" target="_blank" href="http://goo.gl/jYQoM">Upgrade to Pro!<span>$6</span></a>
						</li>
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][4] ) : ?>active<?php endif; ?>">
					<ul>
						<li>							
							<p>
								This plugin does not match to your site style? Is this script not quite working as it should? 
								Having trouble installing? Or need some custom modifications that aren’t already included? 
								Or you want more features on next release? Feel free to get in touch about any of your queries by
								following or sending me tweets:<br /><br />
								<a href="https://twitter.com/zourbuth" class="twitter-follow-button" data-size="large" data-show-count="false" data-lang="en">Follow @zourbuth</a>
								<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>								
							<p>
						</li>
					</ul>
				</li>
			</ul>
		</div>	
	<?php
	}
}

?>