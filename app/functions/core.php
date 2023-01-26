<?php namespace functions\core;

require_once(ROOT . "/classes/interfaces/Base.php");
require_once(ROOT . "/classes/abstracts/Base.php");
require_once(ROOT . "/models/Autoload.php");
/**
 * <summery>
 * This function attempts to convert the value of the `$value` parameter to one of several possible types, specified in the `$types` parameter.
 * The `$types` parameter can be either a string containing a pipe-separated list of types, or an array of types. The function will iterate through the specified types and attempt to cast `$value` to each type in turn, until it finds one that works or runs out of types to try.
 * If a successful type conversion is found, the function will update the value of `$value` to the new value and return `true`. Otherwise, it will return `false`.
 * </summery>
 * @param mixed $value The input value to try and convert.
 * @param string|array $types The types that the value can be converted into. Can be separated with a pipe (`|`) if passed as a string.
 * @param string|null $convertedType The type that the value was successfully converted into. If the conversion fails, this parameter will be set to `null`.
 * @return bool `true` if the type conversion was successful, `false` otherwise.
 */
function tryParse(&$value, $types, &$convertedType = false)
{
	if(is_string($types)) $types = explode("|", $types);
	$newValue = $value;
	foreach($types as $type)
	{
		try
		{
			switch($type)
			{
				case "boolean":
					if($value == 'true' || $value == '1' || $value == 'on')
					{
						$newValue = true;
					}elseif($value == 'false' || $value == '0' || $value == 'off')
					{
						$newValue = false;
					}
					break;
				case "integer":
					$newValue = (int) $value;
					break;
				case "double":
					$newValue = (double) $value;
					break;
				case "string":
					$newValue = (string) $value;
					break;
				case "array":
					$newValue = (array) $value;
					break;
				case "object":
					$newValue = (object) $value;
					break;
			}
			$value = $newValue;
			$convertedType = $type;
			return true;
		}catch(\Exception $e)
		{ }
	}
	return false;
}