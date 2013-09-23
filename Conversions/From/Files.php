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
// >>> $Id: Files.php 492 2009-12-09 06:34:16Z bradj $
// 
////////////////////////////////////////////////////////////////////////////////

class BTB_Array_Driver_From_Files extends BTB_Array_Driver
{
	/*
	 * PROPERTY: except
	 * -------------------------------------------------------------------------
	 * This is an array of folders or files that wont be indexed in the array.
	 */
	private $except = array('.', '..', '.svn');
	public function set_except($value) { $this->except = $value; return $this; }
	
	/*
	 * METHOD: __construct
	 * -------------------------------------------------------------------------
	 * This will set any properties defined in the constructor
	 * 
	 * Parameters:
	 * 	$except - As Above
	 * 
	 * Returns:
	 * 	void
	 */
	public function __construct($except = null)
	{
		// Set our properties, note we dont have to set the properties here.
		// We can you the "set_" methods if desired.
		if ($except !== null) $this->except = $except;
	}
	
	/*
	 * METHOD: Convert
	 * -------------------------------------------------------------------------
	 * This will do the actual converting
	 * 
	 * Parameters:
	 * 	$filepath - A file path to where you would like to start converting.
	 * 
	 * Returns:
	 * 	array
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