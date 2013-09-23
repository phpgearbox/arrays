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
// >>> $Id: Table.php 492 2009-12-09 06:34:16Z bradj $
// 
////////////////////////////////////////////////////////////////////////////////

class BTB_Array_Driver_From_Table extends BTB_Array_Driver
{
	/*
	 * PROPERTY: table_location
	 * -------------------------------------------------------------------------
	 * This is either a CSS or Xpath Query to give the location of the
	 * HTML Table you would like to convert toi an array.
	 */
	private $table_location;
	public function set_table_location($value) { $this->table_location = $value; return $this; }
	
	/*
	 * PROPERTY: table_headings
	 * -------------------------------------------------------------------------
	 * Do you want to treat the first row of the table as the headings
	 * for each column.
	 */
	private $table_headings = true;
	public function set_table_headings($value) { $this->table_headings = $value; return $this; }
	
	/*
	 * PROPERTY: query_type
	 * -------------------------------------------------------------------------
	 * This will take either css or xpath
	 */
	private $query_type = 'css';
	public function set_query_type($value) { $this->query_type = $value; return $this; }
	
	/*
	 * METHOD: __construct
	 * -------------------------------------------------------------------------
	 * This will set any properties defined in the constructor
	 * 
	 * Parameters:
	 * 	$table_location - As Above
	 * 	$table_headings - As Above
	 * 	$query_type - As Above
	 * 
	 * Returns:
	 * 	void
	 */
	public function __construct($table_location = null, $table_headings = null, $query_type = null)
	{
		// Set our properties, note we dont have to set the properties here.
		// We can you the "set_" methods if desired.
		if ($table_location !== null) $this->table_location = $table_location;
		if ($table_headings !== null) $this->table_headings = $table_headings;
		if ($query_type !== null) $this->query_type = $query_type;
	}
	
	/*
	 * METHOD: Convert
	 * -------------------------------------------------------------------------
	 * This will do the actual converting
	 * 
	 * Parameters:
	 * 	$data - The HTML string that contains the table to convert to an array.
	 * 
	 * Returns:
	 * 	array
	 */
	public function Convert($data)
	{
		// First of all lets do some simple string manipulation
		$data = str_replace(array('<thead>', '</thead>', '<tbody>', '</tbody>', '<tfoot>', '</tfoot>'), '', $data);
		$data = str_replace(array('<th', '</th'), array('<tr', '</tr'), $data);
		
		// Create a new dom object
		$dom = new Zend_Dom_Query($data);
		
		// Find the table within the document
		switch ($this->query_type)
		{
			case 'css': $table = $dom->query($this->table_location); break;
			case 'xpath': $table = $dom->queryXpath($this->table_location); break;
		}
		
		// Convert the table node into an array
		$array = new BTB_Array_Driver_From_Dom();
		$dom_array = $array->Convert($table->current());
		
		// Then tidy up that array
		$table_array = array();
		
		// We wish to use the first row of the table as the keys for the array
		if ($this->table_headings)
		{
			// Create a simple counter
			$x = -1;
			
			// Loop through each row
			foreach ($dom_array as $tr_num => $tr)
			{
				// Ignoring anything else
				if (strpos($tr_num, 'tr_') !== false)
				{
					// Ignore the first row as that only contains the headings
					if ($x == -1) { $x++; continue; }
					
					// Loop through each of the TD Elements
					foreach ($tr as $td_num => $td)
					{
						// Ignoring anything else
						if (strpos($td_num, 'td_') !== false)
						{
							// Work out what the key name is
							$key = $dom_array['tr_0'][$td_num]['#text_0'];
							
							// Add the cell to the array
							$table_array[$x][$key] = $td['#text_0'];
						}
					}
					
					// Increase the row count
					$x++;
				}
			}
		}
		else
		{
			// Create a simple counter
			$x = 0;
			
			// Loop through each row
			foreach ($dom_array as $tr_num => $tr)
			{
				// Ignoring anything else
				if (strpos($tr_num, 'tr_') !== false)
				{
					// Loop through each of the TD Elements
					foreach ($tr as $td_num => $td)
					{
						// Ignoring anything else
						if (strpos($td_num, 'td_') !== false)
						{
							// Add the cell to the array
							$table_array[$x][] = $td['#text_0'];
						}
					}
					
					// Increase the row count
					$x++;
				}
			}
		}
		
		return $table_array;
	}
}