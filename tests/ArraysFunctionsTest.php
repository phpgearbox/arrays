<?php
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

class ArraysFunctionsTest extends PHPUnit_Framework_TestCase
{
	public function testIsArrayLike()
	{
		$this->assertTrue(Gears\Arrays\isArrayLike(array()));
		$this->assertTrue(Gears\Arrays\isArrayLike(new Gears\Arrays\Fluent()));
		$this->assertTrue(Gears\Arrays\isArrayLike(new Gears\Arrays\Fluent(), true));
		$this->assertFalse(Gears\Arrays\isArrayLike(array(), true));
		$this->assertFalse(Gears\Arrays\isArrayLike('string'));
	}
}