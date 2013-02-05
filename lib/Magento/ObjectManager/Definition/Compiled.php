<?php
/**
 * Compiled class definitions. Shoudl be used for maximum performance in production.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link
 */
class Magento_ObjectManager_Definition_Compiled implements Magento_ObjectManager_Definition
{
    /**
     * Class definitions
     *
     * @var array
     */
    protected $_definitions;

    /**
     * @param array $definitions
     */
    public function __construct(array $definitions)
    {
        list($this->_signatures, $this->_definitions) = $definitions;
    }

    /**
     * Check whether method has parameters
     *
     * @param string $className
     * @return bool
     */
    public function hasParameters($className)
    {
        return $this->_definitions[$className] !== null;
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
        $definition = $this->_definitions[$className];
        if ($definition !== null) {
            if (is_string($this->_signatures[$definition])) {
                $this->_signatures[$definition] = unserialize($this->_signatures[$definition]);
            }
            return $this->_signatures[$definition];
        }
        return null;
    }
}
