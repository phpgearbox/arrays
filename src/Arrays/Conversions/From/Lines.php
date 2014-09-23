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

namespace Gears\Arrays\Conversions\From;

class Lines extends \Gears\Arrays\Conversions\Template
{
	/**
	 * Property: memory
	 * =========================================================================
	 * This is the amount of memory that we will take up to create a
	 * file pointer to a temporay place in main memory.
	 * 
	 * Defaults to 5MB
	 */
	protected $memory = 5;
	
	/**
	 * Property: trim
	 * =========================================================================
	 * Remove leading and trailing whitespace
	 * 
	 * Defaults to true
	 */
	protected $trim = true;
	
	/**
	 * Method: Convert
	 * =========================================================================
	 * This will do the actual converting
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $data - A string of lines to convert to an array
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * array
	 */
	public function Convert($data)
	{
		// Create an array
		$lines = array();
		
		// Create a file pointer to our Text Data
		$fp = fopen('php://temp/maxmemory:'.$this->memory*(1024*1024), 'r+');
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
