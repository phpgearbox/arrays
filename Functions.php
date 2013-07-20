<?php
////////////////////////////////////////////////////////////////////////////////
// __________ __             ________                   __________              
// \______   \  |__ ______  /  _____/  ____ _____ ______\______   \ _______  ___
//  |     ___/  |  \\____ \/   \  ____/ __ \\__  \\_  __ \    |  _//  _ \  \/  /
//  |    |   |   Y  \  |_> >    \_\  \  ___/ / __ \|  | \/    |   (  <_> >    < 
//  |____|   |___|  /   __/ \______  /\___  >____  /__|  |______  /\____/__/\_ \
//                \/|__|           \/     \/     \/             \/            \/
// =============================================================================
//         Designed and Developed by Brad Jones <bj @="gravit.com.au" />        
// =============================================================================
////////////////////////////////////////////////////////////////////////////////

namespace Gears\Arrays;

/**
 * Function: Search
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
function Search($array, $search, $exact = true, $trav_keys = null)
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
			$children_results = Search($val, $search, $exact, $used_keys);
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
 * Function: Debug
 * =============================================================================
 * This will output an array for debug purposes. If running from a web server
 * we will wrap it in some pre tags for you.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to output.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * void
 */
function Debug($array)
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
}

// ----- Single element access -----

/**
 * Retrieves a nested element from an array or $default if it doesn't exist
 *
 * <code>
 * $friends = [
 *      'Alice' => ['age' => 33, 'hobbies' => ['biking', 'skiing']],
 *      'Bob' => ['age' => 29],
 * ];
 *
 * Arr::getNested($friends, 'Alice.hobbies.1'); //=> 'skiing'
 * Arr::getNested($friends, ['Alice', 'hobbies', 1]); //=> 'skiing'
 * Arr::getNested($friends, 'Bob.hobbies.0', 'none'); //=> 'none'
 * </code>
 *
 * @param array $array
 * @param string|array $keys The key path as either an array or a dot-separated string
 * @param mixed $default
 * @return mixed
 */
function GetNested($array, $keys, $default = null)
{
	if (is_string($keys)) {
		$keys = explode('.', $keys);
	} else if ($keys === null) {
		return $array;
	}

	foreach ($keys as $key) {
		if (is_array($array) && array_key_exists($key, $array)) {
			$array = $array[$key];
		} else {
			return $default;
		}
	}

	return $array;
}

/**
 * Returns the value at the given index or $default if it not present
 *
 * @param array $array
 * @param int|string $key
 * @param mixed $default
 * @return mixed
 */
function GetOrElse($array, $key, $default = null)
{
	return array_key_exists($key, $array) ? $array[$key] : $default;
}

/**
 * Returns the value at the given index. If not present, inserts $default and returns it
 *
 * @param array $array
 * @param int|string $key
 * @param mixed $default
 * @return mixed
 */
function GetOrPut(&$array, $key, $default = null)
{
	if (!array_key_exists($key, $array)) {
		$array[$key] = $default;
	}

	return $array[$key];
}

/**
 * Deletes and returns a value from an array
 *
 * @param array $array
 * @param int|string $key
 * @param mixed $default
 * @return mixed
 */
function GetAndDelete(&$array, $key, $default = null)
{
	if (array_key_exists($key, $array)) {
		$result = $array[$key];
		unset($array[$key]);
		return $result;
	} else {
		return $default;
	}
}


// ----- Slicing -----

/**
 * Returns longest prefix of elements that satisfy the $predicate.
 *
 * The predicate will be passed value and key of each element.
 *
 * @param array $array
 * @param callable $predicate ($value, $key) -> bool
 * @return array
 */
function TakeWhile($array, $predicate)
{
	$n = 0;
	foreach ($array as $key => $value) {
		if (!$predicate($value, $key)) break;
		++$n;
	}

	return array_slice($array, 0, $n);
}

/**
 * Drops longest prefix of elements satisfying $predicate and returns the rest.
 *
 * @param array $array
 * @param callable $predicate ($value, $key) -> bool
 * @return array
 */
