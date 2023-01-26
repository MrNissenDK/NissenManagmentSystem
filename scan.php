<?php
/**
 * run in a cron job for keeping paths uptodate
 * or run it mannuly when you made a path
 */
use models\PathFile;
const ROOT = __DIR__ . "/app";
include_once(ROOT . "/functions/core.php");
function export(array $array)
{
	$map = [];
	foreach($array as $key => $value)
	{
		switch(gettype($value))
		{
			case "boolean": 
			{
				$value = $value ? 'true' : 'false';
			}
			case "integer":
			case "double" :
			{
				break;
			}
			case "string" :
			{
					$value = "'{$value}'";
					break;
			}
			case "array"  :
			{
				$value = export($value);
				break;
			}
			default: continue;
		}
		$map[] = "'$key' => $value";
	}
	return "[" . join(', ', $map) . "]";
}

if ( ! function_exists('glob_recursive'))
{
	// Does not support flag GLOB_BRACE        
	function glob_recursive($pattern, $flags = 0)
	{
		$files = glob($pattern, $flags);
		foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
		{
			$files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
		}
		return $files;
	}
}
$baseControlles = __DIR__ . "/app/";
$controllers = glob_recursive("{$baseControlles}*Controller.php");
$output = [];
ob_start();
if($controllers)
{
	foreach($controllers as $_path)
	{
		$path = str_replace([$baseControlles, '.php'], "", $_path);

		$controller = explode("/", $path);
		$controller = join("\\", $controller);
		$route = str_replace(["server/controllers", "controller", "/home"], "", strtolower($path));
		var_dump($route, $controller, $path);
		if (!class_exists($controller)) continue;
		foreach(get_class_methods($controller) as $_methode)
		{
			$methode = explode("_", $_methode);
			$pathMap = &$output["$route/" . str_replace("index", "", strtolower($methode[0]))];
			$data = [];
			if(empty($data)) $data = [
				'class' => $controller,
				'function' => $_methode,
				'methodes' => []
			];
			if (empty($methode[1]))
				$data['methodes'] []= 'ALL';
			else
				$data['methodes'] []= strtoupper($methode[1]);
			$pathMap = export(['class', $_path, $data]);
		}
	}
}
$map = [];
foreach($output as $path => $file)
{
	$map[] = "\t'$path' => $file";
}
$pathMapTemplate = file_get_contents(__DIR__ . "/PathMap.template");
$pathMapTemplate = preg_replace(
	[
		"{{{map}}}",
		"{{{time}}}"
	],
	[
		str_replace(["  ", "\n"], ["\t", "\n\t"], "[\r\n" . join(",\r\n", $map) . "\r\n]"),
		date(DATE_RFC3339)
	], $pathMapTemplate
);
file_put_contents(ROOT . "/server/PathMap.php", $pathMapTemplate);