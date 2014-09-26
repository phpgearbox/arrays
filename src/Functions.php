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

use \Closure;
use \Gears\Arrays as Arr;

/**
 * Function: isArrayLike
 * =============================================================================
 * This is our version of is_array. Because in our Fluent API we are dealing
 * with Objects that act like arrays we needed a way to detect these objects
 * as well as standard arrays.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $value - The value you think might be an array.
 * $strict - If set to true we test specifically for an array like object.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * bool
 */
function isArrayLike($value, $strict = false)
{
	if ($strict)
	{
		return ($value instanceof \ArrayAccess && $value instanceof \Traversable);
	}
	else
	{
		return is_array($value) ||
		(
			$value instanceof \ArrayAccess &&
			$value instanceof \Traversable
		);
	}
}

/**
 * Function: debug
 * =============================================================================
 * This will output an array for debug purposes.
 * If running from a web server we will wrap it in some pre tags for you.
 * And then we kill the script.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to output.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * void
 */
function debug($array)
{
	// are we running on the command line or in a browser
	if (php_sapi_name() == 'cli')
	{
		echo "\n\nARRAY DUMP:\n-----\n".print_r($array, true)."\n\n";
	}
	else
	{
		echo
			'<div style="text-align:left;">'.
				'<h1>ARRAY DUMP</h1><hr>'.
				'<pre>'.print_r($array, true).'</pre>'.
			'</div>'
		;
	}

	// kill php
	exit;
}

/**
 * Function: getOrElse
 * =============================================================================
 * Returns the value at the given index or $default if it not present
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to search
 * $key - The key to search for
 * $default - An optional default to return when the key doesn't exist.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function getOrElse($array, $key, $default = null)
{
	return array_key_exists($key, $array) ? $array[$key] : $default;
}

/**
 * Function: getOrPut
 * =============================================================================
 * Returns the value at the given index. If not present,
 * inserts $default and returns it
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to search / update.
 * $key - The key to search for
 * $default - An optional default to be inserted when the key doesn't exist.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function getOrPut(&$array, $key, $default = null)
{
	if (!array_key_exists($key, $array))
	{
		$array[$key] = $default;
	}
	
	return $array[$key];
}

/**
 * Function: searchRecursive
 * =============================================================================
 * This will search an array recursively for your search terms.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to search within.
 * $search - The search query.
 * $exact - If false search terms can simply appear inside the array values.
 * $trav_keys - Used recursively
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * An array of results or false if it can find anything
 */
function searchRecursive($array, $search, $exact = true, $trav_keys = null)
{
	// Check to make sure we have something to search for and search in.
	if(!is_array($array) || !$search || ($trav_keys && !is_array($trav_keys)))
	{
		return false;
	}
	
	// Create a Results array
	$results = [];
	
	// Loop through each value in the array
	foreach($array as $key => $val)
	{
		// Create a list of keys that we used to find the result
		$used_keys = $trav_keys ? array_merge($trav_keys, [$key]) : [$key];
		
		// Check for an exact hit on the key
		if ($key === $search)
		{
			$results[] =
			[
				'type' => "key",
				'hit' => $key,
				'keys' => $used_keys,
				'val' => $val
			];
		}
		
		// Can we match to non exact hits?
		elseif (!$exact)
		{
			// Check to see if the array key contains the search term
			if (strpos(strtolower($key), strtolower($search)) !== false)
			{
				$results[] =
				[
					'type' => "key",
					'hit' => $key,
					'keys' => $used_keys,
					'val' => $val
				];
			}
		}
		
		// Check to see if the value is another nested array
		if (is_array($val))
		{
			// Recursively call ourselves
			$children_results = searchRecursive($val, $search, $exact, $used_keys);
			if ($children_results)
			{
				$results = array_merge($results, $children_results);
			}
		}
		
		// Check for an exact hit on the value
		elseif ($val === $search)
		{
			$results[] =
			[
				'type' => "val",
				'hit' => $val,
				'keys' => $used_keys,
				'val' => $val
			];
		}
		
		// Can we match to non exact hits?
		elseif (!$exact)
		{
			// Check to see if the array key contains the search term
			if (strpos(strtolower($val), strtolower($search)) !== false)
			{
				$results[] =
				[
					'type' => "val",
					'hit' => $val,
					'keys' => $used_keys,
					'val' => $val
				];
			}
		}
	}
	
	// Return our results
	return $results ? $results : false;
}

