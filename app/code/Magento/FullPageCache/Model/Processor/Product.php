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
    protected $_fpcProcessor;

    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @param \Magento\FullPageCache\Model\Processor $fpcProcessor
     * @param \Magento\Core\Model\Session $coreSession
     * @param \Magento\App\State $appState
     * @param \Magento\FullPageCache\Model\Container\PlaceholderFactory $placeholderFactory
     * @param \Magento\FullPageCache\Model\ContainerFactory $containerFactory
     * @param \Magento\FullPageCache\Model\Cache $fpcCache
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     */
    public function __construct(
        \Magento\FullPageCache\Model\Processor $fpcProcessor,
        \Magento\Core\Model\Session $coreSession,
        \Magento\App\State $appState,
        \Magento\FullPageCache\Model\Container\PlaceholderFactory $placeholderFactory,
        \Magento\FullPageCache\Model\ContainerFactory $containerFactory,
        \Magento\FullPageCache\Model\Cache $fpcCache,
        \Magento\Registry $coreRegistry,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
    ) {
        parent::__construct($fpcProcessor, $coreSession, $appState, $placeholderFactory, $containerFactory);
        $this->_fpcCache = $fpcCache;
        $this->_coreRegistry = $coreRegistry;
        $this->_storeConfig = $coreStoreConfig;
    }

    /**
     * Prepare response body before caching
     *
     * @param \Magento\App\ResponseInterface $response
     * @return string
     */
    public function prepareContent(\Magento\App\ResponseInterface $response)
    {
        $countLimit = $this->_storeConfig->getValue(
            \Magento\Reports\Block\Product\Viewed::XML_PATH_RECENTLY_VIEWED_COUNT
        , \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        // save recently viewed product count limit
        $cacheId = $this->_fpcProcessor->getRecentlyViewedCountCacheId();
        if (!$this->_fpcCache->getFrontend()->test($cacheId)) {
            $this->_fpcCache->save($countLimit, $cacheId, array(\Magento\FullPageCache\Model\Processor::CACHE_TAG));
        }
        // save current product id
        $product = $this->_coreRegistry->registry('current_product');
        if ($product) {
            $cacheId = $this->_fpcProcessor->getRequestCacheId() . '_current_product_id';
            $this->_fpcCache->save(
                $product->getId(), $cacheId, array(\Magento\FullPageCache\Model\Processor::CACHE_TAG)
            );
            $this->_fpcProcessor->setMetadata(self::METADATA_PRODUCT_ID, $product->getId());
            \Magento\FullPageCache\Model\Cookie::registerViewedProducts($product->getId(), $countLimit);
        }

        return parent::prepareContent($response);
    }
}
