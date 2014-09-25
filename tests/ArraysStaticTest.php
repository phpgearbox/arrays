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

class ArraysStaticTest extends PHPUnit_Framework_TestCase
{
		public function testValues()
	{
		$data = array(array('id' => 1, 'name' => 'Hello'), array('id' => 2, 'name' => 'World'));
		$data = Arr::filter($data, function($item){ return $item['id'] == 2; });
		Arr::values($data);
		$this->assertEquals(array(array('id' => 2, 'name' => 'World')), $data);
	}

	public function testUnique()
	{
		$this->assertEquals(array('Hello', 'World'), Arr::unique(array('Hello', 'World', 'World')));
	}

	public function testTransform()
	{
		$data = array('taylor', 'colin', 'shawn');
		Arr::transform($data, function($item) { return strrev($item); });
		$this->assertEquals(array('rolyat', 'niloc', 'nwahs'), array_values($data));
	}

	public function testTake()
	{
		$this->assertEquals(array('taylor', 'dayle'), Arr::take(array('taylor', 'dayle', 'shawn'), 2));
	}

	public function testSum()
	{
		$data = array();
		$this->assertEquals(0, Arr::sum($data, 'foo'));

		$data = array((object) array('foo' => 50), (object) array('foo' => 50));
		$this->assertEquals(100, Arr::sum($data, 'foo'));

		$data = array((object) array('foo' => 50), (object) array('foo' => 50));
		$this->assertEquals(100, Arr::sum($data, function($i) { return $i->foo; }));
	}

	public function testSplice()
	{
		$data = array('foo', 'baz');
		Arr::splice($data, 1, 0, 'bar');
		$this->assertEquals(array('foo', 'bar', 'baz'), $data);

		$data = array('foo', 'baz');
		Arr::splice($data, 1, 1);
		$this->assertEquals(array('foo'), $data);

		$data = array('foo', 'baz');
		$cut = Arr::splice($data, 1, 1, 'bar');
		$this->assertEquals(array('foo', 'bar'), $data);
		$this->assertEquals(array('baz'), $cut);
	}

	public function testSortBy()
	{
		$data = array('taylor', 'dayle');
		Arr::sortBy($data, function($x) { return $x; });
		$this->assertEquals(array(1 => 'dayle', 0 => 'taylor'), $data);
	}

	public function testSortByDesc()
	{
		$data = array('dayle', 'taylor');
		Arr::sortByDesc($data, function($x) { return $x; });
		$this->assertEquals(array(1 => 'taylor', 0 => 'dayle'), $data);
	}

	public function testChunk ()
	{
		$data = Arr::chunk(array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10), 3);
		$this->assertEquals(4, count($data));
		$this->assertEquals(array(1, 2, 3), $data[0]);
		$this->assertEquals(array(10), $data[3]);
	}

	public function testSlice()
	{
		$array = array("a", "b", "c", "d", "e");
		$this->assertEquals(array('c', 'd', 'e'), Arr::slice($array, 2));
		$this->assertEquals(array('d'), Arr::slice($array, -2, 1));
		$this->assertEquals(array('a', 'b', 'c'), Arr::slice($array, 0, 3));
		$this->assertEquals(array(2 => 'c', 3 => 'd'), Arr::slice($array, 2, -1, true));
	}

	public function testShift()
	{
		$array = array('foo', 'bar');
		$this->assertEquals('foo', Arr::shift($array));
		$this->assertEquals('bar', Arr::first($array));
	}

	public function testSearch()
	{
		$array = array(0 => 'blue', 1 => 'red', 2 => 'green', 3 => 'red');
		$this->assertEquals(2, Arr::search($array, 'green'));
		$this->assertEquals(1, Arr::search($array, 'red'));
	}

	public function testReverse()
	{
		$this->assertEquals(array('alan', 'zaeed'), Arr::reverse(array('zaeed', 'alan')));
	}

	public function testReject()
	{
		$this->assertEquals(['foo'], Arr::reject(['foo', 'bar'], 'bar'));
		$this->assertEquals(['foo'], Arr::reject(['foo', 'bar'], function($v) { return $v == 'bar'; }));
		$this->assertEquals(['foo'], Arr::reject(['foo', null], null));
		$this->assertEquals(['foo', 'bar'], Arr::reject(['foo', 'bar'], 'baz'));
		$this->assertEquals(['foo', 'bar'], Arr::reject(['foo', 'bar'], function($v) { return $v == 'baz'; }));
	}

	public function testReduce()
	{
		$this->assertEquals(15, Arr::reduce(array(1, 2, 3, 4, 5), function($carry, $item){ $carry += $item; return $carry; }));
	}

	public function testRandom()
	{
		$data = array(1, 2, 3, 4, 5, 6);
		$random = Arr::random($data);
		$this->assertInternalType('integer', $random);
		$this->assertContains($random, $data);
		$this->assertCount(3, Arr::random($data, 3));
		$this->assertNull(Arr::random(array()));
	}

	public function testEach()
	{
		$GLOBALS['each'] = array();
		Arr::each(array(1, 2, 3), function($value) { $GLOBALS['each'][] = $value+1; });
		$this->assertEquals(array(2, 3, 4), $GLOBALS['each']);

		$GLOBALS['each'] = array();
		Arr::each(array(1, 2, 3), function($value, $key){ $GLOBALS['each'][] = $value+$key; });
		$this->assertEquals(array(1, 3, 5), $GLOBALS['each']);

		$GLOBALS['each'] = array();
		Arr::each(array(1, 2, 3), function($value){ $GLOBALS['each'][] = $value+1; return false; });
		$this->assertEquals(array(2), $GLOBALS['each']);

		unset($GLOBALS['each']);
	}

	public function testPut()
	{
		$array = array('foo', 'bar');
		Arr::put($array, 0, 'xyz');
		$this->assertEquals('xyz', Arr::first($array));
	}

	public function testPush()
	{
		$array = array('foo', 'bar');
		Arr::push($array, 'xyz');
		$this->assertEquals('xyz', Arr::last($array));
	}

	public function testPrepend()
	{
		$array = array('foo', 'bar');
		Arr::prepend($array, 'xyz');
		$this->assertEquals('xyz', Arr::first($array));
	}

	public function testUnshift()
	{
		$array = array('foo', 'bar');
		$this->assertEquals(3, Arr::unshift($array, 'xyz'));
		$this->assertEquals('xyz', Arr::first($array));
	}

	public function testPop()
	{
		$array = array('foo', 'bar');
		$this->assertEquals('bar', Arr::pop($array));
		$this->assertEquals('foo', Arr::first($array));
	}

	public function testMap()
	{
		$this->assertEquals(array(2, 3, 4), Arr::map(array(1, 2, 3), function($value){ return $value+1; }));
		$this->assertEquals(array(1, 3, 5), Arr::map(array(1, 2, 3), function($value, $key){ return $value+$key; }));
	}

	public function testMerge()
	{
		$this->assertEquals(array('name' => 'Hello', 'id' => 1), Arr::merge(array('name' => 'Hello'), array('id' => 1)));
	}

	public function testFirst()
	{
		$this->assertEquals('foo', Arr::first(array('foo', 'bar')));
		$array = array('name' => 'taylor', 'otherDeveloper' => 'dayle');
		$this->assertEquals('dayle', Arr::first($array, function($key, $value) { return $value == 'dayle'; }));
	}

	public function testLast()
	{
		$this->assertEquals('bar', Arr::last(array('foo', 'bar')));
		$array = array(100, 250, 290, 320, 500, 560, 670);
		$this->assertEquals(670, Arr::last($array, function($key, $value) { return $value > 320; }));
	}

	public function testLists()
	{
		$data = array((object) array('name' => 'taylor', 'email' => 'foo'), array('name' => 'dayle', 'email' => 'bar'));
		$this->assertEquals(array('taylor' => 'foo', 'dayle' => 'bar'), Arr::lists($data, 'email', 'name'));
		$this->assertEquals(array('foo', 'bar'), Arr::lists($data, 'email'));
	}

	public function testKeys()
	{
		$this->assertEquals(array(0, 1, 2), Arr::keys(array('a', 'b', 'c')));
	}

	public function testIsEmpty()
	{
		$this->assertTrue(Arr::isEmpty(array()));
		$this->assertFalse(Arr::isEmpty(array(1)));
	}

	public function testHas()
	{
		$this->assertTrue(Arr::has(array('a' => 'b'), 'a'));
		$this->assertFalse(Arr::has(array(1), 'a'));
	}

	public function testImplode()
	{
		$data = array(array('name' => 'taylor', 'email' => 'foo'), array('name' => 'dayle', 'email' => 'bar'));
		$this->assertEquals('foobar', Arr::implode($data, 'email'));
		$this->assertEquals('foo,bar', Arr::implode($data, 'email', ','));
	}

	public function testGroupBy()
	{
		$data = array(array('rating' => 1, 'url' => '1'), array('rating' => 1, 'url' => '1'), array('rating' => 2, 'url' => '2'));
		$this->assertEquals(array(1 => array(array('rating' => 1, 'url' => '1'), array('rating' => 1, 'url' => '1')), 2 => array(array('rating' => 2, 'url' => '2'))), Arr::groupBy($data, 'rating'));
		$this->assertEquals(array(1 => array(array('rating' => 1, 'url' => '1'), array('rating' => 1, 'url' => '1')), 2 => array(array('rating' => 2, 'url' => '2'))), Arr::groupBy($data, 'url'));
	}

	public function testKeyBy()
	{
		$data = [['rating' => 1, 'name' => '1'], ['rating' => 2, 'name' => '2'], ['rating' => 3, 'name' => '3']];
		$this->assertEquals([1 => ['rating' => 1, 'name' => '1'], 2 => ['rating' => 2, 'name' => '2'], 3 => ['rating' => 3, 'name' => '3']], Arr::keyBy($data, 'rating'));
	}

	public function testFlip()
	{
		$this->assertEquals(array('1' => 'a', '2' => 'b', '3' => 'c'), Arr::flip(array('a' => '1', 'b' => '2', 'c' => '3')));
	}

	public function testFilter()
	{
		$array = array("a"=>1, "b"=>2, "c"=>3, "d"=>4, "e"=>5);
		$this->assertEquals(array("a"=>1, "c"=>3, "e"=>5), Arr::filter($array, function($var){ return($var & 1); }));
		$this->assertEquals(array("b"=>2, "d"=>4), Arr::filter($array, function($var){ return(!($var & 1)); }));
	}

	public function testDiff()
	{
		$this->assertEquals(array(), Arr::diff(array('a'), array('a')));
		$this->assertEquals(array('a'), Arr::diff(array('a'), array()));
	}

	public function testIntersect()
	{
		$this->assertEquals(array('a'), Arr::intersect(array('a'), array('a')));
		$this->assertEquals(array(), Arr::intersect(array('a'), array()));
	}

	public function testContains()
	{
		$this->assertTrue(Arr::contains(array('abc', '123', 'foo'), 'foo'));
		$this->assertFalse(Arr::contains(array('abc', '123', 'foo'), 'xyz'));
		$this->assertTrue(Arr::contains(array('abc', '123', 'foo'), function($key, $value){ if ($value == 'foo') return true; }));
		$this->assertFalse(Arr::contains(array('abc', '123', 'foo'), function($key, $value){ if ($value == 'xyz') return true; }));
	}

	public function testAdd()
	{
		$this->assertEquals(array('a' => array('b' => array('c' => 'd'))), Arr::add(array(), 'a.b.c', 'd'));
		$this->assertEquals(array('a' => array('b' => array('c' => 'd'))), Arr::add(array(), array('a', 'b', 'c'), 'd'));
		$this->assertEquals(array('a' => 'b'), Arr::add(array('a' => 'b'), 'a', 'xyz'));
	}

	public function testSet()
	{
		$data = array('a' => array('b'));
		$this->assertEquals(array('c' => 'd'), Arr::set($data, 'a.b.c', 'd'));
		$this->assertEquals(array('a' => array(0 => 'b', 'b' => array('c' => 'd'))), $data);
		$this->assertEquals(array('z' => '123'), Arr::set($data, array('x', 'y', 'z'), '123'));
		$this->assertEquals(array('a' => array(0 => 'b', 'b' => array('c' => 'd')), 'x' => array('y' => array('z' => '123'))), $data);
		$this->assertEquals('hello', Arr::set($data, null, 'hello'));
		$this->assertEquals('hello', $data);
	}

	public function testGet()
	{
		$this->assertEquals('c', Arr::get(array('a' => array('b' => 'c')), 'a.b'));
		$this->assertEquals('c', Arr::get(array('a' => array('b' => 'c')), array('a', 'b')));
		$this->assertEquals(null, Arr::get(array('a' => array('b' => 'c')), 'x.y.z'));
	}

	public function testPull()
	{
		$developer = array('firstname' => 'Ferid', 'surname' => 'Mövsümov');
		$this->assertEquals('Mövsümov', Arr::pull($developer, 'surname'));
		$this->assertEquals(array('firstname' => 'Ferid'), $developer);
	}

	public function testBuild()
	{
		$this->assertEquals(array('foo' => 'bar'), Arr::build(array('foo' => 'bar'), function($key, $value)
		{
			return array($key, $value);
		}));
	}

	public function testDot()
	{
		$array = Arr::dot(array('name' => 'taylor', 'languages' => array('php' => true)));
		$this->assertEquals($array, array('name' => 'taylor', 'languages.php' => true));
	}

	public function testForget()
	{
		$array = array('names' => array('developer' => 'taylor', 'otherDeveloper' => 'dayle'));
		Arr::forget($array, 'names.developer');
		$this->assertFalse(isset($array['names']['developer']));
		$this->assertTrue(isset($array['names']['otherDeveloper']));

		$array = ['names' => ['developer' => 'taylor', 'otherDeveloper' => 'dayle', 'thirdDeveloper' => 'Lucas']];
		Arr::forget($array, ['names.developer', 'names.otherDeveloper']);
		$this->assertFalse(isset($array['names']['developer']));
		$this->assertFalse(isset($array['names']['otherDeveloper']));
		$this->assertTrue(isset($array['names']['thirdDeveloper']));

		$array = ['names' => ['developer' => 'taylor', 'otherDeveloper' => 'dayle'], 'otherNames' => ['developer' => 'Lucas', 'otherDeveloper' => 'Graham']];
		Arr::forget($array, ['names.developer', 'otherNames.otherDeveloper']);
		$expected = ['names' => ['otherDeveloper' => 'dayle'], 'otherNames' => ['developer' => 'Lucas']];
		$this->assertEquals($expected, $array);
	}

	public function testPluck()
	{
		$array = array((object) array('name' => 'taylor', 'email' => 'foo'), array('name' => 'dayle', 'email' => 'bar'));
		$this->assertEquals(array('taylor', 'dayle'), Arr::pluck($array, 'name'));
		$this->assertEquals(array('taylor' => 'foo', 'dayle' => 'bar'), Arr::pluck($array, 'email', 'name'));
	}

	public function testExcept()
	{
		$array = array('name' => 'taylor', 'age' => 26);
		$this->assertEquals(array('age' => 26), Arr::except($array, array('name')));
	}

	public function testOnly()
	{
		$array = array('name' => 'taylor', 'age' => 26);
		$this->assertEquals(array('name' => 'taylor'), Arr::only($array, array('name')));
		$this->assertSame(array(), Arr::only($array, array('nonExistingKey')));
	}

	public function testDivide()
	{
		$array = array('name' => 'taylor');
		list($keys, $values) = Arr::divide($array);
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
		), Arr::fetch($data, 'comments'));

		$this->assertEquals(array(array('#foo', '#bar'), array('#baz')), Arr::fetch($data, 'comments.tags'));
	}

	public function testFlatten()
	{
		$this->assertEquals(array('#foo', '#bar', '#baz'), Arr::flatten(array(array('#foo', '#bar'), array('#baz'))));
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
		array_values(Arr::sort($array, function($v) { return $v['name']; })));
	}

	public function testWhere()
	{
		$this->assertEquals
		(
			array(2 => 'foo-bar', 3 => 'Bar-Baz'),
			Arr::where(array('fooBar', 'FooBar', 'foo-bar', 'Bar-Baz'), function($key, $value)
			{
				if (strpos($value, '-')) return true;
			})
		);
	}

	public function testIsArrayLike()
	{
		$this->assertTrue(Arr::isArrayLike(array()));
		$this->assertTrue(Arr::isArrayLike(new Gears\Arrays\Fluent(array())));
		$this->assertFalse(Arr::isArrayLike('string'));
	}
}