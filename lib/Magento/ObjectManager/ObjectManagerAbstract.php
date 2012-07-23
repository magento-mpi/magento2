<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ObjectManager
 * @copyright   {copyright}
 * @license     {license_link}
 */

abstract class Magento_ObjectManager_ObjectManagerAbstract implements Magento_ObjectManager
{
    /**
     * Application config
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    public function __construct($config)
    {
        $this->_config = $config;
    }
}
