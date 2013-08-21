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
     * FPC cache instance
     *
     * @var Enterprise_PageCache_Model_Cache
     */
    protected $_fpcCache;

    /**
     * Cache processor
     *
     * @var Enterprise_PageCache_Model_Processor
     */
    protected $_processor;

    /**
     * @param Enterprise_PageCache_Model_Cache $fpcCache
     * @param Enterprise_PageCache_Model_Processor $processor
     */
    public function __construct(
        Enterprise_PageCache_Model_Cache $fpcCache,
        Enterprise_PageCache_Model_Processor $processor
    ) {
        $this->_fpcCache = $fpcCache;
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
        $countLimit = Mage::getStoreConfig(Magento_Reports_Block_Product_Viewed::XML_PATH_RECENTLY_VIEWED_COUNT);
        // save recently viewed product count limit
        $cacheId = $this->_processor->getRecentlyViewedCountCacheId();
        if (!$this->_fpcCache->getFrontend()->test($cacheId)) {
            $this->_fpcCache->save($countLimit, $cacheId, array(Enterprise_PageCache_Model_Processor::CACHE_TAG));
        }
        // save current product id
        $product = Mage::registry('current_product');
        if ($product) {
            $cacheId = $this->_processor->getRequestCacheId() . '_current_product_id';
            $this->_fpcCache->save($product->getId(), $cacheId, array(Enterprise_PageCache_Model_Processor::CACHE_TAG));
            $this->_processor->setMetadata(self::METADATA_PRODUCT_ID, $product->getId());
            Enterprise_PageCache_Model_Cookie::registerViewedProducts($product->getId(), $countLimit);
        }

        return parent::prepareContent($response);
    }
}
