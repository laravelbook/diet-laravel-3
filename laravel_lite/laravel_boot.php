<?php
/**
 * @package  Laravel 3 Lite Bootstrapper
 * @version  3.2.13
 * @author   Taylor Otwell <taylorotwell@gmail.com>
 * @link     http://laravel.com 
 * @author   Max Ehsan <contact@laravelbook.com>
 * @link     http://laravelbook.com/
 * 
 * Highly condensed and optimized version of Laravel 3 framework.
 * 
 * This file is the result of merging commonly used Laravel and Symfony 
 * class files with some extraneous components and comments stripped away.
 *
 * By using this file instead of laravel.php, an Laravel application may
 * improve performance due to the reduction of PHP parsing time.
 * The performance improvement will become especially obvious when PHP op-code 
 * caching engine such as the APC extension is enabled.
 *
 * DO NOT MODIFY THIS FILE MANUALLY!
 *
 */
/**
 * laravel\helpers.php
 */
function e($value)
{
	return HTML::entities($value);
}
function __($key, $replacements = array(), $language = null)
{
	return Lang::line($key, $replacements, $language);
}
function dd($value)
{
	echo "<pre>";
	var_dump($value);
	echo "</pre>";
	die;
}
function array_get($array, $key, $default = null)
{
	if (is_null($key)) return $array;
	foreach (explode('.', $key) as $segment) {
		if (!is_array($array) or !array_key_exists($segment, $array)) {
			return value($default);
		}
		$array = $array[$segment];
	}
	return $array;
}
function array_set(&$array, $key, $value)
{
	if (is_null($key)) return $array = $value;
	$keys = explode('.', $key);
	while (count($keys) > 1) {
		$key = array_shift($keys);
		if (!isset($array[$key]) or !is_array($array[$key])) {
			$array[$key] = array();
		}
		$array = & $array[$key];
	}
	$array[array_shift($keys) ] = $value;
}
function array_forget(&$array, $key)
{
	$keys = explode('.', $key);
	while (count($keys) > 1) {
		$key = array_shift($keys);
		if (!isset($array[$key]) or !is_array($array[$key])) {
			return;
		}
		$array = & $array[$key];
	}
	unset($array[array_shift($keys) ]);
}
function array_first($array, $callback, $default = null)
{
	foreach ($array as $key => $value) {
		if (call_user_func($callback, $key, $value)) return $value;
	}
	return value($default);
}
function array_strip_slashes($array)
{
	$result = array();
	foreach ($array as $key => $value) {
		$key = stripslashes($key);
		if (is_array($value)) {
			$result[$key] = array_strip_slashes($value);
		} else {
			$result[$key] = stripslashes($value);
		}
	}
	return $result;
}
function array_divide($array)
{
	return array(array_keys($array), array_values($array));
}
function array_pluck($array, $key)
{
	return array_map(function ($v) use ($key)
	{
		return is_object($v) ? $v->$key : $v[$key];
	}, $array);
}
function array_only($array, $keys)
{
	return array_intersect_key($array, array_flip((array)$keys));
}
function array_except($array, $keys)
{
	return array_diff_key($array, array_flip((array)$keys));
}
function eloquent_to_json($models)
{
	if ($models instanceof Laravel\Database\Eloquent\Model) {
		return json_encode($models->to_array());
	}
	return json_encode(array_map(function ($m)
	{
		return $m->to_array();
	}, $models));
}
function magic_quotes()
{
	return function_exists('get_magic_quotes_gpc') and get_magic_quotes_gpc();
}
function head($array)
{
	return reset($array);
}
function url($url = '', $https = null)
{
	return Laravel\URL::to($url, $https);
}
function asset($url, $https = null)
{
	return Laravel\URL::to_asset($url, $https);
}
function action($action, $parameters = array())
{
	return Laravel\URL::to_action($action, $parameters);
}
function route($name, $parameters = array())
{
	return Laravel\URL::to_route($name, $parameters);
}
function starts_with($haystack, $needle)
{
	return strpos($haystack, $needle) === 0;
}
function ends_with($haystack, $needle)
{
	return $needle == substr($haystack, strlen($haystack) - strlen($needle));
}
function str_contains($haystack, $needle)
{
	foreach ((array)$needle as $n) {
		if (strpos($haystack, $n) !== false) return true;
	}
	return false;
}
function str_finish($value, $cap)
{
	return rtrim($value, $cap) . $cap;
}
function str_object($value)
{
	return is_object($value) and method_exists($value, '__toString');
}
function root_namespace($class, $separator = '\\')
{
	if (str_contains($class, $separator)) {
		return head(explode($separator, $class));
	}
}
function class_basename($class)
{
	if (is_object($class)) $class = get_class($class);
	return basename(str_replace('\\', '/', $class));
}
function value($value)
{
	return (is_callable($value) and !is_string($value)) ? call_user_func($value) : $value;
}
function with($object)
{
	return $object;
}
function has_php($version)
{
	return version_compare(PHP_VERSION, $version) >= 0;
}
function view($view, $data = array())
{
	if (is_null($view)) return '';
	return Laravel\View::make($view, $data);
}
function render($view, $data = array())
{
	if (is_null($view)) return '';
	return Laravel\View::make($view, $data)->render();
}
function render_each($partial, array $data, $iterator, $empty = 'raw|')
{
	return Laravel\View::render_each($partial, $data, $iterator, $empty);
}
function yield($section)
{
	return Laravel\Section::yield($section);
}
function get_cli_option($option, $default = null)
{
	foreach (Laravel\Request::foundation()->server->get('argv') as $argument) {
		if (starts_with($argument, "--{$option}=")) {
			return substr($argument, strlen($option) + 3);
		}
	}
	return value($default);
}
function get_file_size($size)
{
	$units = array('Bytes', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB');
	return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $units[$i];
}
/**
 * laravel\vendor\Symfony\Component\HttpFoundation\Resources\stubs\SessionHandlerInterface.php
 */
// interface SessionHandlerInterface
// {
// 	public function open($savePath, $sessionName);
// 	public function close();
// 	public function read($sessionId);
// 	public function write($sessionId, $data);
// 	public function destroy($sessionId);
// 	public function gc($lifetime);
// }

require('symfony_lite.php');
require('laravel_lite.php');

if ( defined('SYMFONY_EXTRA') ) {
    require('symfony_extra.php');
}
if ( defined('LARAVEL_EXTRA') ) {
    require('laravel_extra.php');
}