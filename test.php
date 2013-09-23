<?php

require('Functions.php');
require('Object.php');
require('Conversions/Template.php');
require('Conversions/From/Csv.php');

$array =
[
	'abc' =>
	[
		'123',
		'456',
		'789'
	],
	'xyz' =>
	[
		'abc' =>
		[
			'123',
			'456',
			'789',
			'test' =>
			[
				'123'
			]
		]
	]
];

$obj = new Gears\Arrays\Object($array);

$obj->Each(function()
{
	echo 'array';
});


echo count(Gears\Arrays\Object::F($array));


$convertor = new Gears\Arrays\Conversions\From\Csv();
$array = $convertor->Convert("abc, xyz\n123, 456\ntest, test2\n");
print_r($array);
