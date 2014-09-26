<?php namespace Gears\Arrays;
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

use \ArrayIterator;
use \Gears\Arrays\Fluent;

class Iterator extends ArrayIterator
{
	/**
	 * Method: current
	 * =========================================================================
	 * Return current array entry
	 * See: http://php.net/manual/en/arrayiterator.current.php
	 * 
	 * We overload this method so that we can provide a recursive
	 * interface for the \Gears\Arrays\Fluent class.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * n/a
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * mixed
	 */
	public function current()
	{
		$key = parent::key();

		$value = parent::offsetGet($key);

		if (is_array($value)) $this->offsetSet($key, new Fluent($value));

		return parent::offsetGet($key);
	}
}