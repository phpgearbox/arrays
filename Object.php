<?php
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

namespace Gears\Arrays;

class Object implements \ArrayAccess, \Iterator, \Countable
{
	/**
	 * Property: $value
	 * =========================================================================
	 * This stores the actual array that this object represents.
	 */
	private $value;
	
	/**
	 * Method: __construct
	 * =========================================================================
	 * Creates a new Gears\Arrays\Object
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $array - A PHP array to turn into a Gears\Arrays\Object
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public function __construct($array)
	{
		$this->value = (array)$array;
	}
	
	/**
	 * Method: F
	 * =========================================================================
	 * This is the static factory method allowing a syntax like this:
	 * 
	 * 	Gears\Arrays\Object::F($array)->Each(function($k, $v){ echo $k.$v; });
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $array - A PHP array to turn into a Gears\Arrays\Object
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * A new instance of Gears\Arrays\Object
	 */
	public static function F($array)
	{
		return new self($array);
	}
	
	/**
	 * Method: count
	 * =========================================================================
	 * Provides the Countable Implementation
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * n/a
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * int
	 */
	public function count()
	{
		return count($this->value);
	}
	
	/**
	 * Method: rewind
	 * =========================================================================
	 * Provides the Iterator Implementation
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * n/a
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * int
	 */
	public function rewind()
	{
		return reset($this->value);
	}
	
	/**
	 * Method: current
	 * =========================================================================
	 * Provides the Iterator Implementation
	 * 
	 * THIS IS KEY - We return a new instance of Gears\Arrays\Object
	 * if the value is another array.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * n/a
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * int
	 */
	public function current()
	{
		return $this->ReturnSelf(current($this->value));
	}
	
	/**
	 * Method: key
	 * =========================================================================
	 * Provides the Iterator Implementation
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * n/a
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * int
	 */
	public function key()
	{
		return key($this->value);
	}
	
	/**
	 * Method: next
	 * =========================================================================
	 * Provides the Iterator Implementation
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * n/a
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * int
	 */
	public function next()
	{
		return next($this->value);
	}
	
	/**
	 * Method: valid
	 * =========================================================================
	 * Provides the Iterator Implementation
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * n/a
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * int
	 */
	public function valid()
	{
		return key($this->value) !== null;
	}
	
	/**
	 * Method: offsetExists
	 * =========================================================================
	 * ArrayAccess method, checks to see if the key actually exists.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $index - The integer of the index to check.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * boolean
	 */
	public function offsetExists($index)
	{
		return !empty($this->value[$index]);
	}
	
	/**
	 * Method: offsetGet
	 * =========================================================================
	 * ArrayAccess method, retrieves an array value.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $index - The integer of the index to get.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * string
	 */
	public function offsetGet($index)
	{
		return $this->ReturnSelf($this->value[$index]);
	}
	
	/**
	 * Method: offsetSet
	 * =========================================================================
	 * ArrayAccess method, sets an array value.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $index - The integer of the index to set.
	 * $val - The new value for the index.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public function offsetSet($index, $val)
	{
		$this->value[$index] = $value;
	}
	
	/**
	 * Method: offsetUnset
	 * =========================================================================
	 * ArrayAccess method, removes an array value.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $index - The integer of the index to delete.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public function offsetUnset($index)
	{
		unset($this->value[$index]);
	}
	
	/**
	 * Method: ReturnSelf
	 * =========================================================================
	 * Used internally to return a new instance of our self
	 * thus providing a recursive interface.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $value - The value to check if it is an array or not
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * A new instance of Gears\Arrays\Object if $value is an array.
	 * Otherwise we just pass on $value untouched.
	 */
	private function ReturnSelf($value)
	{
		if (is_array($value))
		{
			return new self($value);
		}
		else
		{
			return $value;
		}
	}
	
	/**
	 * Method: __toString
	 * =========================================================================
	 * Magic method to turn Gears\Arrays\Object into an easily readable
	 * string representation of the array structure.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * n/a
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * string
	 */
	public function __toString()
	{
		return \Gears\Arrays\ToString($this->value);
	}
	
	// Alias for above
	public function ToString()
	{
		return $this->__toString();
	}
	
	/**
	 * Method: ToArray
	 * =========================================================================
	 * If you would like to just get a standard PHP array, call this.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $recursive - Used on subsequent recursive calls
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * array
	 */
	public function ToArray($recursive = false)
	{
		if (!$recursive) return $this->value;
		
		return array_map(function($array)
		{
			if ($array instanceof \Gears\Arrays\Object)
			{
				return $array->ToArray(true);
			}
			else
			{
				return $array;
			}
		}, $this->value);
	}
	
	/**
	 * Method: First
	 * =========================================================================
	 * Grab the first element of the array
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * n/a
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * mixed
	 */
	public function First()
	{
		return $this->rewind();
	}
	
	/**
	 * Method: Last
	 * =========================================================================
	 * Grab the last element of the array
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * n/a
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * mixed
	 */
	public function Last()
	{
		return end($this->value);
	}
	
	/**
	 * Method: Each
	 * =========================================================================
	 * This allows you to do something like this:
	 * 
	 * 	$obj = new Gears\Arrays\Object($array);
	 * 	$obj->Each(function($key, $value, $index)
	 * 	{
	 * 		echo 'KEY: '.$key."\n";
	 * 		echo 'VALUE: '.$value."\n";
	 * 		echo 'INDEX: '.$index."\n";
	 * 	});
	 * 
	 * The key and value or self explanatory, the index is totally optional.
	 * In fact you don't have to use any of the paranmeters in your callback
	 * if you don't want to.
	 * 
	 * I digress the index is simply a counter starting at zero increasing by
	 * 1 for each interation. With a simple "not-named keys" array you already
	 * have this value as the key. But with "named keys" you miss out on this.
	 * Sometimes I find myself doing things like this:
	 * 
	 * 	$index = 0;
	 * 	
	 * 	foreach ($array as $key => $value)
	 * 	{
	 * 		// We only want to do 10 of these
	 * 		if ($index > 10) break;
	 * 		
	 * 		// Do something with key and value
	 * 		echo 'KEY: '.$key."\n";
	 * 		echo 'VALUE: '.$value."\n";
	 * 		
	 * 		// Increase the index
	 * 		$index++;
	 * 	}
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $callback - callable($key, $value, $index);
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * self
	 */
	public function Each($callback)
	{
		$index = 0;
		
		foreach ($this->value as $key => $value)
		{
			$callback($key, $this->ReturnSelf($value), $index++);
		}
		
		return $this;
	}
	
	/**
	 * Method: Hook
	 * =========================================================================
	 * Invokes a callback passing the underlying array as the argument,
	 * ignoring the return value. Useful for debugging in the middle of a chain.
	 * Can also be used to modify the object, although doing so is discouraged.
	 * 
	 * For example:
	 * 
	 * 	$obj = new Gears\Arrays\Object($array);
	 * 	$obj
	 * 		->Filter(function ($v) { return $v % 2 != 0; })
	 * 		->Hook(function ($arr) { array_unshift($arr, 0); }) // Add back zero
	 * 		->Map(function ($v) { return $v * $v; })
	 * 		->Hook(function ($arr) { var_dump($arr); }) // Debug
	 * 		->Sum()
	 * 	;
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $callback - callable($array);
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * self
	 */
	public function Hook($callback)
	{
		$callback($this->value);
		return $this;
	}
	
	public function Convert()
	{
		
	}
	
	/**
	 * Method: Debug
	 * =========================================================================
	 * If you specfically want to dump the contents of the array just call this
	 * instead of the hook method. This will work out if your working on
	 * the command line or in a web browser and format the output
	 * apprioratly so that it is easily readable.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * n/a
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * self
	 */
	public function Debug()
	{
		\Gears\Arrays\Debug($this->value);
		return $this;
	}
	
	/*
	 * Below here are all the aliased methods contained in the Functions.php
	 * file. So go look in there for documentation. The only real difference
	 * is that while in the procudual API the first argument will always
	 * be an array, when using this object you obviously omit that.
	 */
	
	public function Search($search, $exact = true, $trav_keys = null)
	{
		return $this->ReturnSelf(\Gears\Arrays\Search($this->value, $search, $exact, $trav_keys));
	}
	
	public function GetNested($keys, $default = null)
	{
		return $this->ReturnSelf(\Gears\Arrays\GetNested($this->value, $keys, $default));
	}
	
	public function GetOrElse($key, $default = null)
	{
		return $this->ReturnSelf(\Gears\Arrays\GetOrElse($this->value, $key, $default));
	}
	
	public function GetOrPut($key, $default = null)
	{
		return $this->ReturnSelf(\Gears\Arrays\GetOrPut($this->value, $key, $default));
	}
	
	public function GetAndDelete($key, $default = null)
	{
		return $this->ReturnSelf(\Gears\Arrays\GetAndDelete($this->value, $key, $default));
	}
	
	public function TakeWhile($predicate)
	{
		return $this->ReturnSelf(\Gears\Arrays\TakeWhile($this->value, $predicate));
	}
	public function DropWhile($predicate)
	{
		return $this->ReturnSelf(\Gears\Arrays\DropWhile($this->value, $predicate));
	}
	
	public function Repeat($n)
	{
		return $this->ReturnSelf(\Gears\Arrays\Repeat($this->value, $n));
	}
	
	public function Find($predicate, $default = null)
	{
		return $this->ReturnSelf(\Gears\Arrays\Find($this->value, $predicate, $default));
	}
	
	public function FindLast($predicate, $default = null)
	{
		return $this->ReturnSelf(\Gears\Arrays\FindLast($this->value, $predicate, $default));
	}
	
	public function FindKey($predicate, $default = null)
	{
		return $this->ReturnSelf(\Gears\Arrays\FindKey($this->value, $predicate, $default));
	}
	
	public function FindLastKey($predicate, $default = null)
	{
		return $this->ReturnSelf(\Gears\Arrays\FindLastKey($this->value, $predicate, $default));
	}
	
	public function Only($keys)
	{
		return $this->ReturnSelf(\Gears\Arrays\Only($this->value, $keys));
	}
	
	public function Except($keys)
	{
		return $this->ReturnSelf(\Gears\Arrays\Except($this->value, $keys));
	}
	
	public function IndexBy($callbackOrKey, $arrayAccess = true)
	{
		return $this->ReturnSelf(\Gears\Arrays\IndexBy($this->value, $callbackOrKey, $arrayAccess));
	}
	
	public function GroupBy($callbackOrKey, $arrayAccess = true)
	{
		return $this->ReturnSelf(\Gears\Arrays\GroupBy($this->value, $callbackOrKey, $arrayAccess));
	}
	
	public function All($predicate)
	{
		return $this->ReturnSelf(\Gears\Arrays\All($this->value, $predicate));
	}
	
	public function Any($predicate)
	{
		return $this->ReturnSelf(\Gears\Arrays\Any($this->value, $predicate));
	}
	
	public function One($predicate)
	{
		return $this->ReturnSelf(\Gears\Arrays\One($this->value, $predicate));
	}
	
	public function None($predicate)
	{
		return $this->ReturnSelf(\Gears\Arrays\None($this->value, $predicate));
	}
	
	public function Exactly($n, $predicate)
	{
		return $this->ReturnSelf(\Gears\Arrays\Exactly($this->value, $n, $predicate));
	}
	
	public function FilterWithKey($predicate)
	{
		return $this->ReturnSelf(\Gears\Arrays\FilterWithKey($this->value, $predicate));
	}
	
	public function Sample($size = null)
	{
		return $this->ReturnSelf(\Gears\Arrays\Sample($this->value, $size));
	}
	
	public function MapWithKey($callback)
	{
		return $this->ReturnSelf(\Gears\Arrays\MapWithKey($this->value, $callback));
	}
	
	public function FlatMap($callback)
	{
		return $this->ReturnSelf(\Gears\Arrays\FlatMap($this->value, $callback));
	}
	
	public function Pluck($valueAttribute, $keyAttribute = null, $arrayAccess = true)
	{
		return $this->ReturnSelf(\Gears\Arrays\Pluck($this->value, $valueAttribute, $keyAttribute, $arrayAccess));
	}
	
	public function MapToAssoc($callback)
	{
		return $this->ReturnSelf(\Gears\Arrays\MapToAssoc($this->value, $callback));
	}
	
	public function Flatten()
	{
		return $this->ReturnSelf(\Gears\Arrays\Flatten($this->value));
	}
	
	public function FoldWithKey($callback, $initial = null)
	{
		return $this->ReturnSelf(\Gears\Arrays\FoldWithKey($this->value, $callback, $initial));
	}
	
	public function FoldRight($callback, $initial = null)
	{
		return $this->ReturnSelf(\Gears\Arrays\FoldRight($this->value, $callback, $initial));
	}
	
	public function FoldRightWithKey($callback, $initial = null)
	{
		return $this->ReturnSelf(\Gears\Arrays\FoldRightWithKey($this->value, $callback, $initial));
	}
	
	public function MinBy($callback)
	{
		return $this->ReturnSelf(\Gears\Arrays\MinBy($this->value, $callback));
	}
	
	public function MaxBy($callback)
	{
		return $this->ReturnSelf(\Gears\Arrays\MaxBy($this->value, $callback));
	}
	
	public function SumBy($callback)
	{
		return $this->ReturnSelf(\Gears\Arrays\SumBy($this->value, $callback));
	}
	
	public function Partition($predicate)
	{
		return $this->ReturnSelf(\Gears\Arrays\Partition($this->value, $predicate));
	}
	
	public function SortBy($callbackOrKey, $mode = SORT_REGULAR)
	{
		return $this->ReturnSelf(\Gears\Arrays\SortBy($this->value, $callbackOrKey, $mode));
	}
}
