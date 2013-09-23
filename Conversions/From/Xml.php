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

class BTB_Array_Driver_From_Xml extends BTB_Array_Driver
{
	/*
	 * PROPERTY: drop_attributes
	 * -------------------------------------------------------------------------
	 * Do we preserve the XML Attributes or do we simply ignore them.
	 */
	private $drop_attributes = false;
	public function set_drop_attributes($value) { $this->drop_attributes = $value; return $this;}
	
	/*
	 * METHOD: __construct
	 * -------------------------------------------------------------------------
	 * This will set any properties defined in the constructor
	 * 
	 * Parameters:
	 * 	$drop_attributes - As Above
	 * 
	 * Returns:
	 * 	void
	 */
	public function __construct($drop_attributes = null)
	{
		// Set our properties, note we dont have to set the properties here.
		// We can you the "set_" methods if desired.
		if ($drop_attributes !== null) $this->drop_attributes = $drop_attributes;
	}
	
	/*
	 * METHOD: Convert
	 * -------------------------------------------------------------------------
	 * This will do the actual converting
	 * 
	 * Parameters:
	 * 	$data - The XML to convert to an array
	 * 
	 * Returns:
	 * 	array
	 */
	public function Convert($data)
	{
		// Convert XML To JSON
		$json = Zend_Json::fromXml($data, $this->drop_attributes);
		
		// Convert the Json to PHP Array
		return Zend_Json::decode($json);
	}
}