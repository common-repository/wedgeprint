<?php


	if( !defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN') ){
		exit();
	}
	/**
	 *  ok, you want nothing from me... good bye!
	 */ 
	delete_option( 'wedgeprint_version' );
	/**
     * we now remove wedgeprint options.. too sad.
	 */
        
    delete_option( 'wedgeprint_time' );
	delete_option( 'wedgeprint_cookie_name' );
	delete_option( 'wedgeprint_cookie_time' );
	delete_option( 'wedgeprint_wedges' );
	delete_option( 'wedgeprint_status' );
	delete_option( 'wedgeprint_interval' );
	delete_option( 'wedgeprint_selected' );
	delete_option( 'wedgeprint_source' );
	delete_option( 'wedgeprint_closeid' );
	
	//drop database table 
	global $wpdb;
	$wedgeprint_wedges_table = $wpdb->prefix . "wedgeprint_wedges";
	
	//contruct the sql sentence for wedges table
	$sql = "DROP TABLE IF EXISTS $wedgeprint_wedges_table ;";
	
	//execute sql		
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$wpdb->query( $sql );
	
	//bye

?>
