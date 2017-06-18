<?php
namespace Shinoa\StudentsList;


use Shinoa\StudentsList\Exceptions\DIContainerException;

/**
 * Use DIContainer to define factories and get only those data and objects, that you need at the time.
 * @package Shinoa\StudentsList
 */
class DIContainer
{
	/**
	 * Contains factories, used to create certain data or object.
	 * @var array
	 */
	protected $services = array();
	
	/**
	 * Contains created objects, that need not to be recreated
	 * @var array
	 */
	protected $objects = array();
	
	/**
	 * Memorizes callable factory with given name.
	 *
	 * @param string $name
	 * @param callable $factory
	 * @throws DIContainerException
	 */
	public function register(string $name, callable $factory)
	{
		if ( !array_key_exists($name, $this->services) ) {
			$this->services[$name] = $factory;
		} else {
			throw new DIContainerException('Service name already occupied');
		}
	}
	
	/**
	 * Returns data by name, if there is a defined factory to produce it.
	 *
	 * @param string $name
	 * @return mixed
	 * @throws DIContainerException
	 */
	public function get(string $name)
	{
		if ( array_key_exists($name, $this->objects) ) {
			$result = $this->objects[$name];
		} else {
			if (array_key_exists($name, $this->services)) {
				$result = $this->services[$name]($this);
			} else {
				throw new DIContainerException('Trying to call nonexistant service');
			}
		}
		return $result;
	}
}