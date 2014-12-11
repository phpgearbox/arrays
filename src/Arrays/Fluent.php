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

use \Illuminate\Support\Collection;
use \Gears\Arrays\Iterator;
use \Gears\Arrays\Exceptions\InvalidMethod;

class Fluent extends Collection
{
	/**
	 * Method: convert
	 * =========================================================================
	 * This is a convenience front end to our conversion classes.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * This method uses some smart dynamic parameters matching.
	 * Best shown with an example:
	 *
	 * ```php
	 * Arr::a([1,2,3])->convert('csv') // 1,2,3
	 * Arr::a()->convert('1,2,3', 'csv') // array(1,2,3)
	 * Arr::a([1,2,3])->convert('csv', ['delimiter' => "-"]) // 1-2-3
	 * Arr::a()->convert('1-2-3', 'csv', ['delimiter' => "-"]) // array(1,2,3)
	 * ```
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * mixed
	 */
	public function convert($arg1, $arg2 = null, $arg3 = array())
	{
		// Create the converter class names
		if (is_null($arg2) || is_array($arg2))
		{
			$converter = '\Gears\Arrays\Conversions\To\\'.ucfirst($arg1);
			$data = $this->items;
			$options = $arg2;
		}
		else
		{
			$converter = '\Gears\Arrays\Conversions\From\\'.ucfirst($arg2);
			$data = $arg1;
			$options = $arg3;
		}

		// Initiate a new converter
		$converter =  new $converter($options);

		// Run the conversion
		$results = $converter->Convert($data);

		// Return a new version of ourself if the result is an array
		if (is_array($results))
		{
			return new static($results);
		}
		else
		{
			return $results;
		}
	}

	/**
	 * Method: get
	 * =========================================================================
	 * Get an item from the collection by key.
	 * 
	 * For example:
	 * 
	 * ```php
	 * $data = Arr::a(['a' => ['b' => ['c' => 'd']]]);
	 * echo $data->get('a.b.c'); // outputs: d
	 * ```
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $key - Can be a single key, "dot" separated keys or an array of keys.
	 * $default - What value would you like to have returned on failure?
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * mixed
	 */
	public function get($key, $default = null)
	{
		// Get the keys array
		if (is_string($key))
		{
			// We found the key in the root of the array
			if ($this->offsetExists($key))
			{
				return $this->offsetGet($key);
			}

			$keys = explode('.', $key);
		}
		elseif (isArrayLike($key))
		{
			$keys = $key;
		}
		else
		{
			// Bail Out - we need a key
			// NOTE: value()_is a laravel helper
			return value($default);
		}

		// Start searching in the root of the array
		$array = $this->items;

		// Loop through the keys
		foreach ($keys as $x)
		{
			// Check to see if the key exists
			if (!isArrayLike($array) || !array_key_exists($x, $array))
			{
				// Bail out and return the default.
				// NOTE: value() is a laravel helper.
				return value($default);
			}

			// Keep recursing into the array
			$array = $array[$x];
		}

		if (is_array($array))
		{
			$value = new static($array);
			$this->set($key, $value);
		}
		else
		{
			$value = $array;
		}
		
		// Return the value
		return $value;
	}

	/**
	 * Method: set
	 * =========================================================================
	 * Set an item on the collection by key.
	 * 
	 * For example:
	 * 
	 * ```php
	 * $data = Arr::a();
	 * $data->set('a.b.c', 'd');
	 * echo $data->a->b->c; // outputs: d
	 * ```
	 * 
	 * NOTE: If no key is given to the method, the entire array will be
	 * replaced. This is in keeping with the procedual version of this method.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $key - Can be a single key, "dot" separated keys or an array of keys.
	 * $value - The value to set on the collection.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * static
	 */
	public function set($key, $value)
	{
		// Get the keys array
		if (is_string($key))
		{
			$keys = explode('.', $key);
		}
		elseif (isArrayLike($key))
		{
			$keys = $key;
		}
		else
		{
			// Assume key not provided, replace entire array with new value.
			$this->items = (array) $value;
			return $this;
		}

		// Start in the root of the array
		$array =& $this->items;

		// Loop through the keys
		while (count($keys) > 1)
		{
			// Get the next key
			$key = array_shift($keys);

			// If the key doesn't exist at this depth, we will just create an
			// empty array to hold the next value, allowing us to create the
			// arrays to hold final values at the correct depth. Then we'll
			// keep digging into the array.
			if (!isset($array[$key]) || !isArrayLike($array[$key]))
			{
				$array[$key] = new static();
			}

			// Recurse into the array
			$array =& $array[$key];
		}

		// Set the new value
		$array[array_shift($keys)] = $value;

		// Return ourselves to allow method chaining
		return $this;
	}

	/**
	 * Method: add
	 * =========================================================================
	 * Add an item to the collection by key, if it doesn't exist.
	 * 
	 * For example:
	 * 
	 * ```php
	 * $data = Arr::a(['a' => ['b' => ['c' => 'd']]]);
	 * $data->add('a', '123'); // fails
	 * $data->add('z', '123'); // works
	 * ```
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $key - Can be a single key, "dot" separated keys or an array of keys.
	 * $value - The value to set on the collection.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * static
	 */
	public function add($key, $value = null)
	{
		if (is_null($value))
		{
			$this->offsetSet(null, $key);
		}
		else
		{
			if (is_null($this->get($key)))
			{
				$this->set($key, $value);
			}
		}

		return $this;
	}

