<?php
/*
    The Countdown 1.1
    http://zourbuth.com/plugins/the-countdown
    Copyright 2011  zourbuth.com  (email : zourbuth@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/**
 * The countdown main function
 * Set up the default form values
 * date-time: mm jj aa hh mn
 * @return
 * @since 1.0
**/
function the_countdown( $args ) {

	$defaults = array(
		'id' 				=> '',
		'title' 			=> esc_attr__( 'Countdown', 'the-countdown' ),
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
		'toggle_active'		=> array( 0 => true, 1 => false, 2 => false, 3 => false, 4 => false ),
		'intro_text' 		=> '',
		'outro_text' 		=> '',
		'customstylescript'	=> ''
	);

	/* Merge the user-selected arguments with the defaults. */
	$instance = wp_parse_args( (array) $args, $defaults );
	
	extract($instance, EXTR_SKIP);
	//print_r($defaults);
	return "<div id='countdown-$id'></div>";
}


/**
 * Get the custom styles/script for each meta for further use 
 * Using wp_head hook to push this function to the head
 * @return
 * @since 1.0
**/
add_action( 'wp_enqueue_scripts', 'the_countdown_wp_head' );

function the_countdown_wp_head() {
	$id   = get_the_ID();
	$meta = get_post_meta($id, 'the_countdown', true);

	if ( isset( $meta['callback'] ) && !empty( $meta['callback'] ) ) {
		
		switch ( $meta['callback'] ) {
			
			case 'lightbox':
				wp_enqueue_script( 'jquery' );
				wp_enqueue_style ( 'prettyPhoto', THE_COUNTDOWN_URL . 'css/prettyPhoto.css' );
				wp_enqueue_script( 'the-countdown', THE_COUNTDOWN_URL . 'js/jquery.countdown.min.js' );
				wp_enqueue_script( 'prettyPhoto', THE_COUNTDOWN_URL . 'js/jquery.prettyPhoto.js' );
				add_action( 'wp_head', 'the_countdown_callback_wp_head', 99 );
				add_filter( 'the_content', 'the_countdown_add_content' );
			break;
				
			case 'hide-content':
			case 'show-content':
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'the-countdown', THE_COUNTDOWN_URL . 'js/jquery.countdown.min.js' );
				add_action( 'wp_head', 'the_countdown_callback_wp_head', 99 );
				add_filter( 'the_content', 'the_countdown_add_content' );
			break;
			
			case 'redirect':
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'the-countdown', THE_COUNTDOWN_URL . 'js/jquery.countdown.min.js' );
				add_action( 'wp_head', 'the_countdown_callback_wp_head', 99 );
			break;
		
		}
	}
}


/**
 * Push additional HTML content to the content for some purposes.
 * Using the_content filter to push this content after the content
 * @return
 * @since 1.0
**/
function the_countdown_add_content($content) {
	$id   = get_the_ID();
	$meta = get_post_meta($id, 'the_countdown', true);
	
	if ( isset( $meta['html'] ) )
		return '<div id="' . $id . 'content">' . $content . '</div><div id="' . $id . 'html" class="hide">' . $meta['html'] . '</div>';
	else
		return '<div id="' . $id . 'content">' . $content . '</div>';
}


/**
 * Push additional HTML content to the content for some purposes.
 * Using the_content filter to push this content after the content
 * @return
 * @since 1.0
**/
function the_countdown_callback_wp_head() {
	$id   = get_the_ID();
	$meta = get_post_meta($id, 'the_countdown', true);

	$callback = $meta['callback'];
	
	switch ( $callback ) {		
		
		case 'lightbox':	
			/* var selector, count = 2,
			countdown = setInterval(function(){
				if ( count == 0 ) {
					clearInterval(countdown);
					$(selector).prettyPhoto();
					$.prettyPhoto.open("http://vimeo.com/8245346", "Title", "Description");
				}
				console.log(count);
				count--;
			}, 1000);	 */
			
			$interval = isset($meta['interval']) ? $meta['interval'] : 5;
			$title = isset($meta['title']) ? $meta['title'] : '';
			$description = isset($meta['description']) ? $meta['description'] : '';
			
			// Check if link is set, if not use the html id for the tag id, if not stop process
			if ( isset( $meta['link'] ) && !empty ( $meta['link'] ) )
				$content = $meta['link'];
			else
				$content = '#' . $id . 'html';
				
			echo '<script type="text/javascript">
					jQuery(document).ready(function($){
						var selector, current = new Date(); 
						current.setSeconds(current.getSeconds() + ' . $interval . '); 
						$(document).countdown({
							until: current,
							onExpiry: function() {
								$(selector).prettyPhoto();
								$.prettyPhoto.open("' . $content . '", "' . $title . '", "' . $description . '");
							}
						}); 				
					});
				  </script>';
		break;  
		  
		case 'hide-content':
			$interval = isset($meta['interval']) ? $meta['interval'] : 10;				
			echo '<script type="text/javascript">
					jQuery(document).ready(function($){
						var selector, current = new Date(); 
						current.setSeconds(current.getSeconds() + ' . $interval . '); 
						$(document).countdown({
							until: current,
							onExpiry: function() {
								$("#' . $id . 'content").fadeOut();
								$("#' . $id . 'html").fadeIn();
							}
						});		
					});
				  </script>';
		break;
		
		case 'show-content':
			$interval = isset($meta['interval']) ? $meta['interval'] : 10;				
			echo '<script type="text/javascript">
					jQuery(document).ready(function($){
						$("#' . $id . 'content").hide();
						$("#' . $id . 'html").show();
						var selector, current = new Date(); 
						current.setSeconds(current.getSeconds() + ' . $interval . '); 
						$(document).countdown({
							until: current,
							onExpiry: function() {
								$("#' . $id . 'content").fadeIn();
								$("#' . $id . 'html").fadeOut();
							}
						});		
					});
				  </script>';
		break;
		
		case 'redirect':
			$interval = isset($meta['interval']) ? $meta['interval'] : 10;				
			$link = isset($meta['link']) ? $meta['link'] : '#';				
			echo '<script type="text/javascript">
					jQuery(document).ready(function($){
						var selector, current = new Date(); 
						current.setSeconds(current.getSeconds() + ' . $interval . '); 
						$(document).countdown({
							until: current,
							expiryUrl: "' . $link . '"
						});
					});
				  </script>';
		break;
		
	}	
}
?>