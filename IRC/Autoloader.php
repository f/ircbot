<?php
namespace IRC;

class Autoloader {

	public static function register()
	{
		spl_autoload_register(array(__CLASS__, 'loadClass'));
	}

	public static function loadClass($name)
	{
		$class_path = explode('\\', $name);
		array_shift($class_path);
		require_once(__DIR__ . '/' . implode(DIRECTORY_SEPARATOR, $class_path) . '.php');
	}

}