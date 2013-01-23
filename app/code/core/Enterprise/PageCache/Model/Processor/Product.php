<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product view processor
 */
class Enterprise_PageCache_Model_Processor_Product extends Enterprise_PageCache_Model_Processor_Default
{
    /**
     * Key for saving product id in metadata
     */
    const METADATA_PRODUCT_ID = 'current_product_id';

    /**
     * @var Mage_Core_Model_Cache
     */
    protected $_cache;

    /**
     * @var Mage_Core_Model_Cache_ProcessorInterface
     */
    protected $_processor;

    /**
     * @param Mage_Core_Model_Cache $cache
     * @param Enterprise_PageCache_Model_Processor $processor
     */
    public function __construct(
        Mage_Core_Model_Cache $cache,
        Enterprise_PageCache_Model_Processor $processor
    ) {
        $this->_cache = $cache;
        $this->_processor = $processor;
    }

    /**
     * Prepare response body before caching
     *
     * @param Zend_Controller_Response_Http $response
     * @return string
     */
    public function prepareContent(Zend_Controller_Response_Http $response)
    {
        $countLimit = Mage::getStoreConfig(Mage_Reports_Block_Product_Viewed::XML_PATH_RECENTLY_VIEWED_COUNT);
        // save recently viewed product count limit
        $cacheId = $this->_processor->getRecentlyViewedCountCacheId();
        if (!$this->_cache->getFrontend()->test($cacheId)) {
            $this->_cache->save($countLimit, $cacheId, array(Enterprise_PageCache_Model_Processor::CACHE_TAG));
        }
        // save current product id
        $product = Mage::registry('current_product');
        if ($product) {
            $cacheId = $this->_processor->getRequestCacheId() . '_current_product_id';
            $this->_cache->save($product->getId(), $cacheId, array(Enterprise_PageCache_Model_Processor::CACHE_TAG));
            $this->_processor->setMetadata(self::METADATA_PRODUCT_ID, $product->getId());
            Enterprise_PageCache_Model_Cookie::registerViewedProducts($product->getId(), $countLimit);
        }

        return parent::prepareContent($response);
    }
}
