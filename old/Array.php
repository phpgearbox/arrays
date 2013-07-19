<?php
////////////////////////////////////////////////////////////////////////////////
//             __         ___________            __ __________              
//     ______ |  |__ _____\__    ___/___   ____ |  |\______   \ _______  ___
//     \____ \|  |  \\____ \|    | /  _ \ /  _ \|  | |    |  _//  _ \  \/  /
//     |  |_> >   Y  \  |_> >    |(  <_> |  <_> )  |_|    |   (  <_> >    < 
//     |   __/|___|  /   __/|____| \____/ \____/|____/______  /\____/__/\_ \
//     |__|        \/|__|                                   \/            \/
// =============================================================================
//       Designed and Developed by Brad Jones <bradj @="hugonet.com.au" />      
// =============================================================================
// 
// >>> $Id: Array.php 492 2009-12-09 06:34:16Z bradj $
// 
////////////////////////////////////////////////////////////////////////////////

class BTB_Array
{
	/*
	 * PROPERTY: $driver
	 * -------------------------------------------------------------------------
	 * This will hold and instance of the driver we will be using.
	 */
	private $driver;
	public function get_driver() { return $this->driver; }
	
	/*
	 * PROPERTY: $driver_name
	 * -------------------------------------------------------------------------
	 * This will hold the name of the driver we will be using.
	 */
	private $driver_name;
	
	/*
	 * PROPERTY: $data
	 * -------------------------------------------------------------------------
	 * This will hold the actual data that is being converted.
	 * The results from the conversion will then replace the orginal data.
	 */
	private $data;
	public function set_data($value) { $this->data = $value; return $this; }
	public function get_data() { return $this->data; }
	
	/*
	 * METHOD: init
	 * -------------------------------------------------------------------------
	 * This is our factory method which allows us to create an instance of this
	 * class, thus allowing you to do some cool method chaining.
	 * 
	 * Parameters:
	 * 	$data - The data to be converted
	 * 
	 * Returns:
	 * 	BTB_Array
	 */
	public static function init($data)
	{
		return new BTB_Array($data);
	}
	
	/*
	 * METHOD: __construct
	 * -------------------------------------------------------------------------
	 * This is the actual constructor.
	 * All it does is sets the data property
	 * 
	 * Parameters:
	 * 	$data - The data to be converted
	 * 
	 * Returns:
	 * 	void
	 */
	public function __construct($data)
	{
		$this->data = $data;
	}
	
	/*
	 * METHOD: To
	 * -------------------------------------------------------------------------
	 * This will set the driver instance
	 * 
	 * Parameters:
	 * 	$driver - What driver or converter will we be using
	 * 
	 * Returns:
	 * 	BTB_Array
	 */
	public function To($driver)
	{
		$this->driver_name = 'BTB_Array_Driver_To_'.$driver;
		$class = new ReflectionClass($this->driver_name);
		$this->driver = $class->newInstance();
		return $this;
	}
	
	/*
	 * METHOD: From
	 * -------------------------------------------------------------------------
	 * This will set the driver instance
	 * 
	 * Parameters:
	 * 	$driver - What driver or converter will we be using
	 * 
	 * Returns:
	 * 	BTB_Array
	 */
	public function From($driver)
	{
		$this->driver_name = 'BTB_Array_Driver_From_'.$driver;
		$class = new ReflectionClass($this->driver_name);
		$this->driver = $class->newInstance();
		return $this;
	}
	
	/*
	 * METHOD: SetConfig
	 * -------------------------------------------------------------------------
	 * Each of the drivers will have various config options.
	 * If you were using the driver class directly you could set these options
	 * using the "set_" methods. This method does that for you.
	 * 
	 * Parameters:
	 * 	$options - An array of config options
	 * 
	 * Returns:
	 * 	BTB_Array
	 */
	public function SetConfig($options)
	{
		// Check to make sure that either the To or From methods have been run
		if (!empty($this->driver))
		{
			// Loop through the array
			foreach ($options as $option_name => $option_value)
			{
				$method = new ReflectionMethod($this->driver_name, 'set_'.$option_name);
				$method->invoke($this->driver, $option_value);
			}
			
			// So we co do method chaining
			return $this;
		}
		else
		{
			throw new BTB_Array_Exception
			(
				'NO DRIVER IS SET, '
				.'Please run either To() or From() before this method.'
			);
		}
	}
	
	/*
	 * METHOD: Convert
	 * -------------------------------------------------------------------------
	 * This will do the actual converting, using the specfied driver.
	 * 
	 * Parameters:
	 * 	
	 * 
	 * Returns:
	 * 	BTB_Array
	 */
	public function Convert()
	{
		$this->data = $this->driver->Convert($this->data); return $this;
	}
	
	/*
	 * METHOD: Length
	 * -------------------------------------------------------------------------
	 * This will return the length of the data currently stored.
	 * 
	 * Parameters:
	 * 	
	 * 
	 * Returns:
	 * 	BTB_Array
	 */
	public function Length()
	{
		if (is_array($this->data)) return count($this->data);
		else return strlen($this->data);
	}
	
	/*
	 * METHOD: Search
	 * -------------------------------------------------------------------------
	 * This will search an array recursively for your search terms.
	 * 
	 * Parameters:
	 * 	$search - The search query
	 * 	$exact - Defaults to true, If false search tersm can simply appear inside the array values.
	 * 	$trav_keys - 
	 * 	$arr - Used when called recursively
	 * 
	 * Returns:
	 * 	array
	 */
	public function Search($search, $exact = true, $trav_keys = null, $arr = null)
	{
		// Replace the default array value with the one from the class
		if ($arr == null) $arr = $this->data;
		
		// Check to make sure we have something to search for and search in.
		if(!is_array($arr) || !$search || ($trav_keys && !is_array($trav_keys)))
			return false;
		
		// Create a Results array
		$res_arr = array();
		
		// Loop through each value in the array
		foreach($arr as $key => $val)
		{
			$used_keys = $trav_keys ? array_merge($trav_keys,array($key)) : array($key);
			if (($key === $search) || (!$exact && (strpos(strtolower($key),strtolower($search))!==false)))
			{
				$res_arr[] = array(
					'type' => "key",
					'hit' => $key,
					'keys' => $used_keys,
					'val' => $val
				);
			}
			
			// Check to see if the value is another nested array
			if (is_array($val))
			{
				$children_res = $this->search($search, $exact, $used_keys, $val);
				if ($children_res) $res_arr = array_merge($res_arr, $children_res);
			}
			elseif (($val === $search) || (!$exact && (strpos(strtolower($val),strtolower($search))!==false)))
			{
				$res_arr[] = array(
					'type' => "val",
					'hit' => $val,
					'keys' => $used_keys,
					'val' => $val
				);
			}
		}
		
		// Return our results
		return $res_arr ? $res_arr : false;
	}
	
	/*
	 * METHOD: Debug
	 * -------------------------------------------------------------------------
	 * This will output an array for debug purposes.
	 * 
	 * Parameters:
	 * 	$html - Defaults to true, If true the output will be surrounded by <pre> tags
	 * 
	 * Returns:
	 * 	void
	 */
	public function Debug($html = true)
	{
		if ($html == true)
		{
			echo '<div align="left"><pre>'.print_r($this->data, true).'</pre></div>';
		}
		else
		{
			echo "ARRAY DUMP:\n-----\n".var_dump($this->data, true)."\n\n";
		}
	}
}