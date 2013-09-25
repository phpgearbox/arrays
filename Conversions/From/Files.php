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

class Files extends \Gears\Arrays\Conversions\Template
{
	/**
	 * Property: except
	 * =========================================================================
	 * This is an array of folders or files that wont be indexed in the array.
	 */
	protected $except = ['.', '..'];
	
	/**
	 * Method: Convert
	 * =========================================================================
	 * This will do the actual converting
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * $filepath - A file path to where you would like to start converting.
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * array
	 */
	public function Convert($filepath)
	{
		if(!is_dir($filepath)) return false;
		$files = array(); $dirs = array($filepath);
		while(NULL !== ($dir = array_pop($dirs))) {
			if($dh = opendir($dir)) {
				while(false !== ($file = readdir($dh))) {
					$break = false;
					foreach ($this->except as $exception) {
						if ($file == $exception) $break = true;
					}
					if ($break) continue;
					$path = $dir . '/' . $file;
					if(is_dir($path)) $dirs[] = $path;
					else $files[] = $path;
				}
				closedir($dh);
			}
		}
		return $files;
	}
}