	/**
	 * Method: offsetGet
	 * =========================================================================
	 * Returns the value at specified offset.
	 * See: http://php.net/manual/en/class.arrayaccess.php
	 * 
	 * *We have overloaded the ```\Illuminate\Support\Collection```
	 * version so that we can provide a recursive interface.*
	 * 
	 * For example:
	 * 
	 * ```php
	 * $data = Arr:a([[1,2,3]]);
	 * print_r($data[0]);
	 * ```
	 * 
	 * Results in:
	 * 
	 * ```
	 * Gears\Arrays\Fluent Object
	 * (
	 * 		[items:protected] => Array
	 * 		(
	 * 			[0] => 1,
	 * 			[1] => 2,
	 * 			[2] => 3
	 * 		)
	 * )
	 * ```
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $key - The key of the underlying array to retrive.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * mixed
	 */
	public function offsetGet($key)
	{
		$value = parent::offsetGet($key);

		if (is_array($value)) $this->offsetSet($key, new static($value));

		return parent::offsetGet($key);
	}

	/**
	 * Method: getIterator
	 * =========================================================================
	 * This returns an array iterator so foreach works on our array like object.
	 * See: http://php.net/manual/en/class.iteratoraggregate.php
	 * 
	 * *We have overloaded the ```\Illuminate\Support\Collection```
	 * version so that we can provide a recursive interface.*
	 * 
	 * For example:
	 * 
	 * ```php
	 * foreach (Arr::a([[1,2,3]]) as $item)
	 * {
	 *  	print_r($item);
	 * }
	 * ```
	 * 
	 * Results in:
	 * 
	 * ```
	 * Gears\Arrays\Fluent Object
	 * (
	 * 		[items:protected] => Array
	 * 		(
	 * 			[0] => 1,
	 * 			[1] => 2,
	 * 			[2] => 3
	 * 		)
	 * )
	 * ```
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * n/a
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * \Gears\Arrays\Iterator
	 */
	public function getIterator()
	{
		return new Iterator($this->items);
	}

	/**
	 * Method: __set
	 * =========================================================================
	 * This is a PHP Magic Method.
	 * See: http://php.net/manual/en/language.oop5.magic.php
	 * 
	 * This enables the object access syntax:
	 * 
	 * ```php
	 * $data = Arr::a();
	 * $data->foo = 'bar';
	 * print_r($data);
	 * ```
	 * 
	 * The results of the above look like:
	 * 
	 * ```
	 * Gears\Arrays\Fluent Object
	 * (
	 * 		[items:protected] => Array
	 * 		(
	 * 			'foo' => 'bar'
	 * 		)
	 * )
	 * ```
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $name - The name or key of the item to set on the underlying array.
	 * $value - The value toi set on the underlying array.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public function __set($name, $value)
	{
		$this->offsetSet($name, $value);
	}

	/**
	 * Method: __get
	 * =========================================================================
	 * This is a PHP Magic Method.
	 * See: http://php.net/manual/en/language.oop5.magic.php
	 * 
	 * This enables the object access syntax:
	 * 
	 * ```php
	 * $data = Arr::a(['foo' => 'bar']);
	 * echo $data->foo; // Outputs: bar
	 * ```
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $name - The name or key of the item to get from the underlying array.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * mixed
	 */
	public function __get($name)
	{
		return $this->offsetGet($name);
	}

	/**
	 * Method: __isset
	 * =========================================================================
	 * This is a PHP Magic Method.
	 * See: http://php.net/manual/en/language.oop5.magic.php
	 * 
	 * This enables you to use isset() or empty() like so:
	 * 
	 * ```php
	 * $data = Arr::a(['foo' => 'bar']);
	 * isset($data->foo); // true
	 * isset($data->bar); // false
	 * ```
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $name - The name or key of the item to test on the underlying array.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * bool
	 */
	public function __isset($name)
	{
		return $this->offsetExists($name);
	}

	/**
	 * Method: __unset
	 * =========================================================================
	 * This is a PHP Magic Method.
	 * See: http://php.net/manual/en/language.oop5.magic.php
	 * 
	 * This enables you to use unset() like so:
	 * 
	 * ```php
	 * $data = Arr::a(['foo' => 'bar']);
	 * isset($data->foo); // true
	 * unset($data->foo);
	 * isset($data->foo); // false
	 * ```
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $name - The name or key of the item to test on the underlying array.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public function __unset($name)
	{
		$this->offsetUnset($name);
	}

	/**
	 * Method: __call
	 * =========================================================================
	 * The existing methods in the ```Illuminate\Support\Collection``` class
	 * are awesome. But there are some extra methods we would like to have
	 * access to.
	 * 
	 * This will first look for a function under our \Gears\Arrays
	 * namespace. If one is found we call that function for you, as if it
	 * were a method of this class.
	 * 
	 * If we can't find a function there we then check for any macros that have
	 * been defined on the \Gears\Arrays class. Again running that for you
	 * automatically.
	 * 
	 * On failure of all that we finally throw an InvalidMethod Exception.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $name - The name of the \Gears\Arrays\"FUNCTION" to call.
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
				// Just a reminder that macros can't have values
				// passed by reference due to __callStatic.
				$func_name = '\Gears\Arrays::'.$name;
			}
			else
			{
				// Bail out, we don't have a function to run
				throw new InvalidMethod($name);
			}
		}

		// Prepend the current items to the arguments
		// Some functions require a reference to the array so we define it here.
		$new_args = [&$this->items];

		foreach ($arguments as $arg)
		{
			// We also check for any arguments that might need
			// to be transformed into an actual array. As most of
			// the procedural functions expect real arrays and not
			// ArrayLike objects.
			if (isArrayLike($arg, true))
			{
				$new_args[] = $arg->toArray();
			}
			else
			{
				$new_args[] = $arg;
			}
		}

		// Save the new arguments
		$arguments = $new_args;

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
