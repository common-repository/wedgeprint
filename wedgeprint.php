<?php
/* 
	Plugin Name: WedgePrint
	Plugin URI: http://www.wedgeprint.net 
	Description: WedgePrint plugin for WordPress is an easy way to show your readers short-time visual screenshots while loading your requested blog page.
	Author: Dweius 
	Version: 0.2.0 
	Author URI: http://www.wedgeprint.net/dweius/
	License: GPL2
*/  

/*  Copyright 2012  Dweius  (email : dweius@wedgeprint.net)

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


if ( defined( 'WPCOM_API_KEY' ) ) {
	$wpcom_api_key = constant( 'WPCOM_API_KEY' );
	
} else {
	$wpcom_api_key = '';
}

if ( ! function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, don't tickle me!";
	exit;
}

//*************** Main functionality ***************

function wedgeprint_action_script() {

	if( ! is_admin() ) {
		
		//exclude admin pages
		
		//prefix_string for the cookie
		$cookie_prefix_name = "wedgeprint_polite_cookie_";
		
		//need to include files from common/
		include_once  plugin_dir_path(__FILE__) . 'common/wedgeprint_inner.php';
		
		
		if( get_option( 'wedgeprint_cookie_time' ) < floor( time() / ( get_option ( 'wedgeprint_interval' ) * 60 ) ) ) {
		
			//if more than an hour has passed since...
			//change cookie name and update time -in hours-. 
			update_option( 'wedgeprint_cookie_name', generate_random( 12 ) );
			update_option( 'wedgeprint_cookie_time', floor( time() / ( get_option ( 'wedgeprint_interval' ) * 60 ) ) );
			
		}
		//All this stuff just to avoid cookie corruption from external end-users js-agents.
		//Ex.: taking a cookie and change its expiration date, or other 'non polite' actions ;)
		
		//get the saved cookie name
		$cookie_rand_name = get_option( 'wedgeprint_cookie_name' );
		//set cookie expiration time
		$cookie_time = time() + (get_option ( 'wedgeprint_interval' ) * 60);
		//construct cookie name as a concatenation
		$cookie_name = $cookie_prefix_name . $cookie_rand_name;
		//get saved cookie time -truncated at hours-.
		$cookie_time_value = get_option( 'wedgeprint_cookie_time' );
		//set the cookie value as a hash -some how it contains a random part-.
		$cookie_hash_value = sha1( $cookie_name . $cookie_time_value );
		//set the cookie name.
		$cookie_hash_name = $cookie_name;
	
		//check for cookies with the same name
		if ( ! isset( $_COOKIE[$cookie_hash_name] ) ) {
			//ok, there is none, we send it ours.
			setcookie( $cookie_hash_name, $cookie_hash_value, $cookie_time, COOKIEPATH, COOKIE_DOMAIN, false );
			//and run the script.
			//we place it under wp_footer() when possible
			if( function_exists( 'wp_footer' ) ){
				add_action( 'wp_footer', 'wedgeprint_script' );
			} else{
				//wp_footer() does not exists.
				add_action( 'wp_print_footer_scripts', 'wedgeprint_script' );
			}
		} else {
			//if we find an older cookie from ours...
			$cookie_actual_value = $_COOKIE[$cookie_hash_name];
			//if both values match, the cookie has been set within the same hour.
			if( $cookie_actual_value !== $cookie_hash_value ) {
				//if not, set the new cookie and run the script.
				setcookie( $cookie_hash_name, $cookie_hash_value, $cookie_time, COOKIEPATH, COOKIE_DOMAIN, false );
				if( function_exists( 'wp_footer' ) ){
					add_action( 'wp_footer', 'wedgeprint_script' );
				} else{
					//wp_footer() does not exists.
					add_action( 'wp_print_footer_scripts', 'wedgeprint_script' );
				}
			}
		}
	}
	//if isadmin... end of run.
}

//*************** Core script ***************

function wedgeprint_script() {

	//get the saved options...
	$wedge_time = get_option( 'wedgeprint_time' );
	$wedge_source = esc_url( get_option( 'wedgeprint_source' ) );
	$wedgeprint_closeid = get_option( 'wedgeprint_closeid' );
	//generate a random name to cancel the show...
	$wedgeprint_frame_id = generate_random( 12 );
	$wedgeprint_frameset_id = generate_random( 12 );
	$wedgeprint_script_id = generate_random( 12 );

	//the magic starts...
	echo "<script type='text/javascript' id='$wedgeprint_script_id'>(function(){fs = document.createElement('frameset'); fs.framespacing = '0px'; fs.rows='*'; fs.cols='*'; fs.id='$wedgeprint_frameset_id';
	 f=document.createElement('frame'); f.src= '$wedge_source';
	 f.frameBorder = '0'; f.scrolling = 'no'; f.noResize= 'noresize'; f.style.overflow = 'hidden'; f.id='$wedgeprint_frame_id';
	 fs.appendChild(f); c = document.getElementsByTagName('body')[0]; c.style.display='none'; c.parentNode.insertBefore(fs,c);
	 if (f.addEventListener){f.addEventListener('load',function (){var o = document.getElementById('$wedgeprint_frame_id'); var u = (o.contentWindow || o.contentDocument); var l = u.document.documentElement.ownerDocument.getElementById('$wedgeprint_closeid'); if (l) l.addEventListener('click',function(){ var g = window.top.document.getElementById('$wedgeprint_frameset_id'); window.top.document.getElementsByTagName('body')[0].style.display= 'block'; if(g) g.parentNode.removeChild(g);},false);},false);
	 }else{f.attachEvent('onload', function (){var o = document.getElementById('$wedgeprint_frame_id'); var u = (o.contentWindow || o.contentDocument); var l = u.document.documentElement.ownerDocument.getElementById('$wedgeprint_closeid'); if (l) l.attachEvent('onclick',function(){ var g = window.top.document.getElementById('$wedgeprint_frameset_id'); window.top.document.getElementsByTagName('body')[0].style.display= 'block'; if(g) g.parentNode.removeChild(g);})});};
	 setTimeout(" . '"' . "(function(){var g = window.top.document.getElementById('$wedgeprint_frameset_id'); window.top.document.getElementsByTagName('body')[0].style.display= 'block'; if(g) g.parentNode.removeChild(g); var s = document.getElementById('$wedgeprint_script_id'); if(s) s.parentNode.removeChild(s);})()" . '"' . ", $wedge_time);})();</script>";

	//yeah...fireworks!
	//;)
}

if( is_admin() ) {
	
	//adds admin page to the 'settings' menu.
	add_action( 'admin_menu', 'wedgeprint_admin_menu' );
	
}else {
	
	if( 'on' === get_option ( 'wedgeprint_status' ) ) {
		//adding the action to the head...
		add_action( 'sanitize_comment_cookies', 'wedgeprint_action_script' );
	}
}


//*************** Maintenance functions *********

include_once  plugin_dir_path(__FILE__) . 'wedgeprint_maintenance.php';

//de-/activation/uninstall procedure...

register_activation_hook( __FILE__, array( 'WedgePrintInit', 'on_activate' ) );

register_deactivation_hook( __FILE__, array( 'WedgePrintInit', 'on_deactivate' ) );


//*************** Admin function ***************

function wedgeprint_admin_menu() { 
		
		//only for blog administrators
		add_options_page( __('WedgePrint Manager'), __('WedgePrint Manager'), 'administrator', basename(__FILE__), 'wedgeprint_admin_options' );  
    }
     
function wedgeprint_admin_options() {
	
	//include path to admin php file
	include_once plugin_dir_path(__FILE__) .  'wedgeprint_admin.php' ;

}

?>
