<?php
if ( ! class_exists( 'WedgePrintInit' ) ) :
/**
 * This class triggers functions that run during activation/deactivation & uninstallation
 * 
 */
class WedgePrintInit {

    function __construct( $case = false ) {
		
		    if ( ! $case )
            wp_die( 'Busted! You should not call this class directly', 'Doing it wrong!' );

        switch( $case ) {
			
            case 'activate' :
                // add_action calls and else
                $this->activate_cb();
                break;

            case 'deactivate' : 
                // reset the options
                $this->deactivate_cb();
                break;

            case 'uninstall' : 
                // delete the tables
                $this->uninstall_cb();
                break;
                
            default : return;
        }
    }

    /**
     * Set up tables, add options, etc. - All preparation that only needs to be done once
     */
    function on_activate() {
		
        new WedgePrintInit( 'activate' );
    }

    /**
     * Do nothing like removing settings, etc. 
     * The user could reactivate the plugin and wants everything in the state before activation.
     * Take a constant to remove everything, so you can develop & test easier.
     */
    function on_deactivate() {
		
        new WedgePrintInit( 'deactivate' );
    }

    /**
     * Remove/Delete everything - If the user wants to uninstall, then he wants the state of origin.
     * 
     * Will be called when the user clicks on the uninstall link that calls for the plugin to uninstall itself
     */
    function on_uninstall() {
		
        // important: check if the file is the one that was registered with the uninstall hook (function)
        if ( __FILE__ != WP_UNINSTALL_PLUGIN )
            return;

        new WedgePrintInit( 'uninstall' );
    }

    function activate_cb() {

        //include common functions
        include_once  plugin_dir_path( __FILE__ ) . 'common/wedgeprint_inner.php' ;
        
        /**
         * we now add wedgeprint options by default
         */
        $upgrade = false;
		//***** add wedgeprint database and default options
		//setting variables 
		global $wpdb;
		global $wedgeprint_db_version;
		$wedgeprint_wedges_table = $wpdb->prefix . "wedgeprint_wedges";
		
		//contruct the sql sentence for wedges table
		$sql = "CREATE TABLE IF NOT EXISTS $wedgeprint_wedges_table (
				wedgeprint_wdg_name varchar(50) NOT NULL,
				wedgeprint_wdg_source varchar(250) NOT NULL,
				wedgeprint_wdg_tmstmp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				wedgeprint_wdg_type varchar(10) DEFAULT NULL,
				wedgeprint_wdg_header varchar(250) DEFAULT NULL,
				wedgeprint_wdg_options varchar(100) DEFAULT NULL,
				wedgeprint_wdg_closeid varchar(24) DEFAULT NULL,
				wedgeprint_wdg_comment varchar(100) DEFAULT NULL,
				PRIMARY KEY (wedgeprint_wdg_name) ) COMMENT='(c) 2012 - wedgeprint.net v-0.2.0';";
		
		//execute sql		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		//now we can update the db_version
		add_option( "wedgeprint_db_version", $wedgeprint_db_version );
		
		//set the default data
		//sample one
		$wedgeprint_wdg_name_one = 'classic-wedge-sample';
		$wedgeprint_wdg_source_one =  plugin_dir_url( __FILE__ ) . 'sample/wedgeprint_sample.html';
		$wedgeprint_wdg_type_one = '';
		$wedgeprint_wdg_header_one = '';
		$wedgeprint_wdg_options_one = '';
		$wedgeprint_wdg_closeid_one = '_cancel_';
		$wedgeprint_wdg_comment_one = 'This is the classic colorful Wedge';
		//sample two
		$wedgeprint_wdg_name_two = 'ya-wedge-sample';
		$wedgeprint_wdg_source_two =  plugin_dir_url( __FILE__ ) . 'sample/wedgeprint_static_sample.html';
		$wedgeprint_wdg_type_two = '';
		$wedgeprint_wdg_header_two = '';
		$wedgeprint_wdg_options_two = '';
		$wedgeprint_wdg_closeid_two = '_your_cancel_id_';
		$wedgeprint_wdg_comment_two = 'Yet another static color Wedge';

		if( get_option('wedgeprint_source') != null && get_option('wedgeprint_source') !== '' &&
				serialize_version( get_option( 'wedgeprint_version' ) == 1 ) ) {
			//sample default just in case...
			$wedgeprint_wdg_name_def = 'active-wedge';
			$wedgeprint_wdg_source_def =  get_option( 'wedgeprint_source' );
			$wedgeprint_wdg_type_def = '';
			$wedgeprint_wdg_header_def = '';
			$wedgeprint_wdg_options_def = '';
			$wedgeprint_wdg_closeid_def = '_cancel_';
			$wedgeprint_wdg_comment_def = 'Your active Wedge';
			
			//add first to db		
			//check if exists default wedge
			$wedge_def = $wpdb->get_row("SELECT * FROM $wedgeprint_wedges_table WHERE wedgeprint_wdg_name = '$wedgeprint_wdg_name_def'");
			if( $wedge_def === null ) {
			//it does not exist so we insert
			$wpdb->insert( $wedgeprint_wedges_table, array( 'wedgeprint_wdg_name' => $wedgeprint_wdg_name_def,
				'wedgeprint_wdg_source' => $wedgeprint_wdg_source_def, 'wedgeprint_wdg_type' => $wedgeprint_wdg_type_def, 
				'wedgeprint_wdg_header' => $wedgeprint_wdg_header_def, 'wedgeprint_wdg_options' => $wedgeprint_wdg_options_def, 
				'wedgeprint_wdg_comment' => $wedgeprint_wdg_comment_def, 'wedgeprint_wdg_closeid' => $wedgeprint_wdg_closeid_def  ) );
			$upgrade = true;
			}
		}
		//add to the db
		
		//check if exists default wedge
		$wedge_one = $wpdb->get_row("SELECT * FROM $wedgeprint_wedges_table WHERE wedgeprint_wdg_name = '$wedgeprint_wdg_name_one'");
		if( $wedge_one === null ) {
			//it does not exist so we insert
			$wpdb->insert( $wedgeprint_wedges_table, array( 'wedgeprint_wdg_name' => $wedgeprint_wdg_name_one,
				'wedgeprint_wdg_source' => $wedgeprint_wdg_source_one, 'wedgeprint_wdg_type' => $wedgeprint_wdg_type_one, 
				'wedgeprint_wdg_header' => $wedgeprint_wdg_header_one, 'wedgeprint_wdg_options' => $wedgeprint_wdg_options_one, 
				'wedgeprint_wdg_comment' => $wedgeprint_wdg_comment_one, 'wedgeprint_wdg_closeid' => $wedgeprint_wdg_closeid_one  ) );
		}
		//check if exists default wedge
		$wedge_two = $wpdb->get_row("SELECT * FROM $wedgeprint_wedges_table WHERE wedgeprint_wdg_name = '$wedgeprint_wdg_name_two'");
		if( $wedge_two === null ) {
			//it does not exist so we insert
			$wpdb->insert( $wedgeprint_wedges_table, array( 'wedgeprint_wdg_name' => $wedgeprint_wdg_name_two,
				'wedgeprint_wdg_source' => $wedgeprint_wdg_source_two, 'wedgeprint_wdg_type' => $wedgeprint_wdg_type_two, 
				'wedgeprint_wdg_header' => $wedgeprint_wdg_header_two, 'wedgeprint_wdg_options' => $wedgeprint_wdg_options_two, 
				'wedgeprint_wdg_comment' => $wedgeprint_wdg_comment_two, 'wedgeprint_wdg_closeid' => $wedgeprint_wdg_closeid_two ) );
		}
		


		//***** end of install default db data
		
		//*** we continue with options
		update_option ( 'wedgeprint_version', '0.2.0' );
		// add default display time
		if( get_option('wedgeprint_time') == null || get_option('wedgeprint_time') === '' ) {
			update_option( 'wedgeprint_time', '4999' );
		}
		// add the random portion of the cookie name
		update_option( 'wedgeprint_cookie_name', generate_random( 12 ) );
		//add default interval
		if( get_option( 'wedgerpint_interval' ) == null || get_option( 'wedgeprint_interval' ) === '' ){
			update_option( 'wedgeprint_interval', '60' );
		}
		// add cookie time in hours
		update_option( 'wedgeprint_cookie_time', floor( time() / ( get_option( 'wedgeprint_interval' ) * 60 ) ) );
		// add default status
		update_option( 'wedgeprint_status',  'off' );
		//add default selected wedge
		if( $upgrade )  {
			update_option( 'wedgeprint_selected', 'active-wedge' );
		}else if( get_option('wedgeprint_selected') == null || get_option('wedgeprint_selected') === '' ){
			update_option( 'wedgeprint_selected', 'classic-wedge-sample' );		
		}
		//add default source
		if( get_option('wedgeprint_source') == null || get_option('wedgeprint_source') === '' ) {
			update_option( 'wedgeprint_source', plugin_dir_url( __FILE__ ) . 'sample/wedgeprint_sample.html' );
		}
		//add default cancel/close id
		if( get_option('wedgeprint_closeid') == null || get_option('wedgeprint_closeid') === '' ) {
			update_option( 'wedgeprint_closeid', '_cancel_' );
		}
		//*** end of installing options
		
		//end of activate.

    }

    function deactivate_cb() {
		
		update_option( 'wedgeprint_status',  'off' );
    }

}
endif;

?>
