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
// >>> $Id: Xml.php 492 2009-12-09 06:34:16Z bradj $
// 
////////////////////////////////////////////////////////////////////////////////

class BTB_Array_Driver_To_Xml extends BTB_Array_Driver
{
	/*
	 * PROPERTY: indent
	 * -------------------------------------------------------------------------
	 * Do we want to have the XML formated so it is readable.
	 */
	private $indent = false;
	public function set_indent($value) { $this->indent = $value; return $this; }
	
	/*
	 * METHOD: __construct
	 * -------------------------------------------------------------------------
	 * This will set any properties defined in the constructor
	 * 
	 * Parameters:
	 * 	$indent - As Above
	 * 
	 * Returns:
	 * 	void
	 */
	public function __construct($indent = null)
	{
		// Set our properties, note we dont have to set the properties here.
		// We can you the "set_" methods if desired.
		if ($indent !== null) $this->indent = $indent;
	}
	
	/*
	 * METHOD: Convert
	 * -------------------------------------------------------------------------
	 * This will do the actual converting
	 * 
	 * Parameters:
	 * 	$data - An array to convert to XML
	 * 
	 * Returns:
	 * 	xml
	 */
	public function Convert($data)
	{
		// Create a new xml document
		$xml = new XmlWriter();
		$xml->openMemory();
		$xml->setIndent($this->indent);
		$xml->startDocument('1.0', 'UTF-8');
		
		// Recursively create the body of the document
		$this->RecursXML($xml, $data);
		
		// End the document
		$xml->endElement();
		
		// Return the XML Document as a String
		return $xml->outputMemory(true);
	}
	
	/*
	 * METHOD: RecursXML
	 * -------------------------------------------------------------------------
	 * This is the resursive function that will convert the each node of the
	 * array to xml.
	 * 
	 * Parameters:
	 * 	$xml - The XMLWriter Instance
	 * 	$data - The array to convert
	 * 
	 * Returns:
	 * 	xml
	 */
	private function RecursXML(XMLWriter $xml, $data)
	{
		// Loop through the array
		foreach($data as $key => $value)
		{
			// Take care of any attributes
			if (!empty($key) && $key == '@attributes')
			{
				// Loop through the attributes
				foreach ($value as $attribute_name => $attribute_value)
				{
					$xml->writeAttribute($attribute_name, $attribute_value);
				}
				continue;
			}
			
			// Do we need to go further into the array
			if(is_array($value))
			{
				// Check for indexed type
				if (isset($value[0]))
				{
					foreach ($value as $value_data)
					{
						if (is_array($value_data))
						{
							$xml->startElement($key);
							$this->RecursXML($xml, $value_data);
							$xml->endElement();
						}
						else
						{
							$xml->writeElement($key, $value_data);
						}
					}
				}
				else
				{
					$xml->startElement($key);
					$this->RecursXML($xml, $value);
					$xml->endElement();
				}
				continue;
			}
			
			// We are not allowed to have a numric key
			if (is_numeric($key)) $key = 'index_'.$key;
			
			$xml->writeElement($key, $value);
		}
	}
}