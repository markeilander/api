<?php
return array(

	'version' => 'v1',

	'route' => [
		'v1' => [ 'prefix' => 'api/v1', 'namespace' => 'Api\V1' ]
	],

	'keys' => [
		env('API_KEY', 'secret')
	],
);