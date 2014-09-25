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
 * Function: isArrayLike
 * =============================================================================
 * This is our version of is_array. Because in our Fluent API we are dealing
 * with Objects that act like arrays we needed a way to detect these objects
 * as well as standard arrays.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $value - The value you think might be an array.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * bool
 */
function isArrayLike($value)
{
	return is_array($value) ||
	(
		$value instanceof \ArrayAccess &&
		$value instanceof \Traversable &&
		$value instanceof \Serializable &&
		$value instanceof \Countable
	);
}

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
 * void
 */
function each($array, \Closure $callback)
{
	foreach ($array as $key => $value)
	{
		if ($callback($value, $key) === false)
		{
			break;
		}
	}
}

/**
 * Function: contains
 * =============================================================================
 * Determine if an item exists in the array. This checks against the values of
 * the array, not the keys. Simply use ```isset()``` to check if a key exists.
 * 
 * NOTE: This only goes one level deep. If you need to do a recursive search.
 * Please use the ```\Gears\Arrays\search()``` function instead.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to search.
 * $value - What are we searching for, this can be a callback.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * bool
 */
function contains($array, $value)
{
	// If the value is a callback.
	// This uses ```Gears\Arrays\first()```
	if ($value instanceof \Closure)
	{
		return ! is_null(first($array, $value));
	}

	// Otherwise we just use in_array
	return in_array($value, $array);
}

/**
 * Function: groupBy
 * =============================================================================
 * Group an associative array by a field or Closure value.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to re-index.
 * $groupBy - A string or callable
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function groupBy($array, $groupBy)
{
	// Create our results array
	$results = array();

	// Loop through the provided array
	foreach ($array as $key => $value)
	{
		// Determine the groupByKey
		if (!is_string($groupBy) && is_callable($groupBy))
		{
			$groupByKey = $groupBy($value, $key);
		}
		else
		{
			// NOTE: data_get is a laravel helper.
			$groupByKey = data_get($value, $groupBy);
		}

		// Add the new result
		$results[$groupByKey][] = $value;
	}

	// Returns our results
	return $results;
}

/**
 * Function: keyBy
 * =============================================================================
 * Key an associative array by a field.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to re-index.
 * $keyBy - The key to use for the re-index.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function keyBy($array, $keyBy)
{
	$results = [];

	foreach ($array as $item)
	{
		// NOTE: data_get is a laravel helper.
		$key = data_get($item, $keyBy);
		$results[$key] = $item;
	}

	return $results;
}

/**
 * Function: implode
 * =============================================================================
 * A slight variation on the standard implode function. This function
 * concatenates values of a given key as a string.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to work with.
 * $value - The key to use.
 * $glue - What are we joining the string together with.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * string
 */
function implode($array, $value, $glue = null)
{
	if (is_null($glue)) return \implode(pluck($array, $value));

	return \implode($glue, pluck($array, $value));
}

/**
 * Function: first
 * =============================================================================
 * This will either return the first item in the array.
 * If however you give us a callback we will return the first value
 * that passes your custom truth test (the callback).
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to work with.
 * $callback - Optional callback.
 * $default - What to return should we not find anything.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function first($array, $callback = null, $default = null)
{
	if (is_null($callback))
	{
		return count($array) > 0 ? reset($array) : null;
	}
	else
	{
		return \Gears\Arrays\Arr::first($array, $callback, $default);
	}
}

/**
 * Function: last
 * =============================================================================
 * The exact opposite of the above.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to work with.
 * $callback - Optional callback.
 * $default - What to return should we not find anything.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function last($array, $callback = null, $default = null)
{
	if (is_null($callback))
	{
		return count($array) > 0 ? end($array) : null;
	}
	else
	{
		return \Gears\Arrays\Arr::last($array, $callback, $default);
	}
}

/**
 * Function: map
 * =============================================================================
 * Run a map over each of the items.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to apply the map to.
 * $callback - A closure to run.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function map($array, \Closure $callback)
{
	return array_map($callback, $array, array_keys($array));
}

/**
 * Function: isEmpty
 * =============================================================================
 * Determine if the array is empty or not.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to work with.
 * 
 * Returns
 * -----------------------------------------------------------------------------
 * bool
 */
function isEmpty($array)
{
	return empty($array);
}

/**
 * Function: has
 * =============================================================================
 * Determine if an item exists in the array by key.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to work on.
 * $key - The key to check.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * bool
 */
function has($array, $key)
{
	return isset($array[$key]);
}

/**
 * Function: keys
 * =============================================================================
 * Get the keys of the array.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to work on.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function keys($array)
{
	return array_keys($array);
}

/**
 * Function: filter
 * =============================================================================
 * Just an alias for array_filter.
 * 
 * http://php.net/manual/en/function.array-filter.php
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to filter.
 * $callback - The callback to run.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function filter($array, \Closure $callback)
{
	return array_filter($array, $callback);
}

/**
 * Function: flip
 * =============================================================================
 * Just an alias for array_flip.
 * 
 * http://php.net/manual/en/function.array-flip.php
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to flip.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function flip($array)
{
	return array_flip($array);
}

/**
 * Function: diff
 * =============================================================================
 * Just an alias for array_diff.
 * 
 * http://php.net/manual/en/function.array-diff.php
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array1 - The first array.
 * $array2 - The second array.
 * $array3 - etc...
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function diff()
{
	return call_user_func_array('array_diff', func_get_args());
}

/**
 * Function: intersect
 * =============================================================================
 * Just an alias for array_intersect.
 * 
 * http://php.net/manual/en/function.array-intersect.php
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array1 - The first array.
 * $array2 - The second array.
 * $array3 - etc...
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function intersect()
{
	return call_user_func_array('array_intersect', func_get_args());
}

/**
 * Function: merge
 * =============================================================================
 * Just an alias for array_merge.
 * 
 * http://php.net/manual/en/function.array-merge.php
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array1 - The first array.
 * $array2 - The second array.
 * $array3 - etc...
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function merge($items)
{
	return call_user_func_array('array_merge', func_get_args());
}

/**
 * Function: lists
 * =============================================================================
 * Get an array with the values of a given key.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to work on.
 * $value - The value you want to pluck or create a list of.
 * $key - An optional key to index the results with.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function lists($array, $value, $key = null)
{
	return pluck($array, $value, $key);
}

/**
 * Function: pop
 * =============================================================================
 * Get and remove the last item from the array.
 * 
 * http://php.net/manual/en/function.array-pop.php
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - Passed by reference.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function pop(&$array)
{
	return array_pop($array);
}

/**
 * Function: unshift
 * =============================================================================
 * Just an alias of array_unshift.
 * 
 * http://php.net/manual/en/function.array-unshift.php
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - Passed by reference.
 * $value1 - The value to add to the array.
 * $value2 - The value to add to the array.
 * $value3 - etc
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * int
 */
function unshift(&$array)
{
	$arguments = [&$array];
	$args = func_get_args(); array_shift($args);
	foreach ($args as $arg) $arguments[] = $arg;
	return call_user_func_array('array_unshift', $arguments);
}

/**
 * Function: prepend
 * =============================================================================
 * Push an item onto the beginning of the array.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - Passed by reference.
 * $value - The value to add to the array.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * void
 */
function prepend(&$array, $value)
{
	array_unshift($array, $value);
}

/**
 * Function: push
 * =============================================================================
 * Push an item onto the end of the array.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - Passed by reference.
 * $value - The value to add to the array.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * void
 */
function push(&$array, $value)
{
	$array[] = $value;
}

/**
 * Function: put
 * =============================================================================
 * Put an item in the array by key.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - Passed by reference.
 * $key - The key to assign to the new item.
 * $value - The value to add to the array.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * void
 */
function put(&$array, $key, $value)
{
	$array[$key] = $value;
}

/**
 * Function: random
 * =============================================================================
 * Get one or more items randomly from the array.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to select from.
 * $amount - How many items from the array do you want, defaults to 1.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function random($array, $amount = 1)
{
	if (isEmpty($array)) return null;

	$keys = array_rand($array, $amount);

	return is_array($keys) ? array_intersect_key($array, array_flip($keys)) : $array[$keys];
}

/**
 * Function: reduce
 * =============================================================================
 * Reduce the array to a single value.
 * 
 * http://php.net/manual/en/function.array-reduce.php
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array
 * $callback
 * $initial
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function reduce($array, callable $callback, $initial = null)
{
	return array_reduce($array, $callback, $initial);
}

/**
 * Function: reject
 * =============================================================================
 * Create a array of all elements that do not pass a given truth test.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The original array.
 * $callback - The truth test.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function reject($array, $callback)
{
	if ($callback instanceof \Closure)
	{
		return filter($array, function($item) use ($callback)
		{
			return ! $callback($item);
		});
	}

	return filter($array, function($item) use ($callback)
	{
		return $item != $callback;
	});
}

/**
 * Function: reverse
 * =============================================================================
 * Reverse items order.
 * 
 * http://php.net/manual/en/function.array-reverse.php
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to reverse.
 * 
 * Returns
 * -----------------------------------------------------------------------------
 * The reversed array.
 */
function reverse($array)
{
	return array_reverse($array);
}

/**
 * Function: search
 * =============================================================================
 * Search the array for a given value and return the
 * corresponding key if successful.
 * 
 * http://php.net/manual/en/function.array-search.php
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array
 * $value
 * $strict
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function search($array, $value, $strict = false)
{
	return array_search($value, $array, $strict);
}

/**
 * Function: shift
 * =============================================================================
 * Get and remove the first item from the array.
 * 
 * http://php.net/manual/en/function.array-shift.php
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function shift(&$array)
{
	return array_shift($array);
}

/**
 * Function: shuffle
 * =============================================================================
 * Shuffle the items in the array.
 * 
 * http://php.net/manual/en/function.shuffle.php
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * void
 */
function shuffle(&$array)
{
	shuffle($array);
}

/**
 * Function: slice
 * =============================================================================
 * Slice the array.
 * 
 * http://php.net/manual/en/function.array-slice.php
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array
 * $offset
 * $length
 * $preserveKeys
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function slice($array, $offset, $length = null, $preserveKeys = false)
{
	return array_slice($array, $offset, $length, $preserveKeys);
}

/**
 * Function: chunk
 * =============================================================================
 * Chunk the array.
 * 
 * http://php.net/manual/en/function.array-chunk.php
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array
 * $size
 * $preserveKeys
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function chunk($array, $size, $preserveKeys = false)
{
	return array_chunk($array, $size, $preserveKeys);
}

/**
 * Function: sort
 * =============================================================================
 * Sorts the array.
 * 
 * To keep compaitibility with the Laravel Collections Class and the
 * static Laravel Arr::sort method we do diffrent things based
 * on what we are given. ie: A real array vs an instance of Gears\Arrays\Fluent
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array
 * $size
 * $preserveKeys
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function sort(&$array, \Closure $callback)
{
	if (is_array($array))
	{
		$original = $array;
		sortBy($array, $callback);
		$results = $array;
		$array = $original;
		return $results;
	}
	else
	{
		uasort($array, $callback);
	}
}

/**
 * Function: sortBy
 * =============================================================================
 * Sort the array using the given Closure.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array
 * $callback
 * $options
 * $descending
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * void
 */
function sortBy(&$array, $callback, $options = SORT_REGULAR, $descending = false)
{
	$results = array();

	if (is_string($callback))
	{
		$callback = function($item) use ($callback)
		{
			// NOTE: data_get is a laravel helper
			return data_get($item, $callback);
		};
	}

	// First we will loop through the items and get the comparator from a callback
	// function which we were given. Then, we will sort the returned values and
	// and grab the corresponding values for the sorted keys from this array.
	foreach ($array as $key => $value)
	{
		$results[$key] = $callback($value);
	}

	$descending ? arsort($results, $options) : asort($results, $options);

	// Once we have sorted all of the keys in the array, we will loop through them
	// and grab the corresponding model so we can set the underlying items list
	// to the sorted version. Then we'll just return the collection instance.
	foreach (array_keys($results) as $key)
	{
		$results[$key] = $array[$key];
	}

	$array = $results;
}

