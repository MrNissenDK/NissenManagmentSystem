<?php namespace classes\abstracts;
use ErrorException;
use function functions\core\tryParse;
/**
 * The Base abstract class provides a basic structure for creating classes with custom getters and setters for properties.
 * 
 * The class uses the `Prop` interface to define custom behavior for specific properties. Properties can be defined
 * with custom getters and setters, or use the default behavior if none are defined.
 * 
 * The class also provides a way to define expected data types for properties using the `addType` and `getType` methods.
 * These methods can be used to check that the data passed to a property is of the correct format.
 * 
 * @property-read string $idName name of the id property
 * @property-read array $__base_data array of the base data
 * @property-read array $gettersSetters array of the getters and setters for specific properties
 * @property-read array $dataType array of the data types for properties
 * @property-read array $updated array of properties that have been updated
 * 
 * @method static void addType(string $name, ...$types) add data types for properties
 * @method mixed getValue() getter function
 * @method bool hasGetter() check if getter function is used
 * @method bool hasSetter() check if setter function is used
 * @method string getType() get the expected data type
 * @method void setValue() setter function
 */
abstract class Base
{
	protected $idName = "id";
	private $__base_data = [];
	/**
	 * custome getter settet propeties
	 * @var \classes\interfaces\Prop[]
	 */
	protected static $gettersSetters = [];
	protected static $dataType = [];
	protected $updated = [];
	private $init = false;
	public function __construct(array $opt = []) {
		if(!empty($opt)) $this->__base_data = array_merge($this->__base_data, $opt);
		$this->init = true;
	}
	public static function addType($name, ...$types)
	{
		if(isset(self::$dataType[$name]))
			self::$dataType[$name] = $types;
		else
			array_push(self::$dataType, ...$types);
	}
	private static function getType($name, string $def = null)
	{
		if(!isset(self::$dataType[$name]))
			self::$dataType[$name] = $def;
		return self::$dataType[$name];
	}
	public function __set($name, $value)
	{
		$type = gettype($value);
		if(isset(self::$gettersSetters[$name]))
		{
			$getterSetter = self::$gettersSetters[$name];
			if($getterSetter->hasSetter())
			{
				$expect = $getterSetter->getType();
				if($expect != 'mixed' && $type != $expect && !tryParse($value, $expect))
				{
					$debug = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
					throw new ErrorException(get_class($this) . "$name can't be set to Type \"$type\", expect {$expect}", 0, E_COMPILE_ERROR, $debug['file'], $debug['line']);
				}
				$getterSetter->setValue($value);
				return;
			}
		}
		$expect = self::getType($name, $type);
		if (is_string($expect))
			$expect = explode('|', $expect);
		if(!in_array($type, $expect) && !tryParse($value, $expect)){
			$expect = join("|", $expect);
			$debug = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
			throw new ErrorException(get_class($this) . "$name can't be set to Type \"$type\", expect $expect", 0, E_COMPILE_ERROR, $debug['file'], $debug['line']);
		}
		if ($this->init && $this->__base_data[$name] !== $value && !isset($this->updated[$name]))
			$this->updated[$name] = $this->__base_data[$name];
		$this->__base_data[$name] = $value;
	}
	public function __get($name)
	{
		if(isset(self::$gettersSetters[$name]))
		{
			$getterSetter = self::$gettersSetters[$name];
			if($getterSetter->hasGetter())
			{
				return $getterSetter->getValue();
			}
		}
		return $this->__base_data[$name];
	}
}