<?php

/**
 * Method Metadata
 *
 * This class contain metadata of a method
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Service_Registry_MethodMetadata extends Varien_Object
{
	protected $name;
	protected $permissions;
	protected $inputSchema;
	protected $inputElement;
	protected $outputSchema;
	protected $outputElement;

	public function __construct ($name, $permissions, $inputSchema, $inputElement, $outputSchema, $outputElement)
	{
		$this->name = $name;
		$this->permissions = $permissions;
		$this->inputSchema = $inputSchema;
		$this->inputElement = $inputElement;
		$this->outputSchema = $outputSchema;
		$this->outputElement = $outputElement;
	}

	public function getName ()
	{
		return $this->name;
	}

	public function getPermissions ()
	{
		return $this->permissions;
	}

	public function getInputSchema ()
	{
		return $this->inputSchema;
	}

	public function getInputElement ()
	{
		return $this->inputElement;
	}

	public function getOutputSchema ()
	{
		return $this->outputSchema;
	}

	public function getOutputElement ()
	{
		return $this->outputElement;
	}
}