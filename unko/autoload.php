<?php
namespace unko;

class Autoloader
{
	private static $instance = null;

	private $root = null;
	private $registered = false;
	private $notfounds = [];

	private function __construct()
	{
		$this->root = dirname(__DIR__);
		$this->registered = false;
		$this->notfounds = [];
	}

	private static function getInstance()
	{
		if(!self::$instance)
		{
			self::$instance = new Autoloader();
		}

		return self::$instance;
	}

	private function load($class_name)
	{
		if(in_array($class_name, $this->notfounds))
		{
			return false;
		}

		$ds = DIRECTORY_SEPARATOR;

		$error_reporting_original = error_reporting();
		error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

		$filename = $this->root . $ds . str_replace("\\", $ds, $class_name) . ".php";

		$result = include_once($filename);

		error_reporting($error_reporting_original);
		return $result;
	}

	public static function autoload($class_name)
	{
		$instance = self::getInstance();
		$instance->load($class_name);
	}

	private function _regist()
	{
		if($this->registered)
		{
			return;
		}

		spl_autoload_register(array(get_class($this), "autoload"));
	}

	public static function regist()
	{
		$instance = self::getInstance();
		$instance->_regist();
	}
}

Autoloader::regist();
