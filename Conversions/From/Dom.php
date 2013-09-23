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

class BTB_Array_Driver_From_Dom extends BTB_Array_Driver
{
	/*
	 * METHOD: Convert
	 * -------------------------------------------------------------------------
	 * This will do the actual converting
	 * 
	 * Parameters:
	 * 	$dom - A DOM Object to convert to an array
	 * 
	 * Returns:
	 * 	array
	 */
	public function Convert($dom)
	{
		return $this->FromDom($dom);
	}
	
	/*
	 * METHOD: FromDom
	 * -------------------------------------------------------------------------
	 * This is a recursive function that will iterate through the DOM Nodes
	 * to create the array for easier inspection and manipulation.
	 * 
	 * Parameters:
	 * 	$domNode - A DOM Node to convert to an array
	 * 
	 * Returns:
	 * 	array
	 */
	private function FromDom($domNode)
	{
		$domArray = array();
		if($domNode->nodeType == XML_TEXT_NODE)
		{
			$domArray = trim($domNode->nodeValue);
		}
		else
		{
			if($domNode->hasAttributes())
			{
				$attributes = $domNode->attributes;
				if(!is_null($attributes))
				{
					$domArray['@attr'] = array();
					foreach ($attributes as $index=>$attr)
					{
						$domArray['@attr'][$attr->name] = $attr->value;
					}
				}
			}
			if($domNode->hasChildNodes())
			{
				$children = $domNode->childNodes;
				for($i=0;$i<$children->length;$i++)
				{
					$child = $children->item($i);
					$domArray[$child->nodeName.'_'.$i] = $this->FromDom($child);
				}
			}
		}
		return $domArray;
	}
}
