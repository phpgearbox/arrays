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
	public function testEach()
	{
		$this->assertEquals(array(2, 3, 4), Gears\Arrays\each(array(1, 2, 3), function($value){ return $value+1; }));
		$this->assertEquals(array(1, 3, 5), Gears\Arrays\each(array(1, 2, 3), function($value, $key){ return $value+$key; }));
	}

	public function testFirst()
	{
		$this->assertEquals('foo', Gears\Arrays\first(array('foo', 'bar')));
		$array = array('name' => 'taylor', 'otherDeveloper' => 'dayle');
		$this->assertEquals('dayle', Gears\Arrays\first($array, function($key, $value) { return $value == 'dayle'; }));
	}

	public function testLast()
	{
		$this->assertEquals('bar', Gears\Arrays\last(array('foo', 'bar')));
		$array = array(100, 250, 290, 320, 500, 560, 670);
		$this->assertEquals(670, Gears\Arrays\last($array, function($key, $value) { return $value > 320; }));
	}

	public function testLists()
	{
		$data = array((object) array('name' => 'taylor', 'email' => 'foo'), array('name' => 'dayle', 'email' => 'bar'));
		$this->assertEquals(array('taylor' => 'foo', 'dayle' => 'bar'), Gears\Arrays\lists($data, 'email', 'name'));
		$this->assertEquals(array('foo', 'bar'), Gears\Arrays\lists($data, 'email'));
	}

	public function testKeys()
	{
		$this->assertEquals(array(0, 1, 2), Gears\Arrays\keys(array('a', 'b', 'c')));
	}

	public function testIsEmpty()
	{
		$this->assertTrue(Gears\Arrays\isEmpty(array()));
		$this->assertFalse(Gears\Arrays\isEmpty(array(1)));
	}

	public function testHas()
	{
		$this->assertTrue(Gears\Arrays\has(array('a' => 'b'), 'a'));
		$this->assertFalse(Gears\Arrays\has(array(1), 'a'));
	}

	public function testImplode()
	{
		$data = array(array('name' => 'taylor', 'email' => 'foo'), array('name' => 'dayle', 'email' => 'bar'));
		$this->assertEquals('foobar', Gears\Arrays\implode($data, 'email'));
		$this->assertEquals('foo,bar', Gears\Arrays\implode($data, 'email', ','));
	}

	public function testGroupBy()
	{
		$data = array(array('rating' => 1, 'url' => '1'), array('rating' => 1, 'url' => '1'), array('rating' => 2, 'url' => '2'));
		$this->assertEquals(array(1 => array(array('rating' => 1, 'url' => '1'), array('rating' => 1, 'url' => '1')), 2 => array(array('rating' => 2, 'url' => '2'))), Gears\Arrays\groupBy($data, 'rating'));
		$this->assertEquals(array(1 => array(array('rating' => 1, 'url' => '1'), array('rating' => 1, 'url' => '1')), 2 => array(array('rating' => 2, 'url' => '2'))), Gears\Arrays\groupBy($data, 'url'));
	}

	public function testKeyBy()
	{
		$data = [['rating' => 1, 'name' => '1'], ['rating' => 2, 'name' => '2'], ['rating' => 3, 'name' => '3']];
		$this->assertEquals([1 => ['rating' => 1, 'name' => '1'], 2 => ['rating' => 2, 'name' => '2'], 3 => ['rating' => 3, 'name' => '3']], Gears\Arrays\keyBy($data, 'rating'));
	}

	public function testFlip()
	{
		$this->assertEquals(array('1' => 'a', '2' => 'b', '3' => 'c'), Gears\Arrays\flip(array('a' => '1', 'b' => '2', 'c' => '3')));
	}

	public function testFilter()
	{
		$array = array("a"=>1, "b"=>2, "c"=>3, "d"=>4, "e"=>5);
		$this->assertEquals(array("a"=>1, "c"=>3, "e"=>5), Gears\Arrays\filter($array, function($var){ return($var & 1); }));
		$this->assertEquals(array("b"=>2, "d"=>4), Gears\Arrays\filter($array, function($var){ return(!($var & 1)); }));
	}

	public function testDiff()
	{
		$this->assertEquals(array(), Gears\Arrays\diff(array('a'), array('a')));
		$this->assertEquals(array('a'), Gears\Arrays\diff(array('a'), array()));
	}

	public function testIntersect()
	{
		$this->assertEquals(array('a'), Gears\Arrays\intersect(array('a'), array('a')));
		$this->assertEquals(array(), Gears\Arrays\intersect(array('a'), array()));
	}

	public function testContains()
	{
		$this->assertTrue(Gears\Arrays\contains(array('abc', '123', 'foo'), 'foo'));
		$this->assertFalse(Gears\Arrays\contains(array('abc', '123', 'foo'), 'xyz'));
		$this->assertTrue(Gears\Arrays\contains(array('abc', '123', 'foo'), function($key, $value){ if ($value == 'foo') return true; }));
		$this->assertFalse(Gears\Arrays\contains(array('abc', '123', 'foo'), function($key, $value){ if ($value == 'xyz') return true; }));
	}

	public function testAdd()
	{
		$this->assertEquals(array('a' => array('b' => array('c' => 'd'))), Gears\Arrays\add(array(), 'a.b.c', 'd'));
		$this->assertEquals(array('a' => array('b' => array('c' => 'd'))), Gears\Arrays\add(array(), array('a', 'b', 'c'), 'd'));
		$this->assertEquals(array('a' => 'b'), Gears\Arrays\add(array('a' => 'b'), 'a', 'xyz'));
	}

	public function testSet()
	{
		$data = array('a' => array('b'));
		$this->assertEquals(array('c' => 'd'), Gears\Arrays\set($data, 'a.b.c', 'd'));
		$this->assertEquals(array('a' => array(0 => 'b', 'b' => array('c' => 'd'))), $data);
		$this->assertEquals(array('z' => '123'), Gears\Arrays\set($data, array('x', 'y', 'z'), '123'));
		$this->assertEquals(array('a' => array(0 => 'b', 'b' => array('c' => 'd')), 'x' => array('y' => array('z' => '123'))), $data);
		$this->assertEquals('hello', Gears\Arrays\set($data, null, 'hello'));
		$this->assertEquals('hello', $data);
	}

	public function testGet()
	{
		$this->assertEquals('c', Gears\Arrays\get(array('a' => array('b' => 'c')), 'a.b'));
		$this->assertEquals('c', Gears\Arrays\get(array('a' => array('b' => 'c')), array('a', 'b')));
		$this->assertEquals(null, Gears\Arrays\get(array('a' => array('b' => 'c')), 'x.y.z'));
	}

	public function testPull()
	{
		$developer = array('firstname' => 'Ferid', 'surname' => 'Mövsümov');
		$this->assertEquals('Mövsümov', Gears\Arrays\pull($developer, 'surname'));
		$this->assertEquals(array('firstname' => 'Ferid'), $developer);
	}

	public function testBuild()
	{
		$this->assertEquals(array('foo' => 'bar'), Gears\Arrays\build(array('foo' => 'bar'), function($key, $value)
		{
			return array($key, $value);
		}));
	}

	public function testDot()
	{
		$array = Gears\Arrays\dot(array('name' => 'taylor', 'languages' => array('php' => true)));
		$this->assertEquals($array, array('name' => 'taylor', 'languages.php' => true));
	}

	public function testForget()
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

	public function testPluck()
	{
		$array = array((object) array('name' => 'taylor', 'email' => 'foo'), array('name' => 'dayle', 'email' => 'bar'));
		$this->assertEquals(array('taylor', 'dayle'), Gears\Arrays\pluck($array, 'name'));
		$this->assertEquals(array('taylor' => 'foo', 'dayle' => 'bar'), Gears\Arrays\pluck($array, 'email', 'name'));
	}

	public function testExcept()
	{
		$array = array('name' => 'taylor', 'age' => 26);
		$this->assertEquals(array('age' => 26), Gears\Arrays\except($array, array('name')));
	}

	public function testOnly()
	{
		$array = array('name' => 'taylor', 'age' => 26);
		$this->assertEquals(array('name' => 'taylor'), Gears\Arrays\only($array, array('name')));
		$this->assertSame(array(), Gears\Arrays\only($array, array('nonExistingKey')));
	}

	public function testDivide()
	{
		$array = array('name' => 'taylor');
		list($keys, $values) = Gears\Arrays\divide($array);
		$this->assertEquals(array('name'), $keys);
		$this->assertEquals(array('taylor'), $values);
	}

	public function testFetch()
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

	public function testFlatten()
	{
		$this->assertEquals(array('#foo', '#bar', '#baz'), Gears\Arrays\flatten(array(array('#foo', '#bar'), array('#baz'))));
	}

	public function testSort()
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

	public function testWhere()
	{
		$this->assertEquals
		(
			array(2 => 'foo-bar', 3 => 'Bar-Baz'),
			Gears\Arrays\where(array('fooBar', 'FooBar', 'foo-bar', 'Bar-Baz'), function($key, $value)
			{
				if (strpos($value, '-')) return true;
			})
		);
	}

	public function testIsArrayLike()
	{
		$this->assertTrue(Gears\Arrays\isArrayLike(array()));
		$this->assertTrue(Gears\Arrays\isArrayLike(new Gears\Arrays\Fluent(array())));
		$this->assertFalse(Gears\Arrays\isArrayLike('string'));
	}
}