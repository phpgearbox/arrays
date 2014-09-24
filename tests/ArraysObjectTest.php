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

use Gears\Arrays as Arr;

class ArraysObjectTest extends PHPUnit_Framework_TestCase
{
	public function testSomething()
	{
		$exampleArray =
		[
			'a' => [1, '2', '3'],
			'b' => [4, '5', '6'],
			'c' => [7, '8', '9'],
			'd' => 'dhfhg'
		];

		$test1 = Arr::a($exampleArray);
		$test2 = $test1->pull('a');
		$test3 = $test1->pull('b');

		//$test1->set('c.1.adhgjh', 'sdghfdhfdh');
		//print_r($test1);
		//exit;

		$test4 = Arr::pull($exampleArray, 'a');
		$test5 = Gears\Arrays\pull($exampleArray, 'b');
		Arr::set($exampleArray, 'address.street3', 'dfhh');

		$test1->address = [];
		$test1->address->street = 'fhjfg';
		$test1->address->street2 = 'fhjfg346';
		$test1->set('address.street3', 'fggjg4574578');
		$test1['address']['street4'] = ['gfhjfgj', 'fjgdfjg'];
		$test1['address.street5.dshdh'] = ['gfhjfgj', 'fjgdfjg'];
		$test1->address->street4;

		print_r($test1);
		print_r($test2);
		print_r($test3);

		print_r($exampleArray);
		print_r($test4);
		print_r($test5);

		//$test->set('1.2.3', 'hello');
		//$test->set('1.2.3', 'hello world');
		//$test = Arr::set($exampleArray, '1.2.3', 'hello world');

		
		exit;
	}
}