<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory class for cache control interface
 */
class Magento_PageCache_Model_CacheControlFactory
{
    /**
     * Path to external cache controls
     */
    const XML_PATH_EXTERNAL_CACHE_CONTROLS = 'global/external_cache/controls';

    /**
     * Paths to external cache config option
     */
    const XML_PATH_EXTERNAL_CACHE_CONTROL  = 'system/external_page_cache/control';

    /**
     * Object manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Config
     *
     * @var Magento_Centinel_Model_Config
     */
    protected $_config;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_ConfigInterface $config
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Core_Model_ConfigInterface $config,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_objectManager = $objectManager;
        $this->_config = $config;
        $this->_coreStoreConfig = $coreStoreConfig;
    }

    /**
     * Return all available external cache controls
     *
     * @return array
     */
    public function getCacheControls()
    {
        $controls = $this->_config->getNode(self::XML_PATH_EXTERNAL_CACHE_CONTROLS);
        return $controls->asCanonicalArray();
    }

    /**
     * Initialize proper external cache control model
     *
     * @throws Magento_Core_Exception
     * @return Magento_PageCache_Model_Control_Interface
     */
    public function getCacheControlInstance()
    {
        $usedControl = $this->_coreStoreConfig->getConfig(self::XML_PATH_EXTERNAL_CACHE_CONTROL);
        if ($usedControl) {
            foreach ($this->getCacheControls() as $control => $info) {
                if ($control == $usedControl && !empty($info['class'])) {
                    return $this->_objectManager->get($info['class']);
                }
            }
        }
        throw new Magento_Core_Exception(__('Failed to load external cache control'));
    }
}
