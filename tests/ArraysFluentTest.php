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

class ArraysFluentTest extends PHPUnit_Framework_TestCase
{
	public function testFactoryMethod()
	{
		$this->assertInstanceOf('Gears\Arrays\Fluent', Arr::a());
	}

	public function testIterator()
	{
		foreach (Arr::a([[1,2,3]]) as $item)
		{
			$this->assertInstanceOf('Gears\Arrays\Fluent', $item);
		}
	}

	public function testArrayAccess()
	{
		$data = Arr::a([[1,2,3]]);
		$this->assertInstanceOf('Gears\Arrays\Fluent', $data[0]);

		$data[0] = 'foo';
		$this->assertEquals('foo', $data[0]);

		$data = Arr::a(['a' => ['b' => ['c' => 'd']]]);
		$this->assertEquals('d', $data['a']['b']['c']);

		$data = Arr::a();
		$data['a'] = [];
		$data['a']['b'] = [];
		$data['a']['b']['c'] = 'd';
		$this->assertEquals('d', $data['a']['b']['c']);
	}

	public function testObjectAccess()
	{
		$data = Arr::a([[1,2,3]]);
		$this->assertInstanceOf('Gears\Arrays\Fluent', $data->{0});

		$data->{0} = 'foo';
		$this->assertEquals('foo', $data->{0});

		$data = Arr::a(['a' => ['b' => ['c' => 'd']]]);
		$this->assertEquals('d', $data->a->b->c);

		$data = Arr::a();
		$data->a = [];
		$data->a->b = [];
		$data->a->b->c = 'd';
		$this->assertEquals('d', $data->a->b->c);
	}

	public function testMacro()
	{
		Arr::macro('mockMacro', function($input){ $input[] = 'bar'; return $input; });
		$this->assertEquals(['foo', 'bar'], Arr::a(['foo'])->mockMacro()->toArray());
	}

	public function testSet()
	{
		$data = Arr::a();
		$data->set('a.b.c', 'd');
		$this->assertEquals('d', $data['a']['b']['c']);
		$this->assertEquals('d', $data->a->b->c);

		$data = Arr::a();
		$data->set(['a','b','c'], 'd');
		$this->assertEquals('d', $data['a']['b']['c']);
		$this->assertEquals('d', $data->a->b->c);

		$data = Arr::a(['a' => ['b' => ['c' => 'd']]]);
		$data['a']['b']['c'];
		$data->set(['a','b','x'], '123');
		$this->assertEquals('123', $data['a']['b']['x']);
		$this->assertEquals('123', $data->a->b->x);
	}

	public function testGet()
	{
		$data = Arr::a(['a' => ['b' => ['c' => 'd']]]);
		$this->assertEquals('d', $data->get('a.b.c'));
		$this->assertEquals('d', $data->get(['a','b','c']));
		$this->assertEquals(['b' => ['c' => 'd']], $data->get('a')->toArray());
	}

	public function testAdd()
	{
		$this->assertEquals(['a' => ['b' => ['c' => 'd']]], Arr::a()->add('a.b.c', 'd')->toArray());
		$this->assertEquals(['a' => ['b' => ['c' => 'd']]], Arr::a(['a' => ['b' => ['c' => 'd']]])->add('a.b.c', '123')->toArray());
	}
}