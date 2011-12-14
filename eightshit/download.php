<?php

// I'm not fully sure what this file is here for

/**
 * The directory in which your application specific resources are located.
 * The application directory must contain the bootstrap.php file.
 *
 * @see  http://kohanaframework.org/guide/about.install#application
 */
$application = 'application';

/**
 * The directory in which your modules are located.
 *
 * @see  http://kohanaframework.org/guide/about.install#modules
 */
$modules = 'modules';

/**
 * The directory in which the Kohana resources are located. The system
 * directory must contain the classes/kohana.php file.
 *
 * @see  http://kohanaframework.org/guide/about.install#system
 */
$system = 'system';

/**
 * The default extension of resource files. If you change this, all resources
 * must be renamed to use the new extension.
 *
 * @see  http://kohanaframework.org/guide/about.install#ext
 */
define('EXT', '.php');

/**
 * Set the PHP error reporting level. If you set this in php.ini, you remove this.
 * @see  http://php.net/error_reporting
 *
 * When developing your application, it is highly recommended to enable notices
 * and strict warnings. Enable them by using: E_ALL | E_STRICT
 *
 * In a production environment, it is safe to ignore notices and strict warnings.
 * Disable them by using: E_ALL ^ E_NOTICE
 *
 * When using a legacy application with PHP >= 5.3, it is recommended to disable
 * deprecated notices. Disable with: E_ALL & ~E_DEPRECATED
 */
error_reporting(E_ALL | E_STRICT);

/**
 * End of standard configuration! Changing any of the code below should only be
 * attempted by those with a working knowledge of Kohana internals.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 */

// Set the full path to the docroot
define('DOCROOT', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

// Make the application relative to the docroot, for symlink'd index.php
if ( ! is_dir($application) AND is_dir(DOCROOT.$application))
	$application = DOCROOT.$application;

// Make the modules relative to the docroot, for symlink'd index.php
if ( ! is_dir($modules) AND is_dir(DOCROOT.$modules))
	$modules = DOCROOT.$modules;

// Make the system relative to the docroot, for symlink'd index.php
if ( ! is_dir($system) AND is_dir(DOCROOT.$system))
	$system = DOCROOT.$system;

// Define the absolute paths for configured directories
define('APPPATH', realpath($application).DIRECTORY_SEPARATOR);
define('MODPATH', realpath($modules).DIRECTORY_SEPARATOR);
define('SYSPATH', realpath($system).DIRECTORY_SEPARATOR);

// Clean up the configuration vars
unset($application, $modules, $system);

if (file_exists('install'.EXT))
{
	// Load the installation check
	return include 'install'.EXT;
}

/**
 * Define the start time of the application, used for profiling.
 */
if ( ! defined('KOHANA_START_TIME'))
{
	define('KOHANA_START_TIME', microtime(TRUE));
}

/**
 * Define the memory usage at the start of the application, used for profiling.
 */
if ( ! defined('KOHANA_START_MEMORY'))
{
	define('KOHANA_START_MEMORY', memory_get_usage());
}

// Bootstrap the application
require APPPATH.'bootstrap'.EXT;

if(isset($GLOBALS['HTTP_RAW_POST_DATA']))
{
	$image_binary = $GLOBALS['HTTP_RAW_POST_DATA'];

	$raw_name = tempnam(APPPATH.'tmp', 'hell');
	$image_name = "shit_".Text::random('alnum', 6).'_'.time().'.png';

	$fh = fopen($raw_name, 'wb');
	fwrite($fh, $image_binary);
	fclose($fh);

	chmod($raw_name, 777);

	$valid_user = TRUE;
	$image_saved = TRUE;
	$user_saved = TRUE;

	try
	{
		$user = DB::select()->from('users')->where('userid', '=', $_GET['userInfo'])->limit(1)->execute();
		$user = $user[0];
	}
	catch(Exception $e)
	{
		$user = NULL;
		$valid_user = FALSE;
	}

	if($valid_user === TRUE)
	{
		try
		{
			if(Image::factory($raw_name)->resize(128, 128)->save(APPPATH.DIRECTORY_SEPARATOR.'incoming/'.$image_name) === FALSE)
			{
				$image_saved = FALSE;
			}

		}
		catch(Exception $e)
		{
			$image_saved = FALSE;
		}

		if($image_saved)
		{
			try
			{
				DB::insert('pending_images', array('user_id', 'image'))->values(array($user['id'], $image_name))->execute();
			}
			catch(Exception $e)
			{
				$user_saved = FALSE;
			}
		}

		if($valid_user === TRUE && $image_saved === TRUE && $user_saved === TRUE)
		{
			die('true');
		}
		else
		{
			die('false');
		}
	}


}