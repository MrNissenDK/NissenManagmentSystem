<?php namespace models;
use classes\abstracts\Base;
use DateTime;
/**
 * Summary of Autoload
 * @property string $dir
 * @property string $file
 * @property \DateTime $time
 * @property bool $exists
 */
class Autoload extends Base
{
	private static $loaded = [];
	public function __construct(string $filepath = null) {
		$this->dir = dirname($filepath);
		$this->file = basename($filepath);
		$this->time = new DateTime();
		$this->exists = file_exists($filepath);
		if($this->exists) require_once($filepath);
		parent::__construct();
	}
	public static function getLoaded()
	{
		return self::$loaded;
	}
	/**
 * @property string $fileName
 */
	public static function loadFile(string $filepath)
	{
		$load = new self($filepath);
		self::$loaded[] = $load;
		return $load->exists;
	}
	public static function load(string $className)
	{
		$path = ROOT . "/" . str_replace("\\", "/", $className) . ".php";
		self::loadFile($path);
	}
}
spl_autoload_register(function ($className) {
	Autoload::load($className);
});