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

namespace Gears\Arrays\Conversions\From;

class Csv implements \Gears\Arrays\Conversions\Template
{
	/**
	 * Property: delimiter
	 * -------------------------------------------------------------------------
	 * This is the delimiter or seperator that is used in the CSV.
	 * For example you could actually turn this into a TSV Driver by using "\t"
	 * 
	 * Defaults to ,
	 */
	private $delimiter = ',';
	
	/**
	 * Property: enclosure
	 * -------------------------------------------------------------------------
	 * This is the character that is used to enclose or surround the values.
	 * Eg:
	 * 	row1, abc, 123			(WITHOUT ENCLOSURE)
	 * 	"row2", "abc", "123"	(WITH ENCLOSURE)
	 * 
	 * Defaults to " 
	 */
	private $enclosure = '"';
	
	/**
	 * Property: memory
	 * -------------------------------------------------------------------------
	 * This is the amount of memory that we will take up to create a
	 * file pointer to a temporay place in main memory.
	 * 
	 * Defaults to 5MB
	 */
	private $memory = 5;
	
	/**
	 * Property: length
	 * -------------------------------------------------------------------------
	 * Must be greater than the longest line (in characters) to be found in the
	 * CSV (allowing for trailing line-end characters). It became optional in
	 * PHP 5 and setting it to 0 will let PHP work it out for you.
	 * However it is slightly slower.
	 * 
	 * Defaults to 0
	 */
	private $length = 0;
	
	/**
	 * Property: headings
	 * -------------------------------------------------------------------------
	 * Do we treat the first row in the CSV as headings for our array.
	 * Or do we just output a number indexed array.
	 * 
	 * Defaults to true
	 */
	private $headings = true;
	
	/**
	 * Method: __construct
	 * =========================================================================
	 * This will set any properties defined in the constructor
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $options - An array of values, with keys the same as the properties above
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public function __construct($options)
	{
		foreach ($options as $key => $value)
		{
			if (isset($this->{$key}))
			{
				$this->{$key} = $value;
			}
		}
	}
	
	/**
	 * Method: Convert
	 * =========================================================================
	 * This will do the actual converting
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $csv - The CSV to convert to an array
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * array
	 */
	public function Convert($csv)
	{
		// Create a file pointer to our CSV Data
		$fp = fopen('php://temp/maxmemory:'.$this->memory*(1024*1024), 'r+');
		fputs($fp, $csv);
		rewind($fp);
		
		// Loop through the CSV Lines
		$row = 1; $rows = array();
		while (($data = fgetcsv($fp, $this->length, $this->delimiter, $this->enclosure)) !== false)
		{
			// Create the heading array
			if ($this->headings == true && $row == 1) $headingTexts = $data;
			
			// Add the csv line into the array using the heading names
			elseif ($this->headings == true)
			{
				foreach ($data as $key => $value) 
				{
					unset($data[$key]);
					$data[$headingTexts[$key]] = $value;
				}
				$rows[] = $data;
			}
			
			// Otherwise dont worry about the headings at all
			else $rows[] = $data;
			
			// Increase the row count
			$row++;
		}
		
		// Finish Up
		fclose($fp);
		return $rows;
	}
}
