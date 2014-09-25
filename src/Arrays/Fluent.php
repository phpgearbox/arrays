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

use \Gears\Arrays\Exceptions\InvalidMethod;
use \Gears\Arrays\Exceptions\InvalidOffset;

class Fluent implements \ArrayAccess, \Iterator, \Countable, \Serializable
{
	/**
	 * Property: $value
	 * =========================================================================
	 * This stores the actual array that this object represents.
	 */
	protected $value = array();
	
	/**
	 * Method: __construct
	 * =========================================================================
	 * Simply casts the input to an array and saves it to our new object.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $array - A PHP array to turn into a Gears\Arrays\Fluent object.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public function __construct(array $array = array())
	{
		$this->value = $array;
	}

	public static function make($items)
	{
		if (is_null($items)) return new static;

		if ($items instanceof Fluent) return $items;

		return new static(is_array($items) ? $items : array($items));
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
	 * void
	 */
	public function rewind()
	{
		reset($this->value);
	}
	
	/**
	 * Method: current
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
	public function current()
	{
		return $this->lazyLoadFluent(key($this->value));
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
	 * void
	 */
	public function next()
	{
		next($this->value);
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
		return isset($this->value[$index]);
	}

	public function __isset($name)
	{
		return $this->offsetExists($name);
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
		if ($this->offsetExists($index))
		{
			return $this->lazyLoadFluent($index);
		}
		else
		{
			throw new InvalidOffset($index);
		}
	}

	public function __get($name)
	{
		return $this->offsetGet($name);
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
	public function offsetSet($index, $value)
	{
		if (is_null($index))
		{
			$this->value[] = $value;
		}
		else
		{
			// This optionally allows dot notation - so you could do stuff like.
			// $test['a.b.c'] = '123';
			$this->set($index, $value);
		}
	}

	public function __set($name, $value)
	{
		$this->offsetSet($name, $value);
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

	public function __unset($name)
	{
		return $this->offsetUnset($name);
	}

	/**
	 * Method: serialize
	 * =========================================================================
	 * Serializable Interface
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * n/a
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * string
	 */
	public function serialize()
	{
		return serialize($this->toArray());
	}

	/**
	 * Method: unserialize
	 * =========================================================================
	 * Serializable Interface
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $serialized - The string to turnh into a new object.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public function unserialize($serialized)
	{
		$this->value = unserialize($serialized);
	}

	/**
	 * Method: lazyLoadFluent
	 * =========================================================================
	 * So that we don't waste time recursively creating a heap of
	 * Gears\Arrays\Fluent objects unnecessarily we only do so right before
	 * they are needed.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $array - A PHP array to turn into a Gears\Arrays\Fluent object.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	private function lazyLoadFluent($key)
	{
		if (is_array($this->value[$key]))
		{
			$this->value[$key] = new static($this->value[$key]);
		}

		return $this->value[$key];
	}
	
	/**
	 * Method: __toString
	 * =========================================================================
	 * Magic method to turn ourselves into an easily readable
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
		return print_r($this->value, true);
	}
	
	// Alias for above
	public function toString()
	{
		return $this->__toString();
	}
	
	/**
	 * Method: toArray
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
	public function toArray($recursive = false)
	{
		if (!$recursive) return $this->value;
		
		return array_map(function($array)
		{
			if ($array instanceof \Gears\Arrays\Fluent)
			{
				return $array->toArray(true);
			}
			else
			{
				return $array;
			}
		}, $this->value);
	}
	
	/**
	 * Method: hook
	 * =========================================================================
	 * Invokes a callback passing the underlying array as the argument,
	 * ignoring the return value. Useful for debugging in the middle of a chain.
	 * Can also be used to modify the object, although doing so is discouraged.
	 * 
	 * For example:
	 * 
	 * 	$obj = new Gears\Arrays\Fluent($array);
	 * 	$obj
	 * 		->filter(function ($v) { return $v % 2 != 0; })
	 * 		->hook(function ($arr) { array_unshift($arr, 0); }) // Add back zero
	 * 		->map(function ($v) { return $v * $v; })
	 * 		->hook(function ($arr) { var_dump($arr); }) // Debug
	 * 		->sum()
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
	public function hook(\Closure $callback)
	{
		call_user_func_array($callback, [&$this->value]);
		return $this;
	}

	/**
	 * Method: __call
	 * =========================================================================
	 * This is what creates the fluent api. This class is just a fancy
	 * container and has no real functionality at all.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $name - The name of the \Gears\String\"FUNCTION" to call.
	 * $arguments - The arguments to pass to the function.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * mixed
	 */
	public function __call($name, $arguments)
	{
		// Create the function name
		$func_name = '\Gears\Arrays\\'.$name;

		// Does the function exist
		if (!function_exists($func_name))
		{
			// Try a macro
			if (\Gears\Arrays::hasMacro($name))
			{
				$func_name = '\Gears\Arrays::'.$name;
			}
			else
			{
				// Bail out, we don't have a function to run
				throw new InvalidMethod($name);
			}
		}

		// Prepend the current string value to the arguments
		// Some functions require a reference so we define it here.
		// NOTE: Macros can't have values passed by reference.
		$refrenced_args = [&$this->value];
		foreach ($arguments as $arg) $refrenced_args[] = $arg;
		$arguments = $refrenced_args;

		// Call the function
		$result = call_user_func_array($func_name, $arguments);

		if (empty($result))
		{
			// Nothing to return so just return the current instance.
			// This probably means a function that acted on the array
			// by reference ran.
			return $this;
		}
		else
		{
			if (is_array($result))
			{
				// Return a new instance of ourselves with the new results.
				return new static($result);
			}
			else
			{
				// Otherwise we just return the result from the function.
				// This is now the end of our Gears\Array\Fluent object.
				return $result;
			}
		}
	}
}
