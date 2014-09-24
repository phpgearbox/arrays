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

use \Illuminate\Support\Arr as LaravelArray;
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
			throw new InvalidMethod();
		}

		// Call the function
		return call_user_func_array($func_name, $arguments);
	}

	/*
	 * The following are a few speical cases. These methods expect a refrence
	 * to be passed to the array that they act on. Where as the methods handled
	 * by the magic __callStatic method above take an array, manipulate it and
	 * return a completely new array or value.
	 */
	public static function pull(&$array, $key, $default = null) { return LaravelArray::pull($array, $key, $default); }
	public static function set(&$array, $key, $value) { return LaravelArray::set($array, $key, $value); }
	public static function forget(&$array, $keys) { return LaravelArray::forget($array, $keys); }
	public static function getOrPut(&$array, $key, $default = null) { return \Gears\Arrays\getOrPut($array, $key, $default); }
}
