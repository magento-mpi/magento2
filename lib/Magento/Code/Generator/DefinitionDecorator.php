<?php
/**
 * Object manager definition decorator. Generates all proxies and factories declared
 * in class constructor signatures before reading it's definition
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Code_Generator_DefinitionDecorator implements Magento_ObjectManager_Definition
{
    /**
     * Processed classes list
     *
     * @var array
     */
    protected $_processedClasses = array();

    /**
     * Class generator
     *
     * @var Magento_Code_Generator_Class
     */
    protected $_generator;

    /**
     * Decorated objectManager definition
     *
     * @var Magento_ObjectManager_Definition
     */
    protected $_decoratedDefinition;

    /**
     * @param Magento_ObjectManager_Definition $definition
     * @param Magento_Code_Generator_Class $generator
     */
    public function __construct(
        Magento_ObjectManager_Definition $definition, Magento_Code_Generator_Class $generator = null
    ) {
        $this->_decoratedDefinition = $definition;
        $this->_generator = $generator ?: new Magento_Code_Generator_Class();
    }

    /**
     * Get list of method parameters
     *
     * Retrieve an ordered list of constructor parameters.
     * Each value is an array with following entries:
     *
     * array(
     *     0, // string: Parameter name
     *     1, // string|null: Parameter type
     *     2, // bool: whether this param is required
     *     3, // mixed: default value
     * );
     *
     * @param string $className
     * @return array|null
     */
    public function getParameters($className)
    {
        if (!array_key_exists($className, $this->_processedClasses)) {
            $this->_generator->generateForConstructor($className);
            $this->_processedClasses[$className] = 1;
        }
        return $this->_decoratedDefinition->getParameters($className);
    }

    /**
     * Retrieve list of all classes covered with definitions
     *
     * @return array
     */
    public function getClasses()
    {
        return $this->_decoratedDefinition->getClasses();
    }
}
