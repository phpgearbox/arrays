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

namespace Gears\Array;

/*
 * METHOD: Search
 * -------------------------------------------------------------------------
 * This will search an array recursively for your search terms.
 * 
 * Parameters:
 * 	$search - The search query
 * 	$exact - Defaults to true, If false search tersm can simply appear inside the array values.
 * 	$trav_keys - 
 * 	$arr - Used when called recursively
 * 
 * Returns:
 * 	array
 */
function Search($search, $exact = true, $trav_keys = null, $arr = null)
{
	// Replace the default array value with the one from the class
	if ($arr == null) $arr = $this->data;
	
	// Check to make sure we have something to search for and search in.
	if(!is_array($arr) || !$search || ($trav_keys && !is_array($trav_keys)))
		return false;
	
	// Create a Results array
	$res_arr = array();
	
	// Loop through each value in the array
	foreach($arr as $key => $val)
	{
		$used_keys = $trav_keys ? array_merge($trav_keys,array($key)) : array($key);
		if (($key === $search) || (!$exact && (strpos(strtolower($key),strtolower($search))!==false)))
		{
			$res_arr[] = array(
				'type' => "key",
				'hit' => $key,
				'keys' => $used_keys,
				'val' => $val
			);
		}
		
		// Check to see if the value is another nested array
		if (is_array($val))
		{
			$children_res = $this->search($search, $exact, $used_keys, $val);
			if ($children_res) $res_arr = array_merge($res_arr, $children_res);
		}
		elseif (($val === $search) || (!$exact && (strpos(strtolower($val),strtolower($search))!==false)))
		{
			$res_arr[] = array(
				'type' => "val",
				'hit' => $val,
				'keys' => $used_keys,
				'val' => $val
			);
		}
	}
	
	// Return our results
	return $res_arr ? $res_arr : false;
}

/*
 * METHOD: Debug
 * -------------------------------------------------------------------------
 * This will output an array for debug purposes.
 * 
 * Parameters:
 * 	$html - Defaults to true, If true the output will be surrounded by <pre> tags
 * 
 * Returns:
 * 	void
 */
function Debug($html = true)
{
	if ($html == true)
	{
		echo '<div align="left"><pre>'.print_r($this->data, true).'</pre></div>';
	}
	else
	{
		echo "ARRAY DUMP:\n-----\n".var_dump($this->data, true)."\n\n";
	}
}
