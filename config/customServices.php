<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, SparkPost and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	 */

	// 'app_per' => env('APP_PER', 'Administer roles & permissions'),
	'app_name'  => env('APP_NAME', 'Test'),
	'app_short_name'  => env('APP_SHORTNAME', 'T A'),
	'app_per'   => env('APP_PER', 'Administrator roles & permissions'),
	// 'app_per'   => env('APP_PER', 'Administer roles & permissions'),
	'services'  => [
		
		'client-visit' => env('API_CLIENTVISIT'),
		'career'   => env('API_CAREER'),
		'userinfo'   => env('API_USERINFO'),
		'gateway' =>env('API_GATEWAY'),
		'auth'=>env('API_AUTH')

	],

	'app_log' => env('APP_LOG'),
	'app_domain'=> env('APP_DOMAIN'),
	'app_env' => env('APP_ENV'),
	

];