/**
 * Function: takeWhile
 * =============================================================================
 * Returns longest prefix of elements that satisfy the $predicate.
 * The predicate will be passed value and key of each element.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to slice
 * $predicate - A function that will evaluate ($value, $key) -> bool
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function takeWhile($array, $predicate)
{
	$n = 0;
	
	foreach ($array as $key => $value)
	{
		if (!$predicate($value, $key)) break;
		++$n;
	}
	
	return array_slice($array, 0, $n);
}

/**
 * Function: dropWhile
 * =============================================================================
 * Drops longest prefix of elements satisfying $predicate and returns the rest.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to slice
 * $predicate - A function that will evaluate ($value, $key) -> bool
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function dropWhile($array, $predicate)
{
	$n = 0;
	
	foreach ($array as $key => $val)
	{
		if (!$predicate($val, $key)) break;
		++$n;
	}

	return array_slice($array, $n);
}

/**
 * Function: repeat
 * =============================================================================
 * Repeats the array $n times.
 * TODO: Convert to iterator to conserve memory and time
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to repeat
 * $n - How many times to repeat
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function repeat($array, $n)
{
	$result = array();
	
	while ($n-- > 0)
	{
		foreach ($array as $value)
		{
			$result[] = $value;
		}
	}

	return $result;
}

/**
 * Function: find
 * =============================================================================
 * Returns the first value of the array satisfying the $predicate or $default
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to search
 * $predicate - callable ($value, $key) -> bool
 * $default - An optional value to return when nothing is found
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function find($array, $predicate, $default = null)
{
	foreach ($array as $key => $value)
	{
		if ($predicate($value, $key))
		{
			return $value;
		}
	}
	
	return $default;
}

/**
 * Function: findLast
 * =============================================================================
 * Returns the last value of the array satisfying the $predicate or $default
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to search
 * $predicate - callable ($value, $key) -> bool
 * $default - An optional value to return when nothing is found
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function findLast($array, $predicate, $default = null)
{
	return find(array_reverse($array, true), $predicate, $default);
}

/**
 * Function: findKey
 * =============================================================================
 * Returns the first key satisfying the $predicate or null
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to search
 * $predicate - callable ($value, $key) -> bool
 * $default - An optional value to return when nothing is found
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function findKey($array, $predicate, $default = null)
{
	foreach ($array as $key => $value)
	{
		if ($predicate($value, $key))
		{
			return $key;
		}
	}
	
	return $default;
}

/**
 * Function: findLastKey
 * =============================================================================
 * Returns the last key satisfying the $predicate or null
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to search
 * $predicate - callable ($value, $key) -> bool
 * $default - An optional value to return when nothing is found
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function findLastKey($array, $predicate, $default = null)
{
	return findKey(array_reverse($array, true), $predicate, $default);
}

/**
 * Function: indexBy
 * =============================================================================
 * Re-indexes the array by either results of the callback or a sub-key
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to re-index
 * $callbackOrKey - A string or callable
 * $arrayAccess - Whether to use array or object access when given a key name
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function indexBy($array, $callbackOrKey, $arrayAccess = true)
{
	$indexed = array();
	
	if (is_string($callbackOrKey))
	{
		if ($arrayAccess)
		{
			foreach ($array as $element)
			{
				$indexed[$element[$callbackOrKey]] = $element;
			}
		}
		else
		{
			foreach ($array as $element)
			{
				$indexed[$element->{$callbackOrKey}] = $element;
			}
		}
	}
	else
	{
		foreach ($array as $element)
		{
			$indexed[$callbackOrKey($element)] = $element;
		}
	}
	
	return $indexed;
}

/**
 * Function: all
 * =============================================================================
 * Returns true if all elements satisfy the given predicate
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to test
 * $predicate - A callable to check each value
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * boolean
 */
function all($array, $predicate)
{
	foreach ($array as $key => $value)
	{
		if (!$predicate($value, $key))
		{
			return false;
		}
	}
	
	return true;
}

/**
 * Function: any
 * =============================================================================
 * Returns true if at least one element satisfies the given predicate
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to test
 * $predicate - A callable to check each value
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * boolean
 */
function any($array, $predicate)
{
	foreach ($array as $key => $value)
	{
		if ($predicate($value, $key))
		{
			return true;
		}
	}
	
	return false;
}

/**
 * Function: one
 * =============================================================================
 * Returns true if exactly one element satisfies the given predicate
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to test
 * $predicate - A callable to check each value
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * boolean
 */
function one($array, $predicate)
{
	return exactly($array, 1, $predicate);
}

/**
 * Function: none
 * =============================================================================
 * Returns true if none of the elements satisfy $predicate
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to test
 * $predicate - A callable to check each value
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * boolean
 */
function none($array, $predicate)
{
	return exactly($array, 0, $predicate);
}

/**
 * Function: exactly
 * =============================================================================
 * Returns true if exactly $n elements satisfy the $predicate
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to test
 * $n - The number of elements to match
 * $predicate - A callable to check each value
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * boolean
 */
function exactly($array, $n, $predicate)
{
	$found = 0;
	
	foreach ($array as $key => $value)
	{
		if ($predicate($value, $key))
		{
			if (++$found > $n)
			{
				return false;
			}
		}
	}
	
	return $found == $n;
}

/**
 * Function: filterWithKey
 * =============================================================================
 * Keeps only those elements that satisfy the $predicate
 * Differs from array_filter() in that the key of each element
 * is also passed to the predicate.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to filter
 * $predicate - callable($value, $key) -> bool
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function filterWithKey($array, $predicate)
{
	$result = array();
	
	foreach ($array as $key => $value)
	{
		if ($predicate($value, $key))
		{
			$result[$key] = $value;
		}
	}
	
	return $result;
}

/**
 * Function: sample
 * =============================================================================
 * Returns $size random elements from the array or a single element if $size
 * is null. This function differs from array_rand() in that it returns an array
 * with a single element if $size is 1.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to filter
 * $size - How large a sample to you want
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function sample($array, $size = null)
{
	if ($size === null)
	{
		return $array[array_rand($array)];
	}
	else
	{
		return only($array, (array)array_rand($array, $size));
	}
}

/**
 * Function: mapWithKey
 * =============================================================================
 * Map the collection into another, applying $callback to each element and its
 * key. This function differs from the built-in array_map() in that it also
 * passes the key as a second element to the callback.
 * 
 * 	MapWithKey(['a'=>1,'b'=>2,'c'=>3], function($v){return $v * 2;});
 * 	//=> ['a' => 2, 'b' => 4, 'c' => 6]
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to map
 * $callback - A callable function
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function mapWithKey($array, $callback)
{
	$mapped = array();
	
	foreach ($array as $key => $value)
	{
		$mapped[$key] = $callback($value, $key);
	}
	
	return $mapped;
}

/**
 * Function: flatMap
 * =============================================================================
 * Maps an array into another by applying $callback
 * to each element and flattening the results.
 * 
 * 	FlatMap(['foo', 'bar baz'], function ($s) { return explode(' ', $s); });
 * 	//=> ['foo', 'bar', 'baz']
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to map
 * $callback - A callable function
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function flatMap($array, $callback)
{
	$result = array();
	
	foreach ($array as $key => $value)
	{
		$newValues = $callback($value, $key);
		if ($newValues)
		{
			foreach ($newValues as $newValue)
			{
				$result[] = $newValue;
			}
		}
	}
	
	return $result;
}

/**
 * Function: mapToAssoc
 * =============================================================================
 * Creates an associative array by invoking $callback on each element and
 * using the 2 resulting values as key and value.
 * 
 * 	$friends = [['name' => 'Bob', 'surname' => 'Hope', 'age' => 34], ['name' => 'Alice', 'surname' => 'Miller', 'age' => 23]];
 * 	MapToAssoc($friends, function ($v, $k) { return [$v['name'].' '.$v['surname'], $v['age']] });
 * 	//=> ['Bob Hope' => 34, 'Alice Miller' => 23]
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array -
 * $callback - callable($value, $key) -> array($newKey, $newValue)
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function mapToAssoc($array, $callback)
{
	$mapped = array();
	
	foreach ($array as $key => $value)
	{
		list($newKey, $newValue) = $callback($value, $key);
		$mapped[$newKey] = $newValue;
	}
	
	return $mapped;
}

/**
 * Function: foldWithKey
 * =============================================================================
 * Reduces the array into a single value by calling $callback repeatedly
 * on the elements and their keys, passing the resulting value along each time.
 * 
 * FoldWithKey(['foo', 'bar', 'baz'], function ($res, $v, $k) { return "$res $k:$e"; }); //=> ' 0:foo 1:bar 2:baz'
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array -
 * $callback -  callable($accumulator, $value, $key) -> mixed
 * $intial - 
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function foldWithKey($array, $callback, $initial = null)
{
	foreach ($array as $key => $value)
	{
		$initial = $callback($initial, $value, $key);
	}
	
	return $initial;
}

/**
 * Function: foldRight
 * =============================================================================
 * Right-associative version of array_reduce().
 * 
 * 	FoldRight(['foo', 'bar', 'baz'], function ($res, $e) { return $res . $e; }); //=> 'bazbarfoo'
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array -
 * $callback - callable($accumulator, $value, $key) -> mixed
 * $initial - 
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function foldRight($array, $callback, $initial = null)
{
	return array_reduce(array_reverse($array, true), $callback, $initial);
}

/**
 * Function: foldRightWithKey
 * =============================================================================
 * Right-associative version of FoldWithKey()
 * 
 * 	FoldRightWithKey(['foo', 'bar', 'baz'], function ($res, $v, $k) { return "$res $v:$k"; }); //=> ' 2:baz 1:bar 0:foo'
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array -
 * $callback -  callable($accumulator, $value, $key) -> mixed
 * $intial - 
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function foldRightWithKey($array, $callback, $initial = null)
{
	return foldWithKey(array_reverse($array, true), $callback, $initial);
}

/**
 * Function: minBy
 * =============================================================================
 * Finds the smallest element by result of $callback
 * 
 * 	MinBy(['tasty', 'big', 'cheeseburgers'], 'mb_strlen'); //=> 'big'
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array -
 * $callback - callable($value, $key) -> number|string
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function minBy($array, $callback)
{
	$minResult = null;
	$minElement = null;
	
	foreach ($array as $element)
	{
		$current = $callback($element);
		if (!isset($minResult) || $current < $minResult)
		{
			$minResult = $current;
			$minElement = $element;
		}
	}
	
	return $minElement;
}

/**
 * Function: maxBy
 * =============================================================================
 * Finds the largest element by result of $callback
 * 
 * 	MaxBy(['tasty', 'big', 'cheeseburgers'], 'mb_strlen'); //=> 'cheeseburgers'
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array -
 * $callback - callable($value, $key) -> number|string
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function maxBy($array, $callback)
{
	$maxResult = null;
	$maxElement = null;
	
	foreach ($array as $element)
	{
		$current = $callback($element);
		if (!isset($maxResult) || $current > $maxResult)
		{
			$maxResult = $current;
			$maxElement = $element;
		}
	}
	
	return $maxElement;
}

/**
 * Function: sumBy
 * =============================================================================
 * Returns the sum of all elements passed through $callback
 * 
 * 	SumBy(['tasty', 'big', 'cheeseburgers'], 'mb_strlen'); // => 21
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array -
 * $callback - callable($value, $key) -> number
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * int
 */
function sumBy($array, $callback)
{
	$sum = 0;
	
	foreach ($array as $value)
	{
		$sum += $callback($value);
	}
	
	return $sum;
}

/**
 * Function: partition
 * =============================================================================
 * Returns two arrays: one with elements that satisfy the predicate,
 * the other with elements that don't
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - 
 * $predicate - 
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function partition($array, $predicate)
{
	$pass = array();
	$fail = array();
	
	foreach ($array as $key => $value)
	{
		$predicate($value, $key)
			? $pass[$key] = $value
			: $fail[$key] = $value;
	}
	
	return array($pass, $fail);
}

/**
 * Function: zip
 * =============================================================================
 * Zips together two or more arrays.
 * 
 * 	Zip(range(1, 5), range('a', 'e'), [5, 4, 3, 2, 1]);
 * 	//=> [[1, a, 5], [2, b, 4], [3, c, 3], [4, d, 2], [5, e, 1]]
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array1 -
 * $array2 - 
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function zip()
{
	$args = func_get_args();
	array_unshift($args, null);
	return call_user_func_array('array_map', $args);
}

/**
 * Function: zipWith
 * =============================================================================
 * 
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array1 -
 * $array2 - 
 * $callback - 
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function zipWith($array1, $array2, $callback)
{
	$result = array();
	
	foreach ($array1 as $a)
	{
		list(,$b) = each($array2);
		$result[] = $callback($a, $b);
	}
	
	return $result;
}

////////////////////////////////////////////////////////////////////////////////
//
// SECTION: Laravel Stubs
// =============================================================================
// The following functions are just stubs for methods of the class:
// 
//     \Illuminate\Support\Arr
// 
// They are here to simply provide continuity between the
// Fluent API, Static Method API and Function API.
// 
////////////////////////////////////////////////////////////////////////////////

/**
 * Add an element to an array using "dot" notation if it doesn't exist.
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $value
 * @return array
 */
