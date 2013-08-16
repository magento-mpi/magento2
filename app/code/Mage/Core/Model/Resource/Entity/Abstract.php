<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

abstract class Mage_Core_Model_Resource_Entity_Abstract
{
    protected $_name = null;
    /**
     * Configuration object
     *
     * @var Magento_Simplexml_Config
     */
    protected $_config = array();

    /**
     * Set config
     *
     * @param Magento_Simplexml_Config $config
     */
    public function __construct($config)
    {
        $this->_config = $config;
    }

    /**
     * Get config by key
     *
     * @param string $key
     * @return string|boolean
     */
    public function getConfig($key = '')
    {
        if (''===$key) {
            return $this->_config;
        } elseif (isset($this->_config->$key)) {
            return $this->_config->$key;
        } else {
            return false;
        }
    }
}
