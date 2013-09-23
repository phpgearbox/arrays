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
// >>> $Id: Lines.php 492 2009-12-09 06:34:16Z bradj $
// 
////////////////////////////////////////////////////////////////////////////////

class BTB_Array_Driver_From_Lines extends BTB_Array_Driver
{
	/*
	 * PROPERTY: max_memory
	 * -------------------------------------------------------------------------
	 * This is the amount of memory that we will take up to create a
	 * file pointer to a temporay place in main memory.
	 * 
	 * Defaults to 5MB
	 */
	private $max_memory = 5242880;
	public function set_max_memory($value) { $this->max_memory = $value * (1024*1024); return $this; }
	
	/*
	 * PROPERTY: trim
	 * -------------------------------------------------------------------------
	 * Remove leading and trailing whitespace
	 * 
	 * Defaults to true
	 */
	private $trim = true;
	public function set_trim($value) { $this->trim = $value; return $this; }
	
	/*
	 * METHOD: __construct
	 * -------------------------------------------------------------------------
	 * This will set any properties defined in the constructor
	 * 
	 * Parameters:
	 * 	$max_memory - As Above
	 * 
	 * Returns:
	 * 	void
	 */
	public function __construct($max_memory = null)
	{
		// Set our properties, note we dont have to set the properties here.
		// We can you the "set_" methods if desired.
		if ($max_memory !== null) $this->max_memory = $max_memory * (1024*1024);
	}
	
	/*
	 * METHOD: Convert
	 * -------------------------------------------------------------------------
	 * This will do the actual converting
	 * 
	 * Parameters:
	 * 	$data - A string of lines to convert to an array
	 * 
	 * Returns:
	 * 	array
	 */
	public function Convert($data)
	{
		// Create an array
		$lines = array();
		
		// Create a file pointer to our Text Data
		$fp = fopen('php://temp/maxmemory:'.$this->max_memory, 'r+');
		fputs($fp, $data);
		rewind($fp);
		
		// Loop through each line and add it to our array
		while (!feof($fp))
		{
			if ($this->trim) $lines[] = trim(fgets($fp));
			else $lines[] = fgets($fp);
		}
		fclose($fp);
		
		// Return the array
		return $lines;
	}
}