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
namespace Magento\FullPageCache\Model\Processor;

class Product extends \Magento\FullPageCache\Model\Processor\DefaultProcessor
{
    /**
     * Key for saving product id in metadata
     */
    const METADATA_PRODUCT_ID = 'current_product_id';

    /**
     * FPC cache instance
     *
     * @var \Magento\FullPageCache\Model\Cache
     */
    protected $_fpcCache;

    /**
     * Cache processor
     *
     * @var \Magento\FullPageCache\Model\Processor
     */
    protected $_processor;

    /**
     * @param \Magento\FullPageCache\Model\Cache $fpcCache
     * @param \Magento\FullPageCache\Model\Processor $processor
     */
    public function __construct(
        \Magento\FullPageCache\Model\Cache $fpcCache,
        \Magento\FullPageCache\Model\Processor $processor
    ) {
        $this->_fpcCache = $fpcCache;
        $this->_processor = $processor;
    }

    /**
     * Prepare response body before caching
     *
     * @param \Zend_Controller_Response_Http $response
     * @return string
     */
    public function prepareContent(\Zend_Controller_Response_Http $response)
    {
        $countLimit = \Mage::getStoreConfig(\Magento\Reports\Block\Product\Viewed::XML_PATH_RECENTLY_VIEWED_COUNT);
        // save recently viewed product count limit
        $cacheId = $this->_processor->getRecentlyViewedCountCacheId();
        if (!$this->_fpcCache->getFrontend()->test($cacheId)) {
            $this->_fpcCache->save($countLimit, $cacheId, array(\Magento\FullPageCache\Model\Processor::CACHE_TAG));
        }
        // save current product id
        $product = \Mage::registry('current_product');
        if ($product) {
            $cacheId = $this->_processor->getRequestCacheId() . '_current_product_id';
            $this->_fpcCache->save($product->getId(), $cacheId, array(\Magento\FullPageCache\Model\Processor::CACHE_TAG));
            $this->_processor->setMetadata(self::METADATA_PRODUCT_ID, $product->getId());
            \Magento\FullPageCache\Model\Cookie::registerViewedProducts($product->getId(), $countLimit);
        }

        return parent::prepareContent($response);
    }
}
