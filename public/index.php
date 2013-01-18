<?php
/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @version  3.2.13
 * @author   Taylor Otwell <taylorotwell@gmail.com>
 * @link     http://laravel.com
 */

// --------------------------------------------------------------
// Tick... Tock... Tick... Tock...
// --------------------------------------------------------------
define('LARAVEL_START', microtime(true));
# Comment out the following line to use regular (fat) Laravel
define('LARAVEL_LITE', true);

// --------------------------------------------------------------
// Indicate that the request is from the web.
// --------------------------------------------------------------
$web = true;

// --------------------------------------------------------------
// Set the core Laravel path constants.
// --------------------------------------------------------------
require '../paths.php';

// --------------------------------------------------------------
// Unset the temporary web variable.
// --------------------------------------------------------------
unset($web);

// --------------------------------------------------------------
// Launch Laravel.
// --------------------------------------------------------------
if ( defined('LARAVEL_LITE') ) {
    require path('sys').'laravel_boot.php';
}
else {
    require path('sys').'laravel.php';
}