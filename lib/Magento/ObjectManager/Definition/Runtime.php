<?php
/**
 * Runtime class definitions. Reflection is used to parse constructor signatures. Should be used only in dev mode.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_ObjectManager_Definition_Runtime implements Magento_ObjectManager_Definition
{
    /**
     * @var array
     */
    protected $_definitions = array();

    /**
     * @param Magento_Code_Reader_ClassReader $reader
     */
    public function __construct(Magento_Code_Reader_ClassReader $reader = null)
    {
        $this->_reader = $reader ?: new Magento_Code_Reader_ClassReader();
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
        if (!array_key_exists($className, $this->_definitions)) {
            $this->_definitions[$className] = $this->_reader->getConstructor($className);
        }
        return $this->_definitions[$className];
    }

    /**
     * Retrieve list of all classes covered with definitions
     *
     * @return array
     */
    public function getClasses()
    {
        return array();
    }
}
