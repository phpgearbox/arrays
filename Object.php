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

class Object implements \ArrayAccess, \IteratorAggregate
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
	 * Method: getIterator
	 * =========================================================================
	 * Magic method to allow one to use foreach and other loops
	 * just like a normal array.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * n/a
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->value);
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
		return $this->value[$index];
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
	
	public function Search($search, $exact = true, $trav_keys = null)
	{
		return \Gears\Arrays\Search($this->value, $search, $exact, $trav_keys);
	}
	
	public function GetNested($keys, $default = null)
	{
		return \Gears\Arrays\GetNested($this->value, $keys, $default);
	}
	
	public function GetOrElse($key, $default = null)
	{
		return \Gears\Arrays\GetOrElse($this->value, $key, $default);
	}
	
	public function GetOrPut($key, $default = null)
	{
		return \Gears\Arrays\GetOrPut(&$this->value, $key, $default);
	}
	
	public function GetAndDelete(&$array, $key, $default = null)
	{
		return \Gears\Arrays\GetAndDelete(&$this->value, $key, $default);
	}
	
	public function TakeWhile($predicate)
	{
		return \Gears\Arrays\TakeWhile($this->value, $predicate);
	}
	public function DropWhile($predicate)
	{
		return \Gears\Arrays\DropWhile($this->value, $predicate);
	}
	
	public function Repeat($n)
	{
		return \Gears\Arrays\Repeat($this->value, $n);
	}
	
	public function Find($predicate, $default = null)
	{
		return \Gears\Arrays\Find($this->value, $predicate, $default);
	}
	
	public function FindLast($predicate, $default = null)
	{
		return \Gears\Arrays\FindLast($this->value, $predicate, $default);
	}
	
	public function FindKey($predicate, $default = null)
	{
		return \Gears\Arrays\FindKey($this->value, $predicate, $default);
	}
	
	public function FindLastKey($predicate, $default = null)
	{
		return \Gears\Arrays\FindLastKey($this->value, $predicate, $default);
	}
	
	public function Only($keys)
	{
		return \Gears\Arrays\Only($this->value, $keys);
	}
	
	public function Except($keys)
	{
		return \Gears\Arrays\Except($this->value, $keys);
	}
	
	public function IndexBy($callbackOrKey, $arrayAccess = true)
	{
		return \Gears\Arrays\IndexBy($this->value, $callbackOrKey, $arrayAccess);
	}
	
	public function GroupBy($callbackOrKey, $arrayAccess = true)
	{
		return \Gears\Arrays\GroupBy($this->value, $callbackOrKey, $arrayAccess);
	}
	
	public function All($predicate)
	{
		return \Gears\Arrays\All($this->value, $predicate);
	}
	
	public function Any($predicate)
	{
		return \Gears\Arrays\Any($this->value, $predicate);
	}
	
	public function One($predicate)
	{
		return \Gears\Arrays\One($this->value, $predicate);
	}
	
	public function None($predicate)
	{
		return \Gears\Arrays\None($this->value, $predicate);
	}
	
	public function Exactly($n, $predicate)
	{
		return \Gears\Arrays\Exactly($this->value, $n, $predicate);
	}
	
	public function FilterWithKey($predicate)
	{
		return \Gears\Arrays\FilterWithKey($this->value, $predicate);
	}
	
	public function Sample($size = null)
	{
		return \Gears\Arrays\Sample($this->value, $size);
	}
	
	public function MapWithKey($callback)
	{
		return \Gears\Arrays\MapWithKey($this->value, $callback);
	}
	
	public function FlatMap($callback)
	{
		return \Gears\Arrays\FlatMap($this->value, $callback);
	}
	
	public function Pluck($valueAttribute, $keyAttribute = null, $arrayAccess = true)
	{
		return \Gears\Arrays\Pluck($this->value, $valueAttribute, $keyAttribute, $arrayAccess);
	}
	
	public function MapToAssoc($callback)
	{
		return \Gears\Arrays\MapToAssoc($this->value, $callback);
	}
	
	public function Flatten()
	{
		return \Gears\Arrays\Flatten($this->value);
	}
	
	public function FoldWithKey($callback, $initial = null)
	{
		return \Gears\Arrays\FoldWithKey($this->value, $callback, $initial);
	}
	
	public function FoldRight($callback, $initial = null)
	{
		return \Gears\Arrays\FoldRight($this->value, $callback, $initial);
	}
	
	public function FoldRightWithKey($callback, $initial = null)
	{
		return \Gears\Arrays\FoldRightWithKey($this->value, $callback, $initial);
	}
	
	public function MinBy($callback)
	{
		return \Gears\Arrays\MinBy($this->value, $callback);
	}
	
	public function MaxBy($callback)
	{
		return \Gears\Arrays\MaxBy($this->value, $callback);
	}
	
	public function SumBy($callback)
	{
		return \Gears\Arrays\SumBy($this->value, $callback);
	}
	
	public function Partition($predicate)
	{
		return \Gears\Arrays\Partition($this->value, $predicate);
	}
	
	public function SortBy($callbackOrKey, $mode = SORT_REGULAR)
	{
		return \Gears\Arrays\SortBy($this->value, $callbackOrKey, $mode);
	}
}
