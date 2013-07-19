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

class BTB_Array_Driver_From_Csv extends BTB_Array_Driver
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
	 * PROPERTY: line_length
	 * -------------------------------------------------------------------------
	 * Must be greater than the longest line (in characters) to be found in the
	 * CSV (allowing for trailing line-end characters). It became optional in
	 * PHP 5 and setting it to 0 will let PHP work it out for you.
	 * However it is slightly slower.
	 * 
	 * Defaults to 0
	 */
	private $line_length = 0;
	public function set_line_length($value) { $this->line_length = $value; return $this; }
	
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
	 * 	$line_length - As Above
	 * 	$headings - As Above
	 * 
	 * Returns:
	 * 	void
	 */
	public function __construct($delimiter = null, $enclosure = null, $max_memory = null, $line_length = null, $headings = null)
	{
		// Set our properties, note we dont have to set the properties here.
		// We can you the "set_" methods if desired.
		if ($delimiter !== null) $this->delimiter = $delimiter;
		if ($enclosure !== null) $this->enclosure = $enclosure;
		if ($max_memory !== null) $this->max_memory = $max_memory * (1024*1024);
		if ($line_length !== null) $this->line_length = $line_length;
		if ($headings !== null) $this->headings = $headings;
	}
	
	/*
	 * METHOD: Convert
	 * -------------------------------------------------------------------------
	 * This will do the actual converting
	 * 
	 * Parameters:
	 * 	$csv - The CSV to convert to an array
	 * 
	 * Returns:
	 * 	array
	 */
	public function Convert($csv)
	{
		// Create a file pointer to our CSV Data
		$fp = fopen('php://temp/maxmemory:'.$this->max_memory, 'r+');
		fputs($fp, $csv);
		rewind($fp);
		
		// Loop through the CSV Lines
		$row = 1; $rows = array();
		while (($data = fgetcsv($fp, $this->line_length, $this->delimiter, $this->enclosure)) !== false)
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