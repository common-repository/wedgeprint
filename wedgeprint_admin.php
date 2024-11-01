<?php  

    if ( defined( 'WPCOM_API_KEY' ) )
		$wpcom_api_key = constant( 'WPCOM_API_KEY' );
	else
		$wpcom_api_key = '';

	if ( ! function_exists( 'add_action' ) ) {
		echo "Hi there!  I'm just a plugin, don't tickle me!";
		exit;
	}
	
	//check the user allowed role...
	if( ! current_user_can( 'administrator' ) ){
		echo "You shouldn't be here...";
		exit;
	}
	/**
	 * if there is something to save...
	 */
    if( isset($_POST['wdgp_act_hid'] ) && $_POST['wdgp_act_hid'] === 'S' && 
		 isset($_POST['wdgp_sts_inp'] ) ){
		
			//check nounce
			check_admin_referer( 'wedgeprint-manager_option_settings_status' );
			
			//****Status option*******
			$correct = true;
			$warning_message = "";	
			//get status option	
			$status = $_POST['wdgp_sts_inp'];
			
			//checking status value is ok
			if( $status !== 'on' && $status !== 'off' ) {
				//if not, display message and continue.
		?>  
				<div class="error"><p><strong><?php _e( 'Status unknown. ' ); ?></strong></p></div>  
		<?php		
			
				$correct = false;
			} else {	
				//if status is known, update option		
				update_option( 'wedgeprint_status' , $status );
			}		
			//we finally set wedge_time value for displaying
			$wedge_status = $status;
			
			// and the other options...
			$warning_message = "";			
			$wedge_source = "";
			$wedge_name = "";
			$wedge_comment = "";
			$wedge_closeid = "_cancel_";
			$wedge_time = get_option( 'wedgeprint_time' );
			$wedge_interval = get_option( 'wedgeprint_interval' );
			$wedge_selected = get_option( 'wedgeprint_selected' );
				
		} else if( isset($_POST['wdgp_act_hid'] ) && $_POST['wdgp_act_hid'] === 'G' &&
					isset( $_POST['wdgp_itv_inp'] ) && isset( $_POST['wdgp_tme_inp'] ) ) {
			
			//check nounce
			check_admin_referer( 'wedgeprint-manager_option_settings_general' );
			
			//****Idle option*******
			$correct = true;
			$warning_message = "";	
			//get the value for idle interval option
			$interval = $_POST['wdgp_itv_inp'];
			//checking interval value is ok
			if( ! is_numeric( $interval ) || intval( $interval ) <= 0 || intval( $interval ) > 99 ) {
				//if not, display message and continue.
		?>  
				<div class="error"><p><strong><?php _e( 'Idle interval out of range. ' ); ?></strong></p></div>  
		<?php 
				$correct = false;
			} else {	
				//if idle interval is in range, update option		
				update_option( 'wedgeprint_interval' , $interval );
			}		
			//we finally set wedge_interval value for displaying
			$wedge_interval = $interval;
			
			
			//****Time option*******
			
			//get the value for time option
			$time = $_POST['wdgp_tme_inp'];
			//checking time value is ok
			if( ! is_numeric( $time ) || intval( $time ) <= 0 || intval( $time ) > 9999 ) {
				//if not, display message and continue.
		?>  
				<div class="error"><p><strong><?php _e( 'Time out of range. ' ); ?></strong></p></div>  
		<?php 
				$correct = false;
			} else {	
				//if time is in range, update option		
				update_option( 'wedgeprint_time' , $time );
			}		
			//we finally set wedge_time value for displaying
			$wedge_time = $time;
			
			//add the other options
			$warning_message = "";			
			$wedge_source = "";
			$wedge_name = "";
			$wedge_comment = "";
			$wedge_closeid = "_cancel_";
			$wedge_status = get_option( 'wedgeprint_status' );
			$wedge_selected = get_option( 'wedgeprint_selected' );
			
		} else if( isset($_POST['wdgp_act_hid'] ) && $_POST['wdgp_act_hid'] === 'C' && 
					isset( $_POST['wdgp_src_inp'] ) && isset( $_POST['wdgp_nme_inp'] ) &&
					isset( $_POST['wdgp_cmm_inp'] ) && isset( $_POST['wdgp_cid_inp'] ) ) {
			
			//check nounce
			check_admin_referer( 'wedgeprint-manager_option_settings_create' );
			
			//****Source option*******
			$correct = true;
			$warning_message = "";	
			//we set wedge_source for display
			$wedge_source = "";
			$wedge_name = "";
			$wedge_comment = "";
			$wedge_closeid = "_cancel_";
			//source needle 
			$home_url = home_url( '/' );
			//set a warning message just in case
			$warning_message = "";
			//	
				$source = $_POST['wdgp_src_inp'];
				$name = $_POST['wdgp_nme_inp'];
				$comment = $_POST['wdgp_cmm_inp'];
				$closeid = $_POST['wdgp_cid_inp'];
				// check if source is acceptable...
				if( preg_match( '/[^a-zA-Z_\-0-9]/i', $name) ) {
					
			?>
					<div class="error"><p><strong><?php _e( 'Wedge name only allows alphanumeric characters and "_","-".' ); ?></strong></p></div> 
			<?php 
					//cannot allow non-alphanumeric characters basically
					$correct = false;
					$wedge_name = $name;
					$wedge_source = $source;
					$wedge_comment = $comment;
					$wedge_closeid = $closeid;
				echo $name;
					//search in wedges - option
				} else if(stripos(get_option( 'wedgeprint_wedges' ), $name . ';') !== false ) {
					
			?>
					<div class="error"><p><strong><?php _e( 'There is already a Wedge named "' . $name . '".' ); ?></strong></p></div> 
			<?php 
					//wedge names must be uniques
					$correct = false;
					$wedge_name = $name;
					$wedge_source = $source;
					$wedge_comment = $comment;
					$wedge_closeid = $closeid;
									
				} else if( preg_match( '/[^a-zA-Z_\-0-9]/i', $closeid ) ) {
					
			?>
					<div class="error"><p><strong><?php _e( 'Wedge close id only allows alphanumeric characters and "_","-".' ); ?></strong></p></div> 
			<?php 
					//cannot allow non-alphanumeric characters basically
					$correct = false;
					$wedge_name = $name;
					$wedge_source = $source;
					$wedge_comment = $comment;
					$wedge_closeid = $closeid;
				
				} else if( preg_match( '/[^a-zA-Z_\-0-9 ]/i', $comment ) ) {
					
			?>
					<div class="error"><p><strong><?php _e( 'Wedge comments only allows alphanumeric characters, spaces and "_","-".' ); ?></strong></p></div> 
			<?php 
					//cannot allow non-alphanumeric characters basically
					$correct = false;
					$wedge_name = $name;
					$wedge_source = $source;
					$wedge_comment = $comment;
					$wedge_closeid = $closeid;
				
				} else if( $source ===  '' || strlen( $source ) == 0  || stripos( $source , $home_url ) === false ||
					strpos( $source, $home_url ) > 0 ) { 
			?>
					<div class="error"><p><strong><?php _e( 'A correct source within your blog home url ( "' . $home_url . '" ) is required.' ); ?></strong></p></div> 
			<?php 
					$correct = false;
										
					//source would link a page outside the blog domain. The wedge will not be displayed correctly
					$warning_message = "Remember not to link pages from other sites. The wedge will not be correctly displayed!";
					$wedge_name = $name;
					$wedge_source = $source;
					$wedge_comment = $comment;
					$wedge_closeid = $closeid;
					
				
				} else {
					//if source seems to be loosely correct, add to database
					global $wpdb;
					$wedgeprint_wedges_table = $wpdb->prefix . "wedgeprint_wedges";
					$wpdb->query( $wpdb->prepare("INSERT INTO $wedgeprint_wedges_table ( wedgeprint_wdg_name, wedgeprint_wdg_source,
												wedgeprint_wdg_closeid, wedgeprint_wdg_comment )
												VALUES ( %s, %s, %s, %s )", 
												$name, esc_url_raw( $source ), $closeid, $comment 
												)
								);		 
					$wedge_name = "";
					$wedge_source = "";
					$wedge_comment = "";
					$wedge_closeid = "_cancel_";
					}
	
				$wedge_time = get_option( 'wedgeprint_time' );
				$wedge_interval = get_option( 'wedgeprint_interval' );
				$wedge_selected = get_option( 'wedgeprint_selected' );
			//	}
				//get the other options

				//new wedge code ends here
				
			}else if( isset($_POST['wdgp_act_hid'] ) && $_POST['wdgp_act_hid'] === 'M' ){
				
				$correct = true;
				$warning_message = "";
				
				if( isset($_POST['wdgp_id_hid'] ) && isset($_POST['wdgp_mac_hid_' . urldecode( $_POST['wdgp_id_hid'] ) ] ) ){
				
					//get the hidden fields	
					$id = urldecode( $_POST['wdgp_id_hid'] );
					$maction = $_POST['wdgp_mac_hid_' . urldecode( $_POST['wdgp_id_hid'] ) ];
					//check nounce
					check_admin_referer( 'wedgeprint-manager_option_settings_' . $id );				
					
					if( $maction == 'delete' ){
						//if exists
						if( stripos(get_option( 'wedgeprint_wedges' ), $id . ';') > -1 )	{
							//want to delete a wedge
							global $wpdb;
							$wedgeprint_wedges_table = $wpdb->prefix . "wedgeprint_wedges";
							$wpdb->query( $wpdb->prepare("DELETE FROM $wedgeprint_wedges_table WHERE  wedgeprint_wdg_name = %s", $id ) );
						}
					} else if( $maction == 'select' ){
						//if exists
						if( stripos(get_option( 'wedgeprint_wedges' ), $id . ';') > -1 )	{
							//want to select a wedge
							global $wpdb;
							$wedgeprint_wedges_table = $wpdb->prefix . "wedgeprint_wedges";
							$new_source_n = $wpdb->get_row( "SELECT wedgeprint_wdg_source, wedgeprint_wdg_closeid FROM $wedgeprint_wedges_table WHERE wedgeprint_wdg_name = '$id'", ARRAY_A );
							update_option( 'wedgeprint_source', $new_source_n['wedgeprint_wdg_source'] );
							update_option( 'wedgeprint_closeid', $new_source_n['wedgeprint_wdg_closeid'] );
							update_option( 'wedgeprint_selected', $id );
						}
					}
				//add the other options
				$warning_message = "";			
				$wedge_source = "";
				$wedge_name = "";
				$wedge_comment = "";
				$wedge_closeid = "_cancel_";
				$wedge_status = get_option( 'wedgeprint_status' );
				$wedge_time = get_option( 'wedgeprint_time' );
				$wedge_interval = get_option( 'wedgeprint_interval' );
				$wedge_selected = get_option( 'wedgeprint_selected' );
					
					
					
				} 
				//wedge maintenance ends	
			
			//if not any of the above cases... just go on.
		} else {
		
			//just show saved options...
			$correct = false;
			$warning_message = "";			
			$wedge_source = "";
			$wedge_name = "";
			$wedge_comment = "";
			$wedge_closeid = "_cancel_";
			$wedge_time = get_option( 'wedgeprint_time' );
			$wedge_status = get_option( 'wedgeprint_status' );
			$wedge_interval = get_option( 'wedgeprint_interval' );
			$wedge_selected = get_option( 'wedgeprint_selected' );
	 }
	 
		//gets the wedges into an array
		global $wpdb;
		$wedgeprint_wedges_table = $wpdb->prefix . "wedgeprint_wedges";
		$wedges_rows = $wpdb->get_results( "SELECT * FROM $wedgeprint_wedges_table WHERE 1 = 1 ORDER BY wedgeprint_wdg_tmstmp ASC", ARRAY_A );
		
		//turns off the status when no Wedge is selected
			if( get_option( 'wedgeprint_selected' ) === '' || get_option( 'wedgeprint_source' ) === '' ||
				get_option( 'wedgeprint_wedges' ) === '' ){
					//turn off 
					update_option( 'wedgeprint_status', 'off' );
					$wedge_status = get_option( 'wedgeprint_status' );
				}
					
       if( $correct ) {
				//if everithing went ok... display a nice message.
		?>  
            <div class="updated"><p><strong><?php _e( 'Options saved.' ); ?></strong></p></div>  
        <?php 
			
			}
		//the options form:
		?>
	
    <div class="wrap">  
        <?php echo "<h2>" . __( 'WedgePrint Manager' ) . "</h2>"; ?>  
	<hr />  
	<form name="wdgp_admin_form" method="post" action="">
		<?php
			if ( function_exists('wp_nonce_field') ) {
				wp_nonce_field('wedgeprint-manager_option_settings_status');
			}
		?> 
        <input type="hidden" name="wdgp_act_hid" value="S" /> 
        <p/>
        <p><h3 style="color: #369;"><em><?php echo _e( 'Power Manager' ) ?></em></h3></p> 
        <p><span><strong><?php _e("Status: " ); ?></strong></span>&nbsp;<input type="radio" <?php echo ($wedge_status==='on'?'checked="checked"':''); ?> name="wdgp_sts_inp" value="on" />&nbsp;<?php _e("On" ); ?>&nbsp;<input type="radio" name="wdgp_sts_inp" <?php echo ($wedge_status!=='on'?'checked="checked"':'')?> value="off" />&nbsp;<?php _e("Off" ); ?></p>
        <p><?php _e(" Temporary disable the plugin's functionality without reseting the options. " ); ?></p>
        <p><?php _e(" You should turn it off while working on this page, in order to avoid malfunctioning. " ); ?></p>
        <p><?php _e(" Status will allways turn automatically off when no Wedge is selected" ); ?></p>
        <p><input type="submit" class="button-primary" name="Submit" value="<?php _e( 'Update Status' ) ?>" /></p>
        <hr />
        </form>
        <form name="wdgp_admin_form" method="post" action="">
		<?php
			if ( function_exists('wp_nonce_field') ) {
				wp_nonce_field('wedgeprint-manager_option_settings_general');
			}
		?> 
		<input type="hidden" name="wdgp_act_hid" value="G" />
		<p/>
        <p><h3 style="color: #369;"><em><?php echo _e( 'General Settings' ) ?></em></h3></p> 
        <p><strong><?php _e("Idle interval: " ); ?></strong>&nbsp;<input type="text" name="wdgp_itv_inp" value="<?php echo $wedge_interval; ?>" maxlength="2" size="5"><?php _e(" Range: 1 - 99 (minutes)." ); ?></p> 
        <p><?php _e(" Time interval (minutes) in which no wedge will be shown to the same reader (must have 'cookies enabled')." ); ?></p>
        <p><strong><?php _e("Showing time: " ); ?></strong>&nbsp;<input type="text" name="wdgp_tme_inp" value="<?php echo $wedge_time; ?>" maxlength="4" size="7"><?php _e(" Range: 1 - 9999 (miliseconds)." ); ?></p> 
        <p><?php _e(" Wedge runtime (miliseconds). " ); ?></p>
        <p><input type="submit" class="button-primary" name="Submit" value="<?php _e( 'Update Settings' ) ?>" /></p>
        <hr />
        </form>
        <form name="wdgp_admin_form" method="post" action="">
		<?php
			if ( function_exists('wp_nonce_field') ) {
				wp_nonce_field('wedgeprint-manager_option_settings_create');
			}
		?>
		<input type="hidden" name="wdgp_act_hid" value="C" />
        <p><h3 style="color: #369;"><em><?php echo _e( 'New Wedge' ) ?></em></h3></p>
        <p><strong><?php _e("Name: " ); ?></strong>&nbsp;<input type="text" name="wdgp_nme_inp" value="<?php echo $wedge_name; ?>" maxlength="50" size="70"></p>
        <p><?php _e(" Local identifier for your wedge (only alphanumeric characters)." ) ?></p>
        <p><strong><?php _e("Source: " ); ?></strong>&nbsp;<input type="text" name="wdgp_src_inp" value="<?php echo $wedge_source; ?>" maxlength="250" size="100"></p>
        <p><?php _e(" Web accessible url within your blog (example: http://www.mydomain.com/myblog/mywedge.html). Please avoid 'ugly' characters." ) ?></p>
        <p><strong><?php _e("Comment: " ); ?></strong>&nbsp;<input type="text" name="wdgp_cmm_inp" value="<?php echo $wedge_comment; ?>" maxlength="100" size="100"></p>
        <p><?php _e(" Add extra information about this Wedge." ) ?></p>
        <p><strong><?php _e( "Close Element ID: " ); ?></strong>&nbsp;<input type="text" name="wdgp_cid_inp" value="<?php echo $wedge_closeid; ?>" maxlength="24" size="30"></p>
        <p><?php _e( "Yes, you can now choose your favorite id for the Wedge 'close' functionality!. Remember... must be clickeable within its page!" ) ?></p>
        <p><span style="color: #903;"><strong><?php echo $warning_message; ?></strong></span></p>
        <p><input type="submit" class="button-primary" name="Submit" value="<?php _e( 'Create Wedge' ) ?>" /></p>
        <hr />
        </form>
        <p><h3 style="color: #369;"><em><?php echo _e( 'Wedges' ) ?></em></h3></p>
        <p/>
        <div style="position:relative;" id="wedges_div">
        <?php
			//create an string with all wedges-names
			$wedgeprint_wedges = "";
			$wedgeprint_script_test = "<script type='text/javascript'>(function(){f=document.createElement('script');f.type='text/javascript';f.src='" . urlencode( plugin_dir_url( __FILE__ ) . 'test/wedge_test.php' ) ."';f.async = true;f.defer='defer';document.getElementsByTagName('head')[0].appendChild(f);})();</script>";
			//starts the wedge recovery
			if( $wedges_rows ) {
				foreach( $wedges_rows as $wedge ){
		?>		        <form name="wdgp_admin_form" method="post" action="">
		<?php
				if ( function_exists('wp_nonce_field') ) {
						wp_nonce_field('wedgeprint-manager_option_settings_' . trim( $wedge['wedgeprint_wdg_name'] ) );
					}
		?> 
					<input type="hidden" name="wdgp_act_hid" value="M" />
					<input type="hidden" name="wdgp_id_hid" value="<?php echo urlencode( $wedge['wedgeprint_wdg_name'] ) ?>" />
					<input type="hidden" name="wdgp_mac_hid_<?php echo urlencode( $wedge['wedgeprint_wdg_name'] ) ?>" value="" id="wdgp_mac_hid_<?php echo urlencode( $wedge['wedgeprint_wdg_name'] ) ?>"/>
		<?php
					//fill the wedges - option for searching wedges
					$wedgeprint_wedges = $wedgeprint_wedges . $wedge['wedgeprint_wdg_name'] . ';';
					if( $wedge['wedgeprint_wdg_name'] ===  $wedge_selected ) {
						if( get_option( 'wedgerpint_closeid' ) !== $wedge['wedgeprint_wdg_closeid'] ){
							update_option( 'wedgeprint_closeid', $wedge['wedgeprint_wdg_closeid'] );
						}
						if( get_option( 'wedgeprint_source' ) !== $wedge['wedgeprint_wdg_source'] ){
							update_option( 'wedgeprint_source', $wedge['wedgeprint_wdg_source'] );
						}
		?>	
					<span style="font-weight: bold;color: #093;">&nbsp;&nbsp;&gt;&gt;&nbsp;&nbsp;</span><span style="font-weight: bold;color: #369;"><?php echo $wedge['wedgeprint_wdg_name'] ?></span><span style="font-weight: bold;color: #093;">&nbsp;&nbsp;&lt;&lt;&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">cancel_id=&nbsp;&quot;<?php echo $wedge['wedgeprint_wdg_closeid'] ?>&quot;</span>&nbsp;&nbsp;&nbsp;<span style="font-weight: bold;color: #369;">&quot;<?php echo $wedge['wedgeprint_wdg_comment'] ?>&quot;</span><br/>
					<span style="color: #369;"><?php echo esc_url( $wedge['wedgeprint_wdg_source'] ) ?></span><br/>
					<p/> 
        <?php 		
					} else {
		?> 
					<span style="font-weight: bold;"><?php echo $wedge['wedgeprint_wdg_name'] ?></span>&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">cancel_id=&nbsp;&quot;<?php echo  $wedge['wedgeprint_wdg_closeid'] ?>&quot;</span>&nbsp;&nbsp;&nbsp;<span style="font-weight: bold;">&quot;<?php echo $wedge['wedgeprint_wdg_comment'] ?>&quot;</span><br/>
					<span><?php echo  esc_url( $wedge['wedgeprint_wdg_source'] ) ?></span><br/>
					<input type="submit" class="button" name="delete" onclick="document.getElementById('wdgp_mac_hid_<?php echo urlencode( $wedge['wedgeprint_wdg_name'] ) ?>').value = 'delete';" value="<?php _e( 'Delete' ) ?>" />
					<input type="submit" class="button" name="select" onclick="document.getElementById('wdgp_mac_hid_<?php echo urlencode( $wedge['wedgeprint_wdg_name'] ) ?>').value = 'select';" value="<?php _e( 'Select' ) ?>" />&nbsp;
					<p/>
        <?php
					} 
		?>     
			</form>
			<p/>
		<?php
				}	
			}
			//save the wedges name options
			update_option ('wedgeprint_wedges', $wedgeprint_wedges );
		 ?>
		 </div>
        <hr />
        </p>   
 </div> 

