<?php namespace Gears\Arrays\Conversions\To;
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

use \Gears\Arrays\Conversions\Template;

class Csv extends Template
{
	/**
	 * Property: delimiter
	 * =========================================================================
	 * This is the delimiter or seperator that is used in the CSV.
	 * For example you could actually turn this into a TSV Driver by using "\t"
	 * 
	 * Defaults to ,
	 */
	protected $delimiter = ',';
	
	/**
	 * Property: enclosure
	 * =========================================================================
	 * This is the character that is used to enclose or surround the values.
	 * Eg:
	 * 	row1, abc, 123			(WITHOUT ENCLOSURE)
	 * 	"row2", "abc", "123"	(WITH ENCLOSURE)
	 * 
	 * Defaults to " 
	 */
	protected $enclosure = '"';
	
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
	 * Property: headings
	 * =========================================================================
	 * Do we treat the first row in the CSV as headings for our array.
	 * Or do we just output a number indexed array.
	 * 
	 * Defaults to true
	 */
	protected $headings = true;
	
	/*
	 * Method: Convert
	 * =========================================================================
	 * This will do the actual converting
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $data - An array to convert to CSV
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * csv
	 */
	public function Convert($data)
	{
		// Create a file pointer
		$fp = fopen('php://temp/maxmemory:'.$this->memory*(1024*1024), 'r+');
		
		// Loop through each line
		foreach ($data as $line)
		{
			fputcsv($fp, $line, $this->delimiter, $this->enclosure);
		}
		
		// Get the CSV as a String
		rewind($fp); $csv = stream_get_contents($fp); fclose($fp);
		
		// Add the headings
		if($this->headings == true)
		{
			// Create the headings line
			$headings = '';
			foreach ($data[0] as $key => $value)
			{
				$headings .= $key.',';
			}
			
			// Remove the last comma
			$headings = substr($headings, 0, -1);
			
			// Append the headings to the csv
			$csv = $headings."\n".$csv;
		}
		
		// Return the CSV
		return $csv;
	}
}
