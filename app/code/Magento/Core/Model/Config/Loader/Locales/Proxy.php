<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Config_Loader_Locales_Proxy implements Magento_Core_Model_Config_LoaderInterface
{
    /**
     * @var Magento_Core_Model_Config_Loader_Locales
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
     * @return Magento_Core_Model_Config_Loader_Locales
     */
    protected function _getLoader()
    {
        if (null === $this->_loader) {
            $this->_loader = $this->_objectManager->get('Magento_Core_Model_Config_Loader_Locales');
        }
        return $this->_loader;
    }

    /**
     * Populate configuration object
     *
     * @param Magento_Core_Model_Config_Base $config
     * @param bool $useCache
     * @return void
     */
    public function load(Magento_Core_Model_Config_Base $config, $useCache = true)
    {
        $this->_getLoader()->load($config, $useCache);
    }
}
