<?php
namespace models;
use classes\abstracts\Base;
class PathFile extends Base
{
	const T_CLASS = 'class';
	const T_FILE = 'file';
	public function __construct(string $type, string $file, array $data = []) {
		$this->type = $type;
		$this->file = $file;
		$this->__base_data = $data;
	}
	public function load()
	{
		switch ($this->type)
		{
			case self::T_FILE:
				return $this->loadFile();
			case self::T_CLASS:
				return $this->loadClass();
		}
		return null;
	}
	public function loadFile()
	{
		if(!file_exists($this->file))
			return null;
		return file_get_contents($this->file);
	}
	public function loadClass()
	{
		var_dump($this);
		if(!class_exists($this->__base_data['class'])) return null;
		$class = new $this->__base_data['class'];
		return var_export($class, true);
	}
	public static function _404()
	{
		header("HTTP/1.1 404 Not Found");
		return new self('file', WWWROOT . "/404.html");
	}
}