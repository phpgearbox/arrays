<?php namespace Gears\Arrays\Conversions\From;
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

use \Zend\Dom\Query;
use \Gears\Arrays\Conversions\Template;
use \Gears\Arrays\Conversions\From\Dom;

class Table extends Template
{
	/**
	 * Property: table_location
	 * =========================================================================
	 * This is either a CSS or Xpath Query to give the location of the
	 * HTML Table you would like to convert toi an array.
	 */
	protected $table_location = 'table';
	
	/**
	 * Property: table_headings
	 * =========================================================================
	 * Do you want to treat the first row of the table as the headings
	 * for each column.
	 */
	protected $table_headings = true;
	
	/**
	 * Property: query_type
	 * =========================================================================
	 * This will take either css or xpath
	 */
	protected $query_type = 'css';
	
	/**
	 * Method: Convert
	 * =========================================================================
	 * This will do the actual converting
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $data - The HTML string that contains the table to convert to an array.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * array
	 */
	public function Convert($data)
	{
		// First of all lets do some simple string manipulation
		$data = str_replace(['<thead>', '</thead>', '<tbody>', '</tbody>', '<tfoot>', '</tfoot>'], '', $data);
		$data = str_replace(['<th', '</th'], ['<td', '</td'], $data);
		
		// Create a new dom object
		$dom = new Query($data);
		
		// Find the table within the document
		switch ($this->query_type)
		{
			case 'css': $table = $dom->execute($this->table_location); break;
			case 'xpath': $table = $dom->queryXpath($this->table_location); break;
		}
		
		// Convert the table node into an array
		$array = new Dom();
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
