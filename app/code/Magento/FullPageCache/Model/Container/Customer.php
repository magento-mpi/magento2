<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Container for cache lifetime equated with session lifetime
 */
class Magento_FullPageCache_Model_Container_Customer extends Magento_FullPageCache_Model_Container_Abstract
{
    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_FullPageCache_Model_Cache $fpcCache
     * @param Magento_FullPageCache_Model_Container_Placeholder $placeholder
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_FullPageCache_Helper_Url $urlHelper
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Layout $layout
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_FullPageCache_Model_Cache $fpcCache,
        Magento_FullPageCache_Model_Container_Placeholder $placeholder,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_FullPageCache_Helper_Url $urlHelper,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Layout $layout,
        Magento_Core_Model_Config $coreConfig
    ) {
        parent::__construct(
            $eventManager, $fpcCache, $placeholder, $coreRegistry, $urlHelper, $coreStoreConfig, $layout
        );
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Save data to cache storage and set cache lifetime equal with default cookie lifetime
     *
     * @param string $data
     * @param string $id
     * @param array $tags
     */
    protected function _saveCache($data, $id, $tags = array(), $lifetime = null)
    {
        $lifetime = $this->_coreConfig->getValue(Magento_Core_Model_Cookie::XML_PATH_COOKIE_LIFETIME, 'default');
        return parent::_saveCache($data, $id, $tags, $lifetime);
    }
}
