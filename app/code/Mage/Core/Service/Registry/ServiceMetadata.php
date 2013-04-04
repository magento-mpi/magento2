<?php

/**
 * Service Metadata
 *
 * This class contain metadata of a Service
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Service_Registry_ServiceMetadata extends Varien_Object
{
	protected $name;
	protected $version;
	protected $methods;

	public function __construct ($name, $version)
	{
		$this->name = $name;
		$this->version = $version;
		$this->methods = array();
	}

	public function addMethod ($methodName, $permissions, $inputSchema, $inputElement, $outputSchema, $outputElement)
	{
        try {
            $methodMetadata = $this->getMethod($methodName);
        }
        catch (InvalidArgumentException $e) {
            $methodMetadata = new Mage_Core_Service_Registry_MethodMetadata($methodName, $permissions, $inputSchema, $inputElement, $outputSchema, $outputElement);
			$this->methods[$methodName] = $methodMetadata;
        }

        return $methodMetadata;
	}

	public function getMethod ($methodName)
	{
        if (isset($this->methods[$methodName])) {
	        return $this->methods[$methodName];
        }

		throw new InvalidArgumentException(sprintf('Method "%s" was not found in Service "%s".', $methodName, $this->name));
	}

	public function getName ()
	{
		return $this->name;
	}

	public function getVersion ()
	{
		return $this->version;
	}
}
