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
// >>> $Id: Dom.php 492 2009-12-09 06:34:16Z bradj $
// 
////////////////////////////////////////////////////////////////////////////////

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