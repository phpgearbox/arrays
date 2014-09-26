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

use \Gears\Arrays\Fluent;
use \Gears\Arrays\Exceptions\InvalidMethod;
use \Illuminate\Support\Arr as LaravelArray;

class Arrays extends LaravelArray
{
	/**
	 * Method: a
	 * =========================================================================
	 * This is the static factory method allowing a syntax like this:
	 * 
	 * ```php
	 * Arr::a([1,2,3])->add(4)->each(function($v){ echo $v; });
	 * ```
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $array - A PHP array to turn into a Gears\Arrays\Fluent object
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * A new instance of Gears\Arrays\Fluent object
	 */
	public static function a(array $array = array())
	{
		return Fluent::make($array);
	}

	/**
	 * Method: add
	 * =========================================================================
	 * We are overloading the parent add method so that we can accept
	 * either an array of keys or "dot" separated keys. This should stay
	 * compatiable with the upstream method.
	 * 
	 * For example the following 2 calls are the same:
	 * 
	 * ```php
	 * Arr::add([], 'a.b.c', 'd');
	 * Arr::add([], ['a','b','c'], 'd');
	 * ```
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $array - A PHP array to add items to.
	 * $key - Can be a single key, "dot" separated keys or an array of keys.
	 * $value - The value to set.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * array
	 */
	public static function add($array, $key, $value)
	{
		// this allows the key to be an array of keys
		if (isArrayLike($key)) $key = implode('.', $key);

		return parent::add($array, $key, $value);
	}

	/**
	 * Method: get
	 * =========================================================================
	 * We are overloading the parent get method so that we can accept
	 * either an array of keys or "dot" separated keys. This should stay
	 * compatiable with the upstream method.
	 * 
	 * For example the following 2 calls are the same:
	 * 
	 * ```php
	 * Arr::get(['a' => ['b' => ['c' => 'd']]], 'a.b.c');
	 * Arr::get(['a' => ['b' => ['c' => 'd']]], ['a','b','c']);
	 * ```
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $array - A PHP array to get items from.
	 * $key - Can be a single key, "dot" separated keys or an array of keys.
	 * $default - A value to return upon failure.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * mixed
	 */
	public static function get($array, $key, $default = null)
	{
		// this allows the key to be an array of keys
		if (isArrayLike($key)) $key = implode('.', $key);

		return parent::get($array, $key, $default);
	}

	/**
	 * Method: set
	 * =========================================================================
	 * We are overloading the parent set method so that we can accept
	 * either an array of keys or "dot" separated keys. This should stay
	 * compatiable with the upstream method.
	 * 
	 * For example the following 2 calls are the same:
	 * 
	 * ```php
	 * Arr::set([], 'a.b.c', 'd');
	 * Arr::set([], ['a','b','c'], 'd');
	 * ```
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $array - A refrence to the PHP array to set items in.
	 * $key - Can be a single key, "dot" separated keys or an array of keys.
	 * $value - The value to set.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * array
	 */
	public static function set(&$array, $key, $value)
	{
		// this allows the key to be an array of keys
		if (isArrayLike($key)) $key = implode('.', $key);

		return parent::set($array, $key, $value);
	}

	/**
	 * Method: forget
	 * =========================================================================
	 * We are overloading the parent forget method so that we can accept
	 * either an array of keys or "dot" separated keys. This should stay
	 * compatiable with the upstream method.
	 * 
	 * For example the following 2 calls are the same:
	 * 
	 * ```php
	 * Arr::forget(['a' => ['b' => ['c' => 'd']]], 'a.b.c');
	 * Arr::forget(['a' => ['b' => ['c' => 'd']]], ['a','b','c']);
	 * ```
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $array - A refrence to the PHP array to remove items from.
	 * $keys - Can be a single key, "dot" separated keys or an array of keys.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public static function forget(&$array, $keys)
	{
		// this allows the key to be an array of keys
		if (isArrayLike($keys))
		{
			$new_keys = [];

			foreach ($keys as $key)
			{
				if (isArrayLike($key))
				{
					$new_keys[] = implode('.', $key);
				}
				else
				{
					$new_keys[] = $key;
				}
			}

			$keys = $new_keys;
		}

		return parent::forget($array, $keys);
	}

	/**
	 * Method: fetch
	 * =========================================================================
	 * We are overloading the parent fetch method so that we can accept
	 * either an array of keys or "dot" separated keys. This should stay
	 * compatiable with the upstream method.
	 * 
	 * For example the following 2 calls are the same:
	 * 
	 * ```php
	 * Arr::fetch(['a' => ['b' => ['c' => 'd']]], 'a.b.c');
	 * Arr::fetch(['a' => ['b' => ['c' => 'd']]], ['a','b','c']);
	 * ```
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $array - A refrence to the PHP array to remove items from.
	 * $keys - Can be a single key, "dot" separated keys or an array of keys.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public static function fetch($array, $key)
	{
		// this allows the key to be an array of keys
		if (isArrayLike($key)) $key = implode('.', $key);

		return parent::fetch($array, $key);
	}

	/**
	 * Method: __callStatic
	 * =========================================================================
	 * The existing methods in the ```Illuminate\Support\Arr``` class
	 * are awesome. But there are some extra methods we would like to have
	 * access to.
	 * 
	 * This will first look for a function under our \Gears\Arrays
	 * namespace. If one is found we call that function for you, as if it
	 * were a static method of this class.
	 * 
	 * If we can't find a function there we then check for any macros.
	 * If a macro exists we hand control to our parent.
	 * 
	 * On failure of all that we finally throw an InvalidMethod Exception.
	 * 
	 * NOTE: Any functions which require a reference to be passed through will
	 * not work via __callStatic. As the magic method does not accept
	 * references. These functions need to be defined in this class as stubs.
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
				return parent::__callStatic($name, $arguments);
			}

			// Bail out, we don't have a function to run
			throw new InvalidMethod($name);
		}

		// Call the function
		return call_user_func_array($func_name, $arguments);
	}
}