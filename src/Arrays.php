<?php namespace Gears;
////////////////////////////////////////////////////////////////////////////////
// __________ __             ________                   __________              
// \______   \  |__ ______  /  _____/  ____ _____ ______\______   \ _______  ___
//  |     ___/  |  \\____ \/   \  ____/ __ \\__  \\_  __ \    |  _//  _ \  \/  /
//  |    |   |   Y  \  |_> >    \_\  \  ___/ / __ \|  | \/    |   (  <_> >    < 
//  |____|   |___|  /   __/ \______  /\___  >____  /__|  |______  /\____/__/\_ \
//                \/|__|           \/     \/     \/             \/            \/
// -----------------------------------------------------------------------------
//          Designed and Developed by Brad Jones <brad @="bjc.id.au" />         
// -----------------------------------------------------------------------------
////////////////////////////////////////////////////////////////////////////////

use \Illuminate\Support\Traits\MacroableTrait;
use \Gears\Arrays\Exceptions\InvalidMethod;

class Arrays
{
	/*
	 * Make this compatiable with the Laravel Arr class.
	 * That way we can easily swap in our version in a Laravel App.
	 */
	use MacroableTrait
	{
		__callStatic as __macroCallStatic;
	}
	
	/**
	 * Method: a
	 * =========================================================================
	 * This is the static factory method allowing a syntax like this:
	 * 
	 * 	Arr::a($array)->each(function($k, $v){ echo $k.$v; });
	 * 
	 * NOTE: Unlike the Gears\String class we were unable to combine both APIs
	 * into the one class. This was mainly due to the fact that some of the
	 * array functions need variables to be passed by reference and when using
	 * the PHP magic methods __call and __callStatic, refrences can not be
	 * passed through.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $array - A PHP array to turn into a Gears\Arrays\Fluent object
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * A new instance of Gears\Arrays\Fluent object
	 */
	public static function a($array)
	{
		return new \Gears\Arrays\Fluent($array);
	}

	/**
	 * Method: __callStatic
	 * =========================================================================
	 * This provides a static API. As of PHP 5.5 we can't import functions from
	 * different name spaces. In PHP 5.6 we can. So this is the next best thing.
	 * 
	 * For example compare this:
	 * 
	 *     \Gears\Arrays\pull([1,2,3], 1);
	 * 
	 * To this:
	 * 
	 *     use Gears\Arrays as Arr;
	 *     Arr::pull([1,2,3], 1);
	 * 
	 * NOTE: Static calls like this will return the exact output from the
	 * underlying function. So you can't do method chaining, etc.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $name - The name of the \Gears\Arrays\"FUNCTION" to call.
	 * $arguments - The arguments to pass to the function.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * mixed
	 */
	public static function __callStatic($name, $arguments)
	{
		// Create the function name
		$func_name = '\Gears\Arrays\\'.$name;

		// Does the function exist
		if (!function_exists($func_name))
		{
			// Try a macro
			if (self::hasMacro($name))
			{
				return self::__macroCallStatic($name, $arguments);
			}

			// Bail out, we don't have a function to run
			throw new InvalidMethod($name);
		}

		// Call the function
		return call_user_func_array($func_name, $arguments);
	}

	/*
	 * The following are a few special cases. These methods expect a reference
	 * to be passed to the array that they act on. Where as the methods handled
	 * by the magic __callStatic method above take an array, manipulate it and
	 * return a completely new array or value.
	 */
	public static function pull(&$array, $key, $default = null) { return \Gears\Arrays\pull($array, $key, $default); }
	public static function set(&$array, $key, $value) { return \Gears\Arrays\set($array, $key, $value); }
	public static function forget(&$array, $keys) { return \Gears\Arrays\forget($array, $keys); }
	public static function getOrPut(&$array, $key, $default = null) { return \Gears\Arrays\getOrPut($array, $key, $default); }
	public static function values(&$array) { return \Gears\Arrays\values($array); }
	public static function transform(&$array, \Closure $callback) { return \Gears\Arrays\transform($array, $callback); }
	public static function splice(&$array, $offset, $length = 0, $replacement = array()) { return \Gears\Arrays\splice($array, $offset, $length, $replacement); }
	public static function sortBy(&$array, $callback, $options = SORT_REGULAR, $descending = false) { return \Gears\Arrays\sortBy($array, $callback, $options, $descending); }
	public static function sortByDesc(&$array, $callback, $options = SORT_REGULAR) { return \Gears\Arrays\sortByDesc($array, $callback, $options); }
	public static function shift(&$array) { return \Gears\Arrays\shift($array); }
	public static function put(&$array, $key, $value) { return \Gears\Arrays\put($array, $key, $value); }
	public static function push(&$array, $value) { return \Gears\Arrays\push($array, $value); }
	public static function prepend(&$array, $value) { return \Gears\Arrays\prepend($array, $value); }
	public static function pop(&$array) { return \Gears\Arrays\pop($array); }
	public static function sort(&$array, \Closure $callback) { return \Gears\Arrays\sort($array, $callback); }

	public static function unshift(&$array)
	{
		$arguments = [&$array];
		$args = func_get_args(); array_shift($args);
		foreach ($args as $arg) $arguments[] = $arg;
		return call_user_func_array('array_unshift', $arguments);
	}
}