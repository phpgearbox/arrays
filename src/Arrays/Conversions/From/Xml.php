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

use \Zend\Json\Json;
use \Gears\Arrays\Conversions\Template;

class Xml extends Template
{
	/**
	 * Property: drop_attributes
	 * =========================================================================
	 * Do we preserve the XML Attributes or do we simply ignore them.
	 */
	protected $drop_attributes = false;
	
	/**
	 * METHOD: Convert
	 * =========================================================================
	 * This will do the actual converting
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $data - The XML to convert to an array
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * array
	 */
	public function Convert($data)
	{
		// Convert XML To JSON
		$json = Json::fromXml($data, $this->drop_attributes);
		
		// Convert the Json to PHP Array
		return json_decode($json, true);
	}
}
