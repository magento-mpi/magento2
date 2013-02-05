<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_ObjectManager_Config
{
    /**
     * @var array
     */
    protected $_data = array();

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_data = $data;
    }

    /**
     * @param array $data
     */
    public function extend(array $data)
    {
        $this->_data = array_replace_recursive($this->_data, $data);
    }

    /**
     * Resolve class name, taking into account type preferences
     *
     * @param string $className
     * @return string
     */
    public function resolveClassName($className)
    {
        if (isset($this->_data['preferences'][$className])) {
            return $this->getTypePreference($className);
        }
        return $className;
    }

    /**
     * Check whether type preferences were configured for the object
     *
     * @param string $className
     * @return bool
     */
    public function hasTypePreference($className)
    {
        return isset($this->_data['preferences'][$className]);
    }

    /**
     * Retrieve type preference by className
     *
     * @param string $className
     * @return string
     * @throws LogicException
     */
    public function getTypePreference($className)
    {
        $preferencePath = array();
        while (isset($this->_data['preferences'][$className])) {
            if (isset($preferencePath[$this->_data['preferences'][$className]])) {
                throw new LogicException(
                    'Circular type preference: ' . $className . ' relates to '
                        . $this->_data['preferences'][$className] . ' and viceversa.'
                );
            }
            $className = $this->_data['preferences'][$className];
            $preferencePath[$className] = 1;
        }
        return $className;
    }

    /**
     * @param string $className
     * @return bool
     */
    public function isShared($className)
    {
        return !(isset($this->_data[$className]['shared']) && $this->_data[$className]['shared'] == false);
    }

    /**
     * @param string $className
     * @return array
     */
    public function getArguments($className)
    {
        return isset($this->_data[$className]['parameters']) ? $this->_data[$className]['parameters'] : array();
    }
}
