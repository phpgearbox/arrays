<?php

require('Functions.php');
require('Object.php');

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

foreach ($obj as $key => $value)
{
	echo $key.$value;
}

/*
$obj->Each(function($key, $value)
{
	echo $value;
});
*/