function DropWhile($array, $predicate)
{
	$n = 0;
	foreach ($array as $key => $val) {
		if (!$predicate($val, $key)) break;
		++$n;
	}

	return array_slice($array, $n);
}

/**
 * Repeats the array $n times.
 *
 * TODO: Convert to iterator to conserve memory and time
 *
 * @param array $array
 * @param int $n
 * @return array
 */
function Repeat($array, $n)
{
	$result = array();
	while ($n-- > 0) {
		foreach ($array as $value) {
			$result[] = $value;
		}
	}

	return $result;
}


// ----- Finding -----

/**
 * Returns the first value of the array satisfying the $predicate or $default
 *
 * @param array $array
 * @param callable $predicate ($value, $key) -> bool
 * @param mixed $default
 * @return mixed|null
 */
function Find($array, $predicate, $default = null)
{
	foreach ($array as $key => $value) {
		if ($predicate($value, $key)) {
			return $value;
		}
	}

	return $default;
}

/**
 * Returns the last value of the array satisfying the $predicate or $default
 *
 * @param array $array
 * @param callable $predicate ($value, $key) -> bool
 * @param mixed $default
 * @return mixed|null
 */
function FindLast($array, $predicate, $default = null)
{
	return self::find(array_reverse($array, true), $predicate, $default);
}

/**
 * Returns the first key satisfying the $predicate or null
 *
 * @param array $array
 * @param callable $predicate ($value, $key) -> bool
 * @return int|null|string
 */
function FindKey($array, $predicate)
{
	foreach ($array as $key => $value) {
		if ($predicate($value, $key)) {
			return $key;
		}
	}

	return null;
}

/**
 * Returns the last key satisfying the $predicate or null
 *
 * @param array $array
 * @param callable $predicate ($value, $key) -> bool
 * @return int|null|string
 */
function FindLastKey($array, $predicate)
{
	return self::findKey(array_reverse($array, true), $predicate);
}

function lastIndexOf($array, $value, $strict = true)
{
	$index = array_search($value, array_reverse($array, true), $strict);
	return $index === false ? null : $index;
}


// ----- Hash operations -----

/**
 * Returns only those values whose keys are present in $keys
 *
 * <code>
 * Arr::only(range('a', 'e'), [3, 4]); //=> ['d', 'e']
 * </code>
 *
 * @param array $array
 * @param array $keys
 * @return array
 */
function Only($array, $keys)
{
	return array_intersect_key($array, array_flip($keys));
}

/**
 * Returns only those values whose keys are not present in $keys
 *
 * <code>
 * Arr::except(range('a', 'e'), [2, 4]); //=> ['a', 'b', 'd']
 * </code>
 *
 * @param array $array
 * @param array $keys
 * @return array
 */
function Except($array, $keys)
{
	return array_diff_key($array, array_flip($keys));
}

/**
 * Re-indexes the array by either results of the callback or a sub-key
 *
 * @param array $array
 * @param callable|string $callbackOrKey
 * @param bool $arrayAccess Whether to use array or object access when given a key name
 * @return array
 */
function IndexBy($array, $callbackOrKey, $arrayAccess = true)
{
	$indexed = array();

	if (is_string($callbackOrKey)) {
		if ($arrayAccess) {
			foreach ($array as $element) {
				$indexed[$element[$callbackOrKey]] = $element;
			}
		} else {
			foreach ($array as $element) {
				$indexed[$element->{$callbackOrKey}] = $element;
			}
		}
	} else {
		foreach ($array as $element) {
			$indexed[$callbackOrKey($element)] = $element;
		}
	}

	return $indexed;
}

/**
 * Groups the array into sets key by either results of a callback or a sub-key
 *
 * @param array $array
 * @param callable|string $callbackOrKey
 * @param bool $arrayAccess Whether to use array or object access when given a key name
 * @return array
 */
function GroupBy($array, $callbackOrKey, $arrayAccess = true)
{
	$groups = array();

	if (is_string($callbackOrKey)) {
		if ($arrayAccess) {
			foreach ($array as $element) {
				$groups[$element[$callbackOrKey]][] = $element;
			}
		} else {
			foreach ($array as $element) {
				$groups[$element->{$callbackOrKey}][] = $element;
			}
		}
	} else {
		foreach ($array as $element) {
			$groups[$callbackOrKey($element)][] = $element;
		}
	}

	return $groups;
}


// ----- Assertions -----

/**
 * Returns true if all elements satisfy the given predicate
 *
 * @param $array
 * @param callable $predicate
 * @return bool
 */
function All($array, $predicate)
{
	foreach ($array as $key => $value) {
		if (!$predicate($value, $key)) {
			return false;
		}
	}

	return true;
}

/**
 * Returns true if at least one element satisfies the given predicate
 *
 * @param $array
 * @param callable $predicate
 * @return bool
 */
function Any($array, $predicate)
{
	foreach ($array as $key => $value) {
		if ($predicate($value, $key)) {
			return true;
		}
	}

	return false;
}

/**
 * Returns true if exactly one element satisfies the given predicate
 *
 * @param $array
 * @param callable $predicate
 * @return bool
 */
function One($array, $predicate)
{
	return self::exactly($array, 1, $predicate);
}

/**
 * Returns true if none of the elements satisfy $predicate
 *
 * @param array $array
 * @param callable $predicate
 * @return bool
 */
function None($array, $predicate)
{
	return self::exactly($array, 0, $predicate);
}

/**
 * Returns true if exactly $n elements satisfy the $predicate
 *
 * @param array $array
 * @param int $n
 * @param callable $predicate ($value, $key) -> bool
 * @return bool
 */
function Exactly($array, $n, $predicate)
{
	$found = 0;
	foreach ($array as $key => $value) {
		if ($predicate($value, $key)) {
			if (++$found > $n) return false;
		}
	}

	return $found == $n;
}


// ----- Filtering -----

/**
 * Keeps only those elements that satisfy the $predicate
 *
 * Differs from array_filter() in that the key of each element is also passed to the predicate.
 *
 * @param array $array
 * @param callable $predicate ($value, $key) -> bool
 * @return array
 */
function FilterWithKey($array, $predicate)
{
	$result = array();
	foreach ($array as $key => $value) {
		if ($predicate($value, $key)) $result[$key] = $value;
	}

	return $result;
}

/**
 * Returns $size random elements from the array or a single element if $size is null
 *
 * This function differs from array_rand() in that it returns an array with a single element if $size is 1.
 *
 * @param array $array
 * @param int|null $size
 * @return array
 */
function Sample($array, $size = null)
{
	return $size === null
		? $array[array_rand($array)]
		: static::only($array, (array)array_rand($array, $size));
}


// ----- Mapping -----

/**
 * Map the collection into another, applying $callback to each element and its key.
 *
 * This function differs from the built-in array_map() in that it also passes the key as a
 * second element to the callback.
 *
 * <code>
 * Arr::map(['a' => 1, 'b' => 2, 'c' => 3], function ($v) { return $v * 2; });
 * //=> ['a' => 2, 'b' => 4, 'c' => 6]
 * </code>
 *
 * @param array $array
 * @param callable $callback
 * @return array
 */
function MapWithKey($array, $callback)
{
	$mapped = array();
	foreach ($array as $key => $value) {
		$mapped[$key] = $callback($value, $key);
	}

	return $mapped;
}

/**
 * Maps an array into another by applying $callback to each element and flattening the results
 *
 * <code>
 * Arr::flatMap(['foo', 'bar baz'], function ($s) { return explode(' ', $s); });
 * //=> ['foo', 'bar', 'baz']
 * </code>
 *
 * @param array $array
 * @param callable $callback ($value, $key) -> array
 * @return array array
 */
function FlatMap($array, $callback)
{
	$result = array();
	foreach ($array as $key => $value) {
		$newValues = $callback($value, $key);
		if ($newValues) {
			foreach ($newValues as $newValue) {
				$result[] = $newValue;
			}
		}
	}

	return $result;
}

/**
 * Shortcut method to pick out specified keys/properties from an array of arrays/objects
 *
 * <code>
 * $people = [
 *      ['name' => 'Bob', 'age' => 23],
 *      ['name' => 'Alice', 'age' => 32],
 *      ['name' => 'Frank', 'age' => 40],
 * ];
 *
 * Arr::pluck($people, 'name'); //=> ['Bob', 'Alice', 'Frank']
 * Arr::pluck($people, 'age', 'name'); //=> ['Bob' => 23, 'Alice' => 32, 'Frank' => 40]
 * </code>
 *
 * @param array $array
 * @param string $valueAttribute
 * @param string|null $keyAttribute
 * @param bool $arrayAccess Determines whether to use array access ($elem[$prop]) or property access ($elem->$prop)
 * @return array
 */
function Pluck($array, $valueAttribute, $keyAttribute = null, $arrayAccess = true)
{
	$result = array();
	if ($arrayAccess) {
		if ($keyAttribute) {
			foreach ($array as $value) {
				$result[$value[$keyAttribute]] = $value[$valueAttribute];
			}
		} else {
			foreach ($array as $key => $value) {
				$result[$key] = $value[$valueAttribute];
			}
		}
	} else {
		if ($keyAttribute) {
			foreach ($array as $value) {
				$result[$value->{$keyAttribute}] = $value->{$valueAttribute};
			}
		} else {
			foreach ($array as $key => $value) {
				$result[$key] = $value->{$valueAttribute};
			}
		}
	}

	return $result;
}

/**
 * Creates an associative array by invoking $callback on each element and using the 2 resulting values as key and value
 *
 * <code>
 * $friends = [['name' => 'Bob', 'surname' => 'Hope', 'age' => 34], ['name' => 'Alice', 'surname' => 'Miller', 'age' => 23]];
 * Arr::mapToAssoc($friends, function ($v, $k) { return [$v['name'].' '.$v['surname'], $v['age']] });
 * //=> ['Bob Hope' => 34, 'Alice Miller' => 23]
 * </code>
 *
 * @param array $array
 * @param callable $callback ($value, $key) -> array($newKey, $newValue)
 * @return array
 */
function MapToAssoc($array, $callback)
{
	$mapped = array();
	foreach ($array as $key => $value) {
		list($newKey, $newValue) = $callback($value, $key);
		$mapped[$newKey] = $newValue;
	}

	return $mapped;
}

/**
 * Flattens the array, combining elements of all sub-arrays into one array
 *
 * <code>
 * Arr::flatten([[1, 2, 3], [4, 5]]); //=> [1, 2, 3, 4, 5]
 * </code>
 *
 * @param array $array
 * @return array
 */
function Flatten($array)
{
	return call_user_func_array('array_merge', $array);
}


// ----- Folding and reduction -----

/**
 * Reduces the array into a single value by calling $callback repeatedly on the elements and their keys, passing the resulting value along each time.
 *
 * <code>
 * Arr::foldRight(['foo', 'bar', 'baz'], function ($res, $v, $k) { return "$res $k:$e"; }); //=> ' 0:foo 1:bar 2:baz'
 * </code>
 *
 * @param array $array
 * @param callable $callback ($accumulator, $value, $key) -> mixed
 * @param mixed $initial
 * @return mixed
 */
function FoldWithKey($array, $callback, $initial = null)
{
	foreach ($array as $key => $value) {
		$initial = $callback($initial, $value, $key);
	}

	return $initial;
}

/**
 * Right-associative version of array_reduce().
 *
 * <code>
 * Arr::foldRight(['foo', 'bar', 'baz'], function ($res, $e) { return $res . $e; }); //=> 'bazbarfoo'
 * </code>
 *
 * @param array $array
 * @param callable $callback ($accumulator, $value, $key) -> mixed
 * @param mixed $initial
 * @return mixed
 */
function FoldRight($array, $callback, $initial = null)
{
	return array_reduce(array_reverse($array, true), $callback, $initial);
}

/**
 * Right-associative version of foldWithKey()
 *
 * <code>
 * Arr::foldRight(['foo', 'bar', 'baz'], function ($res, $v, $k) { return "$res $v:$k"; }); //=> ' 2:baz 1:bar 0:foo'
 * </code>
 *
 * @param array $array
 * @param callable $callback ($accumulator, $value, $key) -> mixed
 * @param mixed $initial
 * @return mixed
 */
function FoldRightWithKey($array, $callback, $initial = null)
{
	return self::foldWithKey(array_reverse($array, true), $callback, $initial);
}

/**
 * Finds the smallest element by result of $callback
 *
 * <code>
 * Arr::minBy(['tasty', 'big', 'cheeseburgers'], 'mb_strlen'); //=> 'big'
 * </code>
 *
 * @param array $array
 * @param callable $callback ($value, $key) -> number|string
 * @return mixed
 */
function MinBy($array, $callback)
{
	$minResult = null;
	$minElement = null;
	foreach ($array as $element) {
		$current = $callback($element);
		if (!isset($minResult) || $current < $minResult) {
			$minResult = $current;
			$minElement = $element;
		}
	}

	return $minElement;
}

/**
 * Finds the largest element by result of $callback
 *
 * <code>
 * Arr::maxBy(['tasty', 'big', 'cheeseburgers'], 'mb_strlen'); //=> 'cheeseburgers'
 * </code>
 *
 * @param array $array
 * @param callable $callback ($value, $key) -> number|string
 * @return mixed
 */
function MaxBy($array, $callback)
{
	$maxResult = null;
	$maxElement = null;
	foreach ($array as $element) {
		$current = $callback($element);
		if (!isset($maxResult) || $current > $maxResult) {
			$maxResult = $current;
			$maxElement = $element;
		}
	}

	return $maxElement;
}

/**
 * Returns the sum of all elements passed through $callback
 *
 * <code>
 * Arr::sumBy(['tasty', 'big', 'cheeseburgers'], 'mb_strlen'); // => 21
 * </code>
 *
 * @param array $array
 * @param callable $callback ($value, $key) -> number
 * @return number
 */
function SumBy($array, $callback)
{
	$sum = 0;
	foreach ($array as $value) {
		$sum += $callback($value);
	}

	return $sum;
}


// ----- Splitting -----

/**
 * Returns two arrays: one with elements that satisfy the predicate, the other with elements that don't
 *
 * @param array $array
 * @param callable $predicate
 * @return array
 */
function Partition($array, $predicate)
{
	$pass = array();
	$fail = array();

	foreach ($array as $key => $value) {
		$predicate($value, $key)
			? $pass[$key] = $value
			: $fail[$key] = $value;
	}

	return array($pass, $fail);
}

/**
 * @param array $array
 * @param int $size
 * @param int $step
 * @return GroupedIterator
 */
function Sliding($array, $size, $step = 1)
{
	return new GroupedIterator($array, $size, $step);
}


// ----- Zipping -----

/**
 * Zips together two or more arrays

 * <code>
 * Arr::zip(range(1, 5), range('a', 'e'), [5, 4, 3, 2, 1]);
 * //=> [[1, a, 5], [2, b, 4], [3, c, 3], [4, d, 2], [5, e, 1]]
 * </code>
 *
 * @param array $array1
 * @param array $array2
 * @return array
 */
function Zip($array1, $array2)
{
	$args = func_get_args();
	array_unshift($args, null);
	return call_user_func_array('array_map', $args);
}

/**
 * @param array $array1
 * @param array $array2
 * @param callable $callback
 * @return array
 */
function ZipWith($array1, $array2, $callback)
{
	$result = array();
	foreach ($array1 as $a) {
		list(,$b) = each($array2);
		$result[] = $callback($a, $b);
	}

	return $result;
}


// ----- Sorting -----

/**
 * Returns a copy of the array, sorted by a key or result of a callback
 *
 * @param array $array
 * @param callable|string $callbackOrKey
 * @param int $mode Sort flags
 * @return array
 */
function SortBy($array, $callbackOrKey, $mode = SORT_REGULAR)
{
	$sortBy = array();
	if (is_string($callbackOrKey)) {
		foreach ($array as $value) {
			$sortBy[] = $value[$callbackOrKey];
		}
	} else {
		foreach ($array as $key => $value) {
			$sortBy[] = $callbackOrKey($value, $key);
		}
	}

	array_multisort($sortBy, $mode, $array);

	return $array;
}