/**
 * Function: sortByDesc
 * =============================================================================
 * Sort the array in descending order using the given Closure.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array
 * $callback
 * $options
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * void
 */
function sortByDesc(&$array, $callback, $options = SORT_REGULAR)
{
	sortBy($array, $callback, $options, true);
}

/**
 * Function: splice
 * =============================================================================
 * Remove a portion of the array and replace it with something else
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array
 * $offset
 * $length
 * $replacement
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function splice(&$array, $offset, $length = 0, $replacement = array())
{
	return array_splice($array, $offset, $length, $replacement);
}

/**
 * Function: sum
 * =============================================================================
 * Get the sum of the given values.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array
 * $callback
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function sum($array, $callback)
{
	if (is_string($callback))
	{
		$callback = function($item) use ($callback)
		{
			// NOTE: data_get is a laravel helper
			return data_get($item, $callback);
		};
	}

	return reduce($array, function($result, $item) use ($callback)
	{
		return $result += $callback($item);

	}, 0);
}

/**
 * Function: take
 * =============================================================================
 * Take the first or last {$limit} items.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array
 * $limit
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function take($array, $limit = null)
{
	if ($limit < 0) return slice($array, $limit, abs($limit));

	return slice($array, 0, $limit);
}

/**
 * Function: transform
 * =============================================================================
 * Transform each item in the collection using a callback.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array
 * $callback
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * void
 */
function transform(&$array, \Closure $callback)
{
	$array = array_map($callback, $array);
}

/**
 * Function: unique
 * =============================================================================
 * Return only unique items from the collection array.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function unique($array)
{
	return array_unique($array);
}

/**
 * Function: values
 * =============================================================================
 * Reset the keys on the underlying array.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * void
 */
function values(&$array)
{
	$array = array_values($array);
}

// -------------- EVERYTHING BELOW HERE IS NOT YET UNIT TESTED -------------- //

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

////////////////////////////////////////////////////////////////////////////////
//
// SECTION: Laravel Stubs
// =============================================================================
// The following functions are just stubs for methods of the class:
// 
//     \Gears\Arrays\Arr which extends \Illuminate\Support\Arr
// 
// Thus if you are looking for documentation on how to use these functions.
// Please see: http://laravel.com/api/4.2/Illuminate/Support/Arr.html
// 
////////////////////////////////////////////////////////////////////////////////

function add() { return call_user_func_array('\Gears\Arrays\Arr::add', func_get_args()); }
function get() { return call_user_func_array('\Gears\Arrays\Arr::get', func_get_args()); }
function build() { return call_user_func_array('\Gears\Arrays\Arr::build', func_get_args()); }
function divide() { return call_user_func_array('\Gears\Arrays\Arr::divide', func_get_args()); }
function dot() { return call_user_func_array('\Gears\Arrays\Arr::dot', func_get_args()); }
function except() { return call_user_func_array('\Gears\Arrays\Arr::except', func_get_args()); }
function fetch() { return call_user_func_array('\Gears\Arrays\Arr::fetch', func_get_args()); }
function flatten() { return call_user_func_array('\Gears\Arrays\Arr::flatten', func_get_args()); }
function only() { return call_user_func_array('\Gears\Arrays\Arr::only', func_get_args()); }
function pluck() { return call_user_func_array('\Gears\Arrays\Arr::pluck', func_get_args()); }
function where() { return call_user_func_array('\Gears\Arrays\Arr::where', func_get_args()); }

/*
 * The following are a few special cases. These methods expect a reference
 * to be passed to the array that they act on. Where as the above methods
 * take an array, manipulate it and return a completely new array or value.
 */
function pull(&$array, $key, $default = null) { return \Gears\Arrays\Arr::pull($array, $key, $default); }
function set(&$array, $key, $value) { return \Gears\Arrays\Arr::set($array, $key, $value); }
function forget(&$array, $keys) { \Gears\Arrays\Arr::forget($array, $keys); }