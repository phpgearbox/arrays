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

class BTB_Array_Driver_To_Table extends BTB_Array_Driver
{
	/*
	 * PROPERTY: headings
	 * -------------------------------------------------------------------------
	 * Do we take into account column headings when creating the table.
	 */
	private $headings = true;
	public function set_headings($value) { $this->headings = $value; return $this; }
	
	/*
	 * PROPERTY: indent
	 * -------------------------------------------------------------------------
	 * Do we want to have the HTML formated so it is readable.
	 */
	private $indent = false;
	public function set_indent($value) { $this->indent = $value; return $this; }
	
	/*
	 * METHOD: __construct
	 * -------------------------------------------------------------------------
	 * This will set any properties defined in the constructor
	 * 
	 * Parameters:
	 * 	$headings - As Above
	 * 	$indent - As Above
	 * 
	 * Returns:
	 * 	void
	 */
	public function __construct($headings = null, $indent = null)
	{
		// Set our properties, note we dont have to set the properties here.
		// We can you the "set_" methods if desired.
		if ($headings !== null) $this->headings = $headings;
		if ($indent !== null) $this->indent = $indent;
	}
	
	/*
	 * METHOD: Convert
	 * -------------------------------------------------------------------------
	 * This will do the actual converting
	 * 
	 * Parameters:
	 * 	$data - An array to convert to a HTML Table
	 * 
	 * Returns:
	 * 	html
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
	
	/*
	 * METHOD: AddIndents
	 * -------------------------------------------------------------------------
	 * This will use HTML Tidy to pretify the HTML.
	 * 
	 * Parameters:
	 * 	$html - The html to be formatted
	 * 
	 * Returns:
	 * 	html
	 */
	private function AddIndents($html)
	{
		$tidy = new tidy();
		$tidy->parseString
		(
			$html,
			array(
				'indent'		=> true,
				'indent-spaces'	=> 4,
				'output-html'	=> true,
				'input-xml'		=> true
			),
			'utf8'
		);
		$tidy->cleanRepair();
		return tidy_get_output($tidy);
	}
}