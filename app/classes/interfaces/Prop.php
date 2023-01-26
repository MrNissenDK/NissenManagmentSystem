<?php
namespace classes\interfaces;
interface Prop
{
	/**
	 * is getValue Function used
	 * @return bool
	 */
	public function hasGetter();
	/**
	 * is setValue Function used
	 * @return bool
	 */
	public function hasSetter();
	/**
	 * expected type sepperated with `|`
	 * @return string
	 */
	public function getType();
	public function getValue();
	public function setValue($value);
}