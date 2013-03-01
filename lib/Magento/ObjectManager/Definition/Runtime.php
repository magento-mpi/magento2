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
     * Definition reader
     *
     * @var Magento_Di_Definition_RuntimeDefinition
     */
    protected $_reader;

    /**
     * @param Magento_Di_Definition_RuntimeDefinition $reader
     */
    public function __construct(Magento_Di_Definition_RuntimeDefinition $reader = null)
    {
        $this->_reader = $reader ?: new Magento_Di_Definition_RuntimeDefinition_Zend();
        $this->_generator = new Magento_Di_Generator();
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
     * @return array
     */
    public function getParameters($className)
    {
        if (!class_exists($className)) {
            $this->_generator->generateClass($className);
        }
        return $this->_reader->getMethodParameters($className, '__construct');
    }
}
