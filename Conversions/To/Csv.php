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
// >>> $Id: Csv.php 492 2009-12-09 06:34:16Z bradj $
// 
////////////////////////////////////////////////////////////////////////////////

class BTB_Array_Driver_To_Csv extends BTB_Array_Driver
{
	/*
	 * PROPERTY: delimiter
	 * -------------------------------------------------------------------------
	 * This is the delimiter or seperator that is used in the CSV.
	 * For example you could actually turn this into a TSV Driver by using "\t"
	 * 
	 * Defaults to ,
	 */
	private $delimiter = ',';
	public function set_delimiter($value) { $this->delimiter = $value; return $this; }
	
	/*
	 * PROPERTY: enclosure
	 * -------------------------------------------------------------------------
	 * This is the character that is used to enclose or surround the values.
	 * Eg:
	 * 	row1, abc, 123			(WITHOUT ENCLOSURE)
	 * 	"row2", "abc", "123"	(WITH ENCLOSURE)
	 * 
	 * Defaults to " 
	 */
	private $enclosure = '"';
	public function set_enclosure($value) { $this->enclosure = $value; return $this; }
	
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
	 * PROPERTY: headings
	 * -------------------------------------------------------------------------
	 * Do we treat the first row in the CSV as headings for our array.
	 * Or do we just output a number indexed array.
	 * 
	 * Defaults to true
	 */
	private $headings = true;
	public function set_headings($value) { $this->headings = $value; return $this; }
	
	/*
	 * METHOD: __construct
	 * -------------------------------------------------------------------------
	 * This will set any properties defined in the constructor
	 * 
	 * Parameters:
	 * 	$delimiter - As Above
	 * 	$enclosure - As Above
	 * 	$max_memory - As Above
	 * 	$headings - As Above
	 * 
	 * Returns:
	 * 	void
	 */
	public function __construct($delimiter = null, $enclosure = null, $max_memory = null, $headings = null)
	{
		// Set our properties, note we dont have to set the properties here.
		// We can you the "set_" methods if desired.
		if ($delimiter !== null) $this->delimiter = $delimiter;
		if ($enclosure !== null) $this->enclosure = $enclosure;
		if ($max_memory !== null) $this->max_memory = $max_memory * (1024*1024);
		if ($headings !== null) $this->headings = $headings;
	}
	
	/*
	 * METHOD: Convert
	 * -------------------------------------------------------------------------
	 * This will do the actual converting
	 * 
	 * Parameters:
	 * 	$data - An array to convert to CSV
	 * 
	 * Returns:
	 * 	csv
	 */
	public function Convert($data)
	{
		// Create a file pointer
		$fp = fopen('php://temp/maxmemory:'.$this->max_memory, 'r+');
		
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