function add($array, $key, $value)
{
	return Arr::add($array, $key, $value);
}

/**
 * Build a new array using a callback.
 *
 * @param  array     $array
 * @param  \Closure  $callback
 * @return array
 */
function build($array, Closure $callback)
{
	return Arr::build($array, $callback);
}

/**
 * Divide an array into two arrays. One with keys and the other with values.
 *
 * @param  array  $array
 * @return array
 */
function divide($array)
{
	return Arr::divide($array);
}

/**
 * Flatten a multi-dimensional associative array with dots.
 *
 * @param  array   $array
 * @param  string  $prepend
 * @return array
 */
function dot($array, $prepend = '')
{
	return Arr::dot($array, $prepend);
}

/**
 * Get all of the given array except for a specified array of items.
 *
 * @param  array  $array
 * @param  array|string  $keys
 * @return array
 */
function except($array, $keys)
{
	return Arr::except($array, $keys);
}

/**
 * Fetch a flattened array of a nested array element.
 *
 * @param  array   $array
 * @param  string  $key
 * @return array
 */
function fetch($array, $key)
{
	return Arr::fetch($array, $key);
}

/**
 * Return the first element in an array passing a given truth test.
 *
 * @param  array     $array
 * @param  \Closure  $callback
 * @param  mixed     $default
 * @return mixed
 */
function first($array, $callback, $default = null)
{
	return Arr::first($array, $callback, $default);
}

/**
 * Return the last element in an array passing a given truth test.
 *
 * @param  array     $array
 * @param  \Closure  $callback
 * @param  mixed     $default
 * @return mixed
 */
function last($array, $callback, $default = null)
{
	return Arr::last($array, $callback, $default);
}

/**
 * Flatten a multi-dimensional array into a single level.
 *
 * @param  array  $array
 * @return array
 */
function flatten($array)
{
	return Arr::flatten($array);
}

/**
 * Remove one or many array items from a given array using "dot" notation.
 *
 * @param  array  $array
 * @param  array|string  $keys
 * @return void
 */
function forget(&$array, $keys)
{
	return Arr::forget($array, $keys);
}

/**
 * Get an item from an array using "dot" notation.
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $default
 * @return mixed
 */
function get($array, $key, $default = null)
{
	return Arr::get($array, $key, $default);
}

/**
 * Get a subset of the items from the given array.
 *
 * @param  array  $array
 * @param  array|string  $keys
 * @return array
 */
function only($array, $keys)
{
	return Arr::only($array, $keys);
}

/**
 * Pluck an array of values from an array.
 *
 * @param  array   $array
 * @param  string  $value
 * @param  string  $key
 * @return array
 */
function pluck($array, $value, $key = null)
{
	return Arr::pluck($array, $value, $key);
}

/**
 * Get a value from the array, and remove it.
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $default
 * @return mixed
 */
function pull(&$array, $key, $default = null)
{
	return Arr::pull($array, $key, $default);
}

/**
 * Set an array item to a given value using "dot" notation.
 *
 * If no key is given to the method, the entire array will be replaced.
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $value
 * @return array
 */
function set(&$array, $key, $value)
{
	return Arr::set($array, $key, $value);
}

/**
 * Sort the array using the given Closure.
 *
 * @param  array     $array
 * @param  \Closure  $callback
 * @return array
 */
function sort($array, Closure $callback)
{
	return Arr::sort($array, $callback);
}

/**
 * Filter the array using the given Closure.
 *
 * @param  array     $array
 * @param  \Closure  $callback
 * @return array
 */
function where($array, Closure $callback)
{
	return Arr::where($array, $callback);
}