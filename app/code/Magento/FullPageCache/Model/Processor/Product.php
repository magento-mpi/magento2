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
 * Catalog product view processor
 */
class Magento_FullPageCache_Model_Processor_Product extends Magento_FullPageCache_Model_Processor_Default
{
    /**
     * Key for saving product id in metadata
     */
    const METADATA_PRODUCT_ID = 'current_product_id';

    /**
     * FPC cache instance
     *
     * @var Magento_FullPageCache_Model_Cache
     */
    protected $_fpcCache;

    /**
     * Cache processor
     *
     * @var Magento_FullPageCache_Model_Processor
     */
    protected $_fpcProcessor;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @param Magento_FullPageCache_Model_Processor $fpcProcessor
     * @param Magento_Core_Model_Session $coreSession
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_FullPageCache_Model_Container_PlaceholderFactory $placeholderFactory
     * @param Magento_FullPageCache_Model_ContainerFactory $containerFactory
     * @param Magento_FullPageCache_Model_Cache $fpcCache
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_FullPageCache_Model_Processor $fpcProcessor,
        Magento_Core_Model_Session $coreSession,
        Magento_Core_Model_App_State $appState,
        Magento_FullPageCache_Model_Container_PlaceholderFactory $placeholderFactory,
        Magento_FullPageCache_Model_ContainerFactory $containerFactory,
        Magento_FullPageCache_Model_Cache $fpcCache,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        parent::__construct($fpcProcessor, $coreSession, $appState, $placeholderFactory, $containerFactory);
        $this->_fpcCache = $fpcCache;
        $this->_coreRegistry = $coreRegistry;
        $this->_coreStoreConfig = $coreStoreConfig;
    }

    /**
     * Prepare response body before caching
     *
     * @param Zend_Controller_Response_Http $response
     * @return string
     */
    public function prepareContent(Zend_Controller_Response_Http $response)
    {
        $countLimit = $this->_coreStoreConfig->getConfig(
            Magento_Reports_Block_Product_Viewed::XML_PATH_RECENTLY_VIEWED_COUNT
        );
        // save recently viewed product count limit
        $cacheId = $this->_fpcProcessor->getRecentlyViewedCountCacheId();
        if (!$this->_fpcCache->getFrontend()->test($cacheId)) {
            $this->_fpcCache->save($countLimit, $cacheId, array(Magento_FullPageCache_Model_Processor::CACHE_TAG));
        }
        // save current product id
        $product = $this->_coreRegistry->registry('current_product');
        if ($product) {
            $cacheId = $this->_fpcProcessor->getRequestCacheId() . '_current_product_id';
            $this->_fpcCache->save(
                $product->getId(), $cacheId, array(Magento_FullPageCache_Model_Processor::CACHE_TAG)
            );
            $this->_fpcProcessor->setMetadata(self::METADATA_PRODUCT_ID, $product->getId());
            Magento_FullPageCache_Model_Cookie::registerViewedProducts($product->getId(), $countLimit);
        }

        return parent::prepareContent($response);
    }
}
