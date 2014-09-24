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

/**
 * Function: each
 * =============================================================================
 * Executes a callback for each item of the array.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to apply the callback to.
 * $callback - A closure to run.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * The array with the callback applied.
 */
function each($array, \Closure $callback)
{
	return array_map($callback, $array);
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
 * Function: toString
 * =============================================================================
 * Returns an easily readable string representation of a nested array structure.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to turnh into a string
 * $showKeys - Whether to output array keys. Skip to handle intelligently
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * string
 */
function toString($array, $showKeys = null)
{
	// Set a counter
	$idx = 0;
	
	// Remap the array
	$remapped = mapWithKey($array, function ($v, $k) use (&$idx, $showKeys)
	{
		if ($showKeys === null && $idx++ === $k || $showKeys === false)
		{
			$str = '';
		}
		else
		{
			$str = "$k => ";
		}
		
		if (is_array($v) || $v instanceof \Gears\Arrays\Fluent)
		{
			$str .= toString($v, $showKeys);
		}
		else if (is_object($v))
		{
			if (is_callable($v, '__toString'))
			{
				$str .= (string)$v;
			}
			else
			{
				$str .= get_class($v);
			}
		}
		else
		{
			$str .= (string)$v;
		}
		
		return $str;
	});
	
	// Return a string representation
	return sprintf('[%s]', implode(', ', $remapped));
}

/**
 * Function: search
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
function search($array, $search, $exact = true, $trav_keys = null)
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
			$children_results = search($val, $search, $exact, $used_keys);
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
 * Function: set
 * =============================================================================
 * ...
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - 
 * $keys  - 
 * $value - 
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function set(&$array, $key, $value)
{
	if (is_null($key)) return $array = $value;

	$keys = explode('.', $key);

	while (count($keys) > 1)
	{
		$key = array_shift($keys);

		// If the key doesn't exist at this depth, we will just create an empty array
		// to hold the next value, allowing us to create the arrays to hold final
		// values at the correct depth. Then we'll keep digging into the array.
		if (!isset($array[$key]) || !(is_array($array[$key]) || $array[$key] instanceof \Gears\Arrays\Fluent))
		{
			$array[$key] = array();
		}

		$array =& $array[$key];
	}

	$array[array_shift($keys)] = $value;

	return $array;
}

/**
 * Function: get
 * =============================================================================
 * Retrieves a nested element from an array or $default if it doesn't exist.
 * 
 * 	$friends =
 * 	[
 * 		'Alice' => ['age' => 33, 'hobbies' => ['biking', 'skiing']],
 * 		'Bob' => ['age' => 29],
 * 	];
 * 	
 * 	get($friends, 'Alice.hobbies.1'); //=> 'skiing'
 * 	get($friends, ['Alice', 'hobbies', 1]); //=> 'skiing'
 * 	get($friends, 'Bob.hobbies.0', 'none'); //=> 'none'
 * 
 * NOTE: This replaces the laravel version here: *Illuminate\Support\Arr::get()*
 * because this version allows you to supply an array of keys or dot notation.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to search for your key
 * $keys  - The key path as either an array or a dot-separated string
 * $default - An optional default to return when the key doesn't exist.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function get($array, $keys, $default = null)
{
	if (is_null($keys)) return $array;

	if (isset($array[$keys])) return $array[$keys];

	if (is_string($keys)) $keys = explode('.', $keys);
	
	foreach ($keys as $key)
	{
		if (!is_array($array) || !array_key_exists($key, $array))
		{
			return value($default);
		}

		$array = $array[$key];
	}
	
	return $array;
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
 * Function: groupBy
 * =============================================================================
 * Groups the array into sets key by either results of a callback or a sub-key
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
function groupBy($array, $callbackOrKey, $arrayAccess = true)
{
	$groups = array();
	
	if (is_string($callbackOrKey))
	{
		if ($arrayAccess)
		{
			foreach ($array as $element)
			{
				$groups[$element[$callbackOrKey]][] = $element;
			}
		}
		else
		{
			foreach ($array as $element)
			{
				$groups[$element->{$callbackOrKey}][] = $element;
			}
		}
	}
	else
	{
		foreach ($array as $element)
		{
			$groups[$callbackOrKey($element)][] = $element;
		}
	}
	
	return $groups;
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

/**
 * Function: sortBy
 * =============================================================================
 * Returns a copy of the array, sorted by a key or result of a callback
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array -
 * $callbackOrKey - 
 * $mode - 
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function sortBy($array, $callbackOrKey, $mode = SORT_REGULAR)
{
	$sortBy = array();
	
	if (is_string($callbackOrKey))
	{
		foreach ($array as $value)
		{
			$sortBy[] = $value[$callbackOrKey];
		}
	}
	else
	{
		foreach ($array as $key => $value)
		{
			$sortBy[] = $callbackOrKey($value, $key);
		}
	}
	
	array_multisort($sortBy, $mode, $array);
	
	return $array;
}

/**
 * Section: Laravel Stubs
 * =============================================================================
 * The following functions are just stubs for methods of the class:
 * 
 *     \Illuminate\Support\Arr
 * 
 * I could integrate these calls directly and dynamically into the
 * Gears\Arr class. However then this procedural API would not match
 * that of the Gears\Arr class.
 * 
 * Each function does not define any arguments, we dynamically pick these up
 * so that any changes to the methods definition in the original Laravel class
 * are automatically picked up here.
 * 
 * Thus if you are looking for documenation on how to use these functions.
 * Please see: http://laravel.com/api/4.2/Illuminate/Support/Arr.html
 */

function add() { return call_user_func_array('\Illuminate\Support\Arr::add', func_get_args()); }
function build() { return call_user_func_array('\Illuminate\Support\Arr::build', func_get_args()); }
function divide() { return call_user_func_array('\Illuminate\Support\Arr::divide', func_get_args()); }
function dot() { return call_user_func_array('\Illuminate\Support\Arr::dot', func_get_args()); }
function except() { return call_user_func_array('\Illuminate\Support\Arr::except', func_get_args()); }
function fetch() { return call_user_func_array('\Illuminate\Support\Arr::fetch', func_get_args()); }
function first() { return call_user_func_array('\Illuminate\Support\Arr::first', func_get_args()); }
function last() { return call_user_func_array('\Illuminate\Support\Arr::last', func_get_args()); }
function flatten() { return call_user_func_array('\Illuminate\Support\Arr::flatten', func_get_args()); }
function only() { return call_user_func_array('\Illuminate\Support\Arr::only', func_get_args()); }
function pluck() { return call_user_func_array('\Illuminate\Support\Arr::pluck', func_get_args()); }
function sort() { return call_user_func_array('\Illuminate\Support\Arr::sort', func_get_args()); }
function where() { return call_user_func_array('\Illuminate\Support\Arr::where', func_get_args()); }

/*
 * The following are a few speical cases. These methods expect a refrence
 * to be passed to the array that they act on. Where as the above methods
 * take an array, manipulate it and return a completely new array or value.
 */
function pull(&$array, $key, $default = null) { return \Illuminate\Support\Arr::pull($array, $key, $default); }
function forget(&$array, $keys) { \Illuminate\Support\Arr::forget($array, $keys); }