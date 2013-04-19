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
	protected $className;
	protected $methods;

	/**
	 * Constructor
	 *
	 * @param string name of the service
	 * @param string version of this service
	 * @param string class name associated to this service
	 */
	public function __construct ($name, $version, $className)
	{
		$this->name = $name;
		$this->version = $version;
		$this->className = $className;
		$this->methods = array();
	}

	/**
	 * Create an instance of the class associated to this service
	 *
	 * @return mixed
	 */
	public function instantiate ()
	{
		return new $this->className();
	}

    /**
     * Add a new method into an existing Service
     *
     * @param string methodName name of the method to add
     * @param array  permissions list of permissions needed to execute this method
     * @param string inputSchema location of the XSD file describing the input needed for this method
     * @param string inputElement name of the XML element in the XSD representing the root of the data input structure
     * @param string outputSchema location of the XSD file describing the output from this method
     * @param string outputElement name of the XML element in the XSD representing the root of the data output structure
     * @return Mage_Core_Service_Registry_MethodMetadata
     */
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

    /**
     * Lookup metadata of a given method
     *
     * @param string methodName name of the method to lookup
     * @throws InvalidArgumentException if the method do not exist
     */
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

	public function getClassName ()
	{
		return $this->className;
	}
}
