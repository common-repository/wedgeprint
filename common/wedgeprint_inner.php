<?php
    if ( defined( 'WPCOM_API_KEY' ) )
		$wpcom_api_key = constant( 'WPCOM_API_KEY' );
	else
		$wpcom_api_key = '';

	if ( ! function_exists( 'add_action' ) ) {
		echo "Hi there!  I'm just a plugin, don't tickle me!";
		exit;
	}
/**
 * let us choose alphanumeric characters for random strings
 */
function generate_random( $length ) {
	$string_s = '';
	$allowed_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	$len = strlen( $allowed_chars ) - 1; 
	for ( $counter_i = 0; $counter_i < $length; $counter_i++ ) {
		$rand_char = rand( 0, $len );
		$string_s .= $allowed_chars{$rand_char};
	}
	return $string_s;
}

/**
 * lets serialize the wedgeprint versioning...
 */
function serialize_version( $version ){
	
	if( $version === '0.1.0' ) {
		//first release - beta
		return 1;
	} else if ( $version === '0.2.0' ) {
		//second release - beta
		return 2;
	} else  {
		//unknown
		return 0;
	}
	
}
?>
