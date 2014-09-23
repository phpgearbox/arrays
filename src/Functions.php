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

/**
 * Function: ToString
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
function ToString($array, $showKeys = null)
{
	// Set a counter
	$idx = 0;
	
	// Remap the array
	$remapped = MapWithKey($array, function ($v, $k) use (&$idx, $showKeys)
	{
		if ($showKeys === null && $idx++ === $k || $showKeys === false)
		{
			$str = '';
		}
		else
		{
			$str = "$k => ";
		}
		
		if (is_array($v))
		{
			$str .= ToString($v, $showKeys);
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
 * Function: GetNested
 * =============================================================================
 * Retrieves a nested element from an array or $default if it doesn't exist.
 * 
 * 	$friends =
 * 	[
 * 		'Alice' => ['age' => 33, 'hobbies' => ['biking', 'skiing']],
 * 		'Bob' => ['age' => 29],
 * 	];
 * 	
 * 	GetNested($friends, 'Alice.hobbies.1'); //=> 'skiing'
 * 	GetNested($friends, ['Alice', 'hobbies', 1]); //=> 'skiing'
 * 	GetNested($friends, 'Bob.hobbies.0', 'none'); //=> 'none'
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
function GetNested($array, $keys, $default = null)
{
	if (is_string($keys))
	{
		$keys = explode('.', $keys);
	}
	else if ($keys === null)
	{
		return $array;
	}
	
	foreach ($keys as $key)
	{
		if (is_array($array) && array_key_exists($key, $array))
		{
			$array = $array[$key];
		}
		else
		{
			return $default;
		}
	}
	
	return $array;
}

/**
 * Function: GetOrElse
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
function GetOrElse($array, $key, $default = null)
{
	return array_key_exists($key, $array) ? $array[$key] : $default;
}

/**
 * Function: GetOrPut
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
function GetOrPut(&$array, $key, $default = null)
{
	if (!array_key_exists($key, $array))
	{
		$array[$key] = $default;
	}
	
	return $array[$key];
}

/**
 * Function: GetAndDelete
 * =============================================================================
 * Deletes and returns a value from an array
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to search / update.
 * $key - The key to search for
 * $default - An optional default to return when the key doesn't exist.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * mixed
 */
function GetAndDelete(&$array, $key, $default = null)
{
	if (array_key_exists($key, $array))
	{
		$result = $array[$key];
		unset($array[$key]);
		return $result;
	}
	else
	{
		return $default;
	}
}

/**
 * Function: TakeWhile
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
function TakeWhile($array, $predicate)
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
 * Function: DropWhile
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
function DropWhile($array, $predicate)
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
 * Function: Repeat
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
function Repeat($array, $n)
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
 * Function: Find
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
function Find($array, $predicate, $default = null)
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
 * Function: FindLast
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
function FindLast($array, $predicate, $default = null)
{
	return Find(array_reverse($array, true), $predicate, $default);
}

/**
 * Function: FindKey
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
function FindKey($array, $predicate, $default = null)
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
 * Function: FindLastKey
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
function FindLastKey($array, $predicate, $default = null)
{
	return FindKey(array_reverse($array, true), $predicate, $default);
}

/**
 * Function: Only
 * =============================================================================
 * Returns only those values whose keys are present in $keys
 * 
 * 	Only(range('a', 'e'), [3, 4]); //=> ['d', 'e']
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to search
 * $keys - The keys youi want
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function Only($array, $keys)
{
	return array_intersect_key($array, array_flip($keys));
}

/**
 * Function: Except
 * =============================================================================
 * Returns only those values whose keys are not present in $keys
 * 
 * 	Except(range('a', 'e'), [2, 4]); //=> ['a', 'b', 'd']
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to search
 * $keys - The keys you dont want
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function Except($array, $keys)
{
	return array_diff_key($array, array_flip($keys));
}

/**
 * Function: IndexBy
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
function IndexBy($array, $callbackOrKey, $arrayAccess = true)
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
 * Function: GroupBy
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
function GroupBy($array, $callbackOrKey, $arrayAccess = true)
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
 * Function: All
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
function All($array, $predicate)
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
 * Function: Any
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
function Any($array, $predicate)
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
 * Function: One
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
function One($array, $predicate)
{
	return Exactly($array, 1, $predicate);
}

/**
 * Function: None
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
function None($array, $predicate)
{
	return Exactly($array, 0, $predicate);
}

/**
 * Function: Exactly
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
function Exactly($array, $n, $predicate)
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
 * Function: FilterWithKey
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
function FilterWithKey($array, $predicate)
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
 * Function: Sample
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
function Sample($array, $size = null)
{
	if ($size === null)
	{
		return $array[array_rand($array)];
	}
	else
	{
		return Only($array, (array)array_rand($array, $size));
	}
}

/**
 * Function: MapWithKey
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
function MapWithKey($array, $callback)
{
	$mapped = array();
	
	foreach ($array as $key => $value)
	{
		$mapped[$key] = $callback($value, $key);
	}
	
	return $mapped;
}

/**
 * Function: FlatMap
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
function FlatMap($array, $callback)
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
 * Function: Pluck
 * =============================================================================
 * Shortcut method to pick out specified keys/properties
 * from an array of arrays/objects.
 * 
 * 	$people =
 * 	[
 * 	     ['name' => 'Bob', 'age' => 23],
 * 	     ['name' => 'Alice', 'age' => 32],
 * 	     ['name' => 'Frank', 'age' => 40],
 * 	];
 * 	
 * 	Pluck($people, 'name'); //=> ['Bob', 'Alice', 'Frank']
 * 	Pluck($people, 'age', 'name'); //=> ['Bob' => 23, 'Alice' => 32, 'Frank' => 40]
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array -
 * $valueAttribute - 
 * $keyAttribute - 
 * $arrayAccess - Determines whether to use array access ($elem[$prop]) or property access ($elem->$prop)
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function Pluck($array, $valueAttribute, $keyAttribute = null, $arrayAccess = true)
{
	$result = array();
	
	if ($arrayAccess)
	{
		if ($keyAttribute)
		{
			foreach ($array as $value)
			{
				$result[$value[$keyAttribute]] = $value[$valueAttribute];
			}
		}
		else
		{
			foreach ($array as $key => $value)
			{
				$result[$key] = $value[$valueAttribute];
			}
		}
	}
	else
	{
		if ($keyAttribute)
		{
			foreach ($array as $value)
			{
				$result[$value->{$keyAttribute}] = $value->{$valueAttribute};
			}
		}
		else
		{
			foreach ($array as $key => $value)
			{
				$result[$key] = $value->{$valueAttribute};
			}
		}
	}
	
	return $result;
}

/**
 * Function: MapToAssoc
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
function MapToAssoc($array, $callback)
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
 * Function: Flatten
 * =============================================================================
 * Flattens the array, combining elements of all sub-arrays into one array.
 * 
 * 	Flatten([[1, 2, 3], [4, 5]]); //=> [1, 2, 3, 4, 5]
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to flatten
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * array
 */
function Flatten($array)
{
	return call_user_func_array('array_merge', $array);
}

/**
 * Function: FoldWithKey
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
function FoldWithKey($array, $callback, $initial = null)
{
	foreach ($array as $key => $value)
	{
		$initial = $callback($initial, $value, $key);
	}
	
	return $initial;
}

/**
 * Function: FoldRight
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
function FoldRight($array, $callback, $initial = null)
{
	return array_reduce(array_reverse($array, true), $callback, $initial);
}

/**
 * Function: FoldRightWithKey
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
function FoldRightWithKey($array, $callback, $initial = null)
{
	return FoldWithKey(array_reverse($array, true), $callback, $initial);
}

/**
 * Function: MinBy
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
function MinBy($array, $callback)
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
 * Function: MaxBy
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
function MaxBy($array, $callback)
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
 * Function: SumBy
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
function SumBy($array, $callback)
{
	$sum = 0;
	
	foreach ($array as $value)
	{
		$sum += $callback($value);
	}
	
	return $sum;
}

/**
 * Function: Partition
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
function Partition($array, $predicate)
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
 * Function: Zip
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
function Zip()
{
	$args = func_get_args();
	array_unshift($args, null);
	return call_user_func_array('array_map', $args);
}

/**
 * Function: ZipWith
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
function ZipWith($array1, $array2, $callback)
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
 * Function: SortBy
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
function SortBy($array, $callbackOrKey, $mode = SORT_REGULAR)
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
