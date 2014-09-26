<?php namespace Gears\Arrays;
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

use Illuminate\Support\Arr as LaravelArr;

/*
 * NOTE: The main reason we have extended the Laravel Arr class is because we
 * need to swap out ```is_array()``` for our function ```isArrayLike()``` in a
 * few spots.
 */

class Arr extends LaravelArr
{
	/**
	 * Method: set
	 * =========================================================================
	 * Sets a nested element in the array.
	 * This is compatible with the Laravel method here:
	 * 
	 *     \Illuminate\Support\Arr::set()
	 * 
	 * However to make it work with our Fluent API we need to make a slight
	 * tweak. While we were at it we made it possible to supply an array of
	 * keys, instead of "dot" separated keys.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $array - The array we are going to be working on.
	 * $keys  - A single key, or "dot" separated keys or an array of keys.
	 * $value - The value to set.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * The last array we created, this does not return the original array.
	 */
	public static function set(&$array, $keys, $value)
	{
		// Do we actually have a key, if not replace the entire array.
		if (is_null($keys)) return $array = $value;

		// Have we been given a string for the keys, if so explode it.
		if (is_string($keys)) $keys = explode('.', $keys);

		// Better add in a check to make sure the keys are indeed an array.
		if (!is_array($keys)) throw new \Exception('Must be an array!');

		// Loop through the keys
		while (count($keys) > 1)
		{
			// Grab the next key
			$key = array_shift($keys);

			// Here is our change. Note the new isArrayLike function.
			if (!isset($array[$key]) || !isArrayLike($array[$key]))
			{
				$array[$key] = array();
			}

			// Continue deeper into the array
			$array =& $array[$key];
		}

		// Set the value
		$array[array_shift($keys)] = $value;

		// Return the last array we created
		return $array;
	}

	/**
	 * Method: get
	 * =========================================================================
	 * Retrieves a nested element from an array or $default if it doesn't exist.
	 * This is compatible with the Laravel method here:
	 * 
	 *     Illuminate\Support\Arr::get()
	 * 
	 * However this version has a slight improvement. It allows you to supply an
	 * array of keys instead of the "dot" separated keys, just makes it slightly
	 * more flexible.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $array - The array to search for your key
	 * $keys  - The key path as either an array or a dot-separated string
	 * $default - An optional default to return when the key doesn't exist.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * mixed
	 */
	public static function get($array, $keys, $default = null)
	{
		// Return the entire array
		if (is_null($keys)) return $array;

		// We found the key in the root of the array
		if (isset($array[$keys])) return $array[$keys];

		// Have we been given a string for the keys
		if (is_string($keys)) $keys = explode('.', $keys);
		
		// Better add in a check to make sure the keys are indeed an array.
		if (!is_array($keys)) throw new \Exception('Must be an array!');

		// Loop through the keys
		foreach ($keys as $key)
		{
			// Check to see if the key exists
			if (!isArrayLike($array) || !array_key_exists($key, $array))
			{
				// Bail out and return the default.
				// NOTE: value() is a laravel helper.
				return value($default);
			}

			// Keep recursing into the array
			$array = $array[$key];
		}
		
		// Return the value
		return $array;
	}

	/**
	 * Method: forget
	 * =========================================================================
	 * Remove one or many array items from a given array using "dot" notation.
	 * 
	 * *Just like set and get above we have modded this method to allow the keys
	 * to be supplied as an array or "dot" separated keys.*
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $array - A reference to the array to work on.
	 * $keys - The key path as either an array or a dot-separated string.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public static function forget(&$array, $keys)
	{
		$original =& $array;

		foreach ((array) $keys as $key)
		{
			if (!is_array($key))
			{
				$parts = explode('.', $key);
			}
			else
			{
				$parts = $key;
			}

			while (count($parts) > 1)
			{
				$part = array_shift($parts);

				if (isset($array[$part]) && isArrayLike($array[$part]))
				{
					$array =& $array[$part];
				}
			}

			unset($array[array_shift($parts)]);

			// clean up after each pass
			$array =& $original;
		}
	}

	/**
	 * Method: dot
	 * =========================================================================
	 * Flatten a multi-dimensional associative array with dots.
	 * 
	 * *The only thing we have changed is the call to isArrayLike.*
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $array - The array to work with.
	 * $prepend - A string to place at the start of each key.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * array
	 */
	public static function dot($array, $prepend = '')
	{
		$results = array();

		foreach ($array as $key => $value)
		{
			if (isArrayLike($value))
			{
				$results = array_merge
				(
					$results,
					static::dot($value, $prepend.$key.'.')
				);
			}
			else
			{
				$results[$prepend.$key] = $value;
			}
		}

		return $results;
	}

	/**
	 * Method: fetch
	 * =========================================================================
	 * Fetch a flattened array of a nested array element.
	 * 
	 * All we have done here is make the method accept an array of keys.
	 * We didn't need to make any other changes so while the below is perhaps
	 * a little less efficient I feel it's better in the long run from a
	 * compatibility perspective.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $array - The array to work on.
	 * $key - Either "dot" separated or an array of keys.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * array
	 */
	public static function fetch($array, $key)
	{
		if (is_array($key)) $key = implode('.', $key);
		return parent::fetch($array, $key);
	}
}