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

use \tidy;
use \Gears\Arrays\Conversions\Template;

class Table extends Template
{
	/**
	 * Property: headings
	 * =========================================================================
	 * Do we take into account column headings when creating the table.
	 */
	protected $headings = true;
	
	/**
	 * Property: indent
	 * =========================================================================
	 * Do we want to have the HTML formated so it is readable.
	 */
	protected $indent = false;
	
	/**
	 * Method: Convert
	 * =========================================================================
	 * This will do the actual converting
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $data - An array to convert to a HTML Table
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * html
	 */
	public function Convert($data)
	{
		// Open the table
		$html_table = '<table>';
		
		// Loop through each row
		foreach ($data as $row)
		{
			// Open the row
			$html_table .= '<tr>';
			
			// Are we creating the first row of headings
			if ($this->headings)
			{
				foreach ($row as $heading => $cell)
				{
					// Open Cell
					$html_table .= '<td>';
					
					// Add cell contents
					$html_table .= $heading;
					
					// Close Cell
					$html_table .= '</td>';
				}
				
				$html_table .= '</tr><tr>';
				$this->headings = false;
			}
			
			// Create the body of the table
			foreach ($row as $cell)
			{
				// Open Cell
				$html_table .= '<td>';
				
				// Add cell contents
				$html_table .= $cell;
				
				// Close Cell
				$html_table .= '</td>';
			}
			
			// Close the row
			$html_table .= '</tr>';
		}
		
		// Close the table
		$html_table .= '</table>';
		
		// Make the table human readable
		if (!empty($this->indent)) $html_table = $this->AddIndents($html_table);
		
		return $html_table;
	}
	
	/**
	 * Method: AddIndents
	 * =========================================================================
	 * This will use HTML Tidy to pretify the HTML.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $html - The html to be formatted
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * html
	 */
	private function AddIndents($html)
	{
		$tidy = new tidy();
		$tidy->parseString
		(
			$html,
			[
				'indent'		=> true,
				'indent-spaces'	=> 4,
				'output-html'	=> true,
				'input-xml'		=> true
			],
			'utf8'
		);
		$tidy->cleanRepair();
		return tidy_get_output($tidy);
	}
}
