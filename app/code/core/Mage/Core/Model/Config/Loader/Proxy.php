<?php
/**
 *
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Config_Loader_Proxy implements Mage_Core_Model_Config_LoaderInterface
{
    /**
     * @var Mage_Core_Model_Config_Loader
     */
    protected $_loader;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get loader instance
     *
     * @return Mage_Core_Model_Config_Loader
     */
    protected function _getLoader()
    {
        if (null === $this->_loader) {
            $this->_loader = $this->_objectManager->get('Mage_Core_Model_Config_Loader');
        }
        return $this->_loader;
    }

    /**
     * Populate configuration object
     *
     * @param Mage_Core_Model_Config_Base $config
     */
    public function load(Mage_Core_Model_Config_Base $config)
    {
        $this->_getLoader()->load($config);
    }
}
