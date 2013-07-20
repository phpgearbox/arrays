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

namespace Gears\Arrays;

/**
 * Function: Search
 * =============================================================================
 * This will search an array recursively for your search terms.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to search within.
 * $search - The search query.
 * $exact - If false search terms can simply appear inside the array values.
 * $trav_keys - Used recursively
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * An array of results or false if it can find anything
 */
function Search($array, $search, $exact = true, $trav_keys = null)
{
	// Check to make sure we have something to search for and search in.
	if(!is_array($array) || !$search || ($trav_keys && !is_array($trav_keys)))
	{
		return false;
	}
	
	// Create a Results array
	$results = [];
	
	// Loop through each value in the array
	foreach($array as $key => $val)
	{
		// Create a list of keys that we used to find the result
		$used_keys = $trav_keys ? array_merge($trav_keys, [$key]) : [$key];
		
		// Check for an exact hit on the key
		if ($key === $search)
		{
			$results[] =
			[
				'type' => "key",
				'hit' => $key,
				'keys' => $used_keys,
				'val' => $val
			];
		}
		
		// Can we match to non exact hits?
		elseif (!$exact)
		{
			// Check to see if the array key contains the search term
			if (strpos(strtolower($key), strtolower($search)) !== false)
			{
				$results[] =
				[
					'type' => "key",
					'hit' => $key,
					'keys' => $used_keys,
					'val' => $val
				];
			}
		}
		
		// Check to see if the value is another nested array
		if (is_array($val))
		{
			// Recursively call ourselves
			$children_results = Search($val, $search, $exact, $used_keys);
			if ($children_res)
			{
				$results = array_merge($results, $children_results);
			}
		}
		
		// Check for an exact hit on the value
		elseif ($val === $search)
		{
			$res_arr[] =
			[
				'type' => "val",
				'hit' => $val,
				'keys' => $used_keys,
				'val' => $val
			];
		}
		
		// Can we match to non exact hits?
		elseif (!$exact)
		{
			// Check to see if the array key contains the search term
			if (strpos(strtolower($val), strtolower($search)) !== false)
			{
				$results[] =
				[
					'type' => "val",
					'hit' => $val,
					'keys' => $used_keys,
					'val' => $val
				];
			}
		}
	}
	
	// Return our results
	return $results ? $results : false;
}

/**
 * Function: Debug
 * =============================================================================
 * This will output an array for debug purposes. If running from a web server
 * we will wrap it in some pre tags for you.
 * 
 * Parameters:
 * -----------------------------------------------------------------------------
 * $array - The array to output.
 * 
 * Returns:
 * -----------------------------------------------------------------------------
 * void
 */
function Debug($array)
{
	// are we running on the command line or in a browser
	if (php_sapi_name() == 'cli')
	{
		echo "\n\nARRAY DUMP:\n-----\n".print_r($array, true)."\n\n";
	}
	else
	{
		echo
			'<div style="text-align:left;">'.
				'<h1>ARRAY DUMP</h1><hr>'.
				'<pre>'.print_r($array, true).'</pre>'.
			'</div>'
		;
	}
}
