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

class Fluent implements \ArrayAccess, \Iterator, \Countable
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
	 * Creates a new Gears\Arrays\Fluent object.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $array - A PHP array to turn into a Gears\Arrays\Fluent object.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public function __construct($array)
	{
		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				$this->value[$key] = new static($value);
			}
			else
			{
				$this->value[$key] = $value;
			}
		}
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
		return current($this->value);
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
			return $this->value[$index];
		}
		else
		{
			return null;
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
		if (is_array($value)) $value = new static($value);

		if (is_null($index))
		{
			$this->value[] = $value;
		}
		else
		{
			$this->value[$index] = $value;
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
		return \Gears\Arrays\toString($this->value);
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
			// NOTE: Macros cant have values passed by reference
			if (\Gears\Arrays::hasMacro($name))
			{
				$func_name = '\Gears\Arrays::'.$name;
			}
			else
			{
				// Bail out, we don't have a function to run
				throw new InvalidMethod();
			}
		}

		// Prepend the current string value to the arguments
		// Some functions require a reference so we define it here
		$refrenced_args = [&$this->value];
		foreach ($arguments as $arg) $refrenced_args[] = $arg;
		$arguments = $refrenced_args;

		// Call the function
		$result = call_user_func_array($func_name, $arguments);

		if (empty($result))
		{
			// Nothing to return so just return the current instance.
			// This probably means a function that acted on the array
			// by refrence ran.
			return $this;
		}
		else
		{
			// Return a new instance of ourselves with the new results.
			if (is_array($result))
			{
				return new static($result);
			}
			else
			{
				return $result;
			}
		}
	}
}
