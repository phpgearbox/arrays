<?php

require('Functions.php');

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

Gears\Arrays\Debug(Gears\Arrays\Search($array, '12', false));
