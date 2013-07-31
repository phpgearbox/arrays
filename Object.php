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

class Object implements \ArrayAccess, \Iterator, \Countable
{
	/**
	 * Property: $position
	 * =========================================================================
	 * Used to provide the Iterator Interface
	 */
	private $position = 0;
	
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
	
	function rewind()
	{
		$this->position = 0;
	}
	
	function current()
	{
		return $this->value[$this->position];
	}
	
	function key()
	{
		return $this->position;
	}
	
	function next()
	{
		++$this->position;
	}
	
	function valid()
	{
		return isset($this->value[$this->position]);
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
	 * Function: ReturnSelf
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
	
	public function Each($callback)
	{
		$index = 0;
		
		foreach ($this->value as $key => $value)
		{
			$callback($key, $value, $index++);
		}
		
		return $this;
	}
	
	public function Debug()
	{
		return $this->ReturnSelf(\Gears\Arrays\Debug($this->value));
	}
	
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
