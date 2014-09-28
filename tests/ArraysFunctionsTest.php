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

	public function testArrayBuild()
	{
		$this->assertEquals(array('foo' => 'bar'), Gears\Arrays\build(array('foo' => 'bar'), function($key, $value)
		{
			return array($key, $value);
		}));
	}


	public function testArrayDot()
	{
		$array = Gears\Arrays\dot(array('name' => 'taylor', 'languages' => array('php' => true)));
		$this->assertEquals($array, array('name' => 'taylor', 'languages.php' => true));
	}


	public function testArrayGet()
	{
		$array = array('names' => array('developer' => 'taylor'));
		$this->assertEquals('taylor', Gears\Arrays\get($array, 'names.developer'));
		$this->assertEquals('dayle', Gears\Arrays\get($array, 'names.otherDeveloper', 'dayle'));
		$this->assertEquals('dayle', Gears\Arrays\get($array, 'names.otherDeveloper', function() { return 'dayle'; }));
	}


	public function testArraySet()
	{
		$array = array();
		Gears\Arrays\set($array, 'names.developer', 'taylor');
		$this->assertEquals('taylor', $array['names']['developer']);
	}


	public function testArrayForget()
	{
		$array = array('names' => array('developer' => 'taylor', 'otherDeveloper' => 'dayle'));
		Gears\Arrays\forget($array, 'names.developer');
		$this->assertFalse(isset($array['names']['developer']));
		$this->assertTrue(isset($array['names']['otherDeveloper']));

		$array = ['names' => ['developer' => 'taylor', 'otherDeveloper' => 'dayle', 'thirdDeveloper' => 'Lucas']];
		Gears\Arrays\forget($array, ['names.developer', 'names.otherDeveloper']);
		$this->assertFalse(isset($array['names']['developer']));
		$this->assertFalse(isset($array['names']['otherDeveloper']));
		$this->assertTrue(isset($array['names']['thirdDeveloper']));

		$array = ['names' => ['developer' => 'taylor', 'otherDeveloper' => 'dayle'], 'otherNames' => ['developer' => 'Lucas', 'otherDeveloper' => 'Graham']];
		Gears\Arrays\forget($array, ['names.developer', 'otherNames.otherDeveloper']);
		$expected = ['names' => ['otherDeveloper' => 'dayle'], 'otherNames' => ['developer' => 'Lucas']];
		$this->assertEquals($expected, $array);
	}


	public function testArrayPluckWithArrayAndObjectValues()
	{
		$array = array((object) array('name' => 'taylor', 'email' => 'foo'), array('name' => 'dayle', 'email' => 'bar'));
		$this->assertEquals(array('taylor', 'dayle'), Gears\Arrays\pluck($array, 'name'));
		$this->assertEquals(array('taylor' => 'foo', 'dayle' => 'bar'), Gears\Arrays\pluck($array, 'email', 'name'));
	}


	public function testArrayExcept()
	{
		$array = array('name' => 'taylor', 'age' => 26);
		$this->assertEquals(array('age' => 26), Gears\Arrays\except($array, array('name')));
	}


	public function testArrayOnly()
	{
		$array = array('name' => 'taylor', 'age' => 26);
		$this->assertEquals(array('name' => 'taylor'), Gears\Arrays\only($array, array('name')));
		$this->assertSame(array(), Gears\Arrays\only($array, array('nonExistingKey')));
	}


	public function testArrayDivide()
	{
		$array = array('name' => 'taylor');
		list($keys, $values) = Gears\Arrays\divide($array);
		$this->assertEquals(array('name'), $keys);
		$this->assertEquals(array('taylor'), $values);
	}


	public function testArrayFirst()
	{
		$array = array('name' => 'taylor', 'otherDeveloper' => 'dayle');
		$this->assertEquals('dayle', Gears\Arrays\first($array, function($key, $value) { return $value == 'dayle'; }));
	}

	public function testArrayLast()
	{
		$array = array(100, 250, 290, 320, 500, 560, 670);
		$this->assertEquals(670, Gears\Arrays\last($array, function($key, $value) { return $value > 320; }));
	}


	public function testArrayFetch()
	{
		$data = array(
			'post-1' => array(
				'comments' => array(
					'tags' => array(
						'#foo', '#bar',
					),
				),
			),
			'post-2' => array(
				'comments' => array(
					'tags' => array(
						'#baz',
					),
				),
			),
		);

		$this->assertEquals(array(
			0 => array(
				'tags' => array(
					'#foo', '#bar',
				),
			),
			1 => array(
				'tags' => array(
					'#baz',
				),
			),
		), Gears\Arrays\fetch($data, 'comments'));

		$this->assertEquals(array(array('#foo', '#bar'), array('#baz')), Gears\Arrays\fetch($data, 'comments.tags'));
	}


	public function testArrayFlatten()
	{
		$this->assertEquals(array('#foo', '#bar', '#baz'), Gears\Arrays\flatten(array(array('#foo', '#bar'), array('#baz'))));
	}

	public function testArraySort()
	{
		$array = array(
			array('name' => 'baz'),
			array('name' => 'foo'),
			array('name' => 'bar'),
		);

		$this->assertEquals(array(
			array('name' => 'bar'),
			array('name' => 'baz'),
			array('name' => 'foo')),
		array_values(Gears\Arrays\sort($array, function($v) { return $v['name']; })));
	}

	public function testArrayAdd()
	{
		$this->assertEquals(array('surname' => 'Mövsümov'), Gears\Arrays\add(array(), 'surname', 'Mövsümov'));
		$this->assertEquals(array('developer' => array('name' => 'Ferid')), Gears\Arrays\add(array(), 'developer.name', 'Ferid'));
	}


	public function testArrayPull()
	{
		$developer = array('firstname' => 'Ferid', 'surname' => 'Mövsümov');
		$this->assertEquals('Mövsümov', Gears\Arrays\pull($developer, 'surname'));
		$this->assertEquals(array('firstname' => 'Ferid'), $developer);
	}
}