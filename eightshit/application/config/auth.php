<?php defined('SYSPATH') or die('No direct access allowed.');

return array(

	'driver'       => 'file',
	'hash_method'  => 'sha256',
	'hash_key'     => 'dumbwoeifh9309BUTTMAN$$$',
	'lifetime'     => 1209600,
	'session_key'  => 'put_something_else_here',

	// Username/password combinations for the Auth File driver
	// See the Kohana Auth Module to get a better idea of how to use this
	'users' => array(
		'admin' => 'gonna need to run a passwd hash here',
	),

);
