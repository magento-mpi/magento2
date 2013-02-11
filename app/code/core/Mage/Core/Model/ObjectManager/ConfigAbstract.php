<?php
/**
 * Abstract object manager initializer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Mage_Core_Model_ObjectManager_ConfigAbstract implements Magento_ObjectManager_Configuration
{
    /**
     * Runtime configuration params
     *
     * @var array
     */
    protected $_params = array();

    /**
     * @param array $params
     */
    public function __construct($params)
    {
        $this->_params = $params;
    }

    /**
     * Get init param
     *
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    protected function _getParam($name, $defaultValue = null)
    {
        return isset($this->_params[$name]) ? $this->_params[$name] : $defaultValue;
    }
}
