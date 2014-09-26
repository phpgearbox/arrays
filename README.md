The Array Gear
================================================================================
[![Build Status](https://travis-ci.org/phpgearbox/arrays.svg?branch=master)](https://travis-ci.org/phpgearbox/arrays)
[![Latest Stable Version](https://poser.pugx.org/gears/arrays/v/stable.svg)](https://packagist.org/packages/gears/arrays)
[![Total Downloads](https://poser.pugx.org/gears/arrays/downloads.svg)](https://packagist.org/packages/gears/arrays)
[![License](https://poser.pugx.org/gears/arrays/license.svg)](https://packagist.org/packages/gears/arrays)

A collection of array conversions and manipulators.
There are 2 APIs:

  - One procedural based using name spaced functions / static method calls.
  - And a more fluent object based API.

I am not going to bother documenting every single last function here but please
see below for some general usage examples. The rest you can work out for
yourself by reading the source, it's fairly straight forward and well commented.

How to Install
--------------------------------------------------------------------------------
Installation via composer is easy:

	composer require gears/arrays:*

How to Use
--------------------------------------------------------------------------------
Here are a few procedural examples:

```php
$data = [];

$data = Gears\Arrays\add($data, 'a.b.c', 'd');

// $data now looks like: ['a' => ['b' => ['c' => 'd']]];

Gears\Arrays\set($data, 'a.b.c', 'foo');

// $data now looks like: ['a' => ['b' => ['c' => 'foo']]];

Gears\Arrays\forget($data, 'a.b.c');

// $data now looks like: ['a' => ['b' => []]];
```

In PHP 5.6 you can import functions so you could change the above to:

```php
// Import the functions
use function Gears\Arrays\add;
use function Gears\Arrays\set;
use function Gears\Arrays\forget;

// This results in the same array
$data = [];
$data = add($data, 'a.b.c', 'd');
set($data, 'a.b.c', 'foo');
forget($data, 'a.b.c');
```

> NOTE: All function names are camelCased.

Prior to PHP 5.6 this is not possible. So you can do this instead:

```php
// Import the Array class
use Gears\Arrays as Arr;

// This results in the same array
$data = [];
$data = Arr::add($data, 'a.b.c', 'd');
Arr::set($data, 'a.b.c', 'foo');
Arr::forget($data, 'a.b.c');
```

> NOTE: Just like the standard array_ functions included in PHP.
> Some functions act on a refrence of an array while others will take a copy
> and return the modifications or other values.

The Fluent Array Object:
--------------------------------------------------------------------------------
Okay so this is such a massive part of the package it requires it's own HOW-TO
section. To get started you can create a new object like so:

```php
$data = new Gears\Arrays\Fluent();
$data[] = 'foo';
$data[] = 'bar';

foreach ($data as $item)
{
	echo $item.',';
}

// you would see: foo,bar
```

As you can see the data variable is now an Array-Like object. This is key
because some times certian functions will expect real arrays.
To get a real array back out of the fluent object you can do this:

```php
$real_array = $data->toArray();
```

You may wish to use a factory method to initiate a new Fluent object.

```php
// Import the Array class
use Gears\Arrays as Arr;

// Use the factory method
$data = Arr::a();
```

When you are using the Fluent API, please note how the subsequent method
call signature changes vs that of the procedural api. You no longer need
to provide the array to be performed on as the first argument. This is
automatically done for you.

Here is the same example from the procedural api:

```php
$data = Arr::a();
$data->add('a.b.c', 'd');
$data->set('a.b.c', 'foo');
$data->forget('a.b.c');
```

Now a keen eye might have thought that on line 2 there is an error.
You might be thinking that the second line should look like this:

```php
$data = $data->add('a.b.c', 'd');
```

But you would be wrong. Unlike the ```Gears\String``` package. The Fluent API
and the Procedural API of ```Gears\Arrays```, while similar, they are not
identical. In part this is due to the more complex nature of arrays. Please
beware that there are methods in both APIs that have the same name yet do
slightly different things.

For example I can also use the add method like so:

```php
Arr::a([1,2,3])->add(4)->each(function($v){ echo $v.','; });

// would output: 1,2,3,4,
```

**Recursive Nature:** Unlike a standard ```Illuminate\Support\Collection```
object a ```Gears\Arrays\Fluent``` object is recursive. Each new fluent
object is only loaded when it is accessed, thus minimizing any performance
losses.

Lets show with an example:

```php
$data = Arr::a(['a' => ['b' => ['c' => 'd']]]);

print_r($data);

// you would see something like:
Gears\Arrays\Fluent Object
(
	[items:protected] => Array
	(
		[a] => Array
		(
			[b] => Array
			(
				[c] => 'd'
			)
		)
	)
)

// now lets access something
$data['a']['b']['c'];

print_r($data);

// you would now see something like:
Gears\Arrays\Fluent Object
(
	[items:protected] => Array
	(
		[a] => Gears\Arrays\Fluent Object
		(
			[items:protected] => Array
			(
				[b] => Gears\Arrays\Fluent Object
				(
					[items:protected] => Array
					(
						[c] => d
					)
				)
			)
		)
	)
)
```

**Object Access:** The last thing I would like to show off is the way you can
now use your array is if it were an object. Again I like my examples:

```php
$data1 = Arr::a();
$data1['a'] = [];
$data1['a']['b'] = [];
$data1['a']['b']['c'] = 'd';

$data2 = Arr::a();
$data2->a = [];
$data2->a->b = [];
$data2->a->b->c = 'd';

// $data1 == $data2
```

This Readme only skims the surface of what is possible with the Fluent API.
I promise a full set of documentation is coming... but I do have a life as well.

Laravel Integration
--------------------------------------------------------------------------------
*Gears\Arrays* has been designed as functionally compatible to the *Laravel Arr*
class, in fact it extends the class. Thus everything you could do before you can
still do and then some.

By default Laravel does not alias the Arr class.  So feel free to add the
following to your alias list in the file ```/app/config/app.php```:

```php
'Arr' => 'Gears\Arrays',
```

> Also note that ```Gears\Arrays\Fluent``` is again compaitible
> with the standard ```Illuminate\Support\Collection``` class.

Credits
--------------------------------------------------------------------------------
Thanks to *axelarge* for the inspiration, I have taken his methods re-factored
them slightly and added a few of my own methods, into the mix.
https://github.com/axelarge/php-array-tools

Additionally all methods in the class ```Illuminate\Support\Arr```
provided by Laravel. Have been integrated into ```Gears\Array```.
https://github.com/laravel/framework/blob/4.2/src/Illuminate/Support/Arr.php

Also from Laravel ```Illuminate\Support\Collection```, our fluent interface
extends this class. Some have refered to the Laravel Collection class as
Arrays on steriods. So I am not sure what you would call our fluent class.
*Arrays on INSERT HARD CORE DRUG HERE*...

--------------------------------------------------------------------------------
Developed by Brad Jones - brad@bjc.id.au