<?php
/**
 * Pluginization of \Magento\CatalogSearch\Model\Fulltext model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Plugin;

class FulltextIndexRebuild
{
    /**
     * Search helper
     *
     * @var \Magento\Search\Helper\Data
     */
    protected $_searchHelper;

    /**
     * Layer filter price
     *
     * @var \Magento\Search\Model\Layer\Category\Filter\Price
     */
    protected $_layerFilterPrice;

    /**
     * Cache
     *
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $_cache;

    /**
     * Engine provider
     *
     * @var \Magento\CatalogSearch\Model\Resource\EngineProvider
     */
    protected $_engineProvider = null;

    /**
     * @param \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider
     * @param \Magento\Search\Helper\Data $searchHelper
     * @param \Magento\Search\Model\Layer\Category\Filter\Price $layerFilterPrice
     * @param \Magento\Framework\App\CacheInterface $cache
     */
    public function __construct(
        \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider,
        \Magento\Search\Helper\Data $searchHelper,
        \Magento\Search\Model\Layer\Category\Filter\Price $layerFilterPrice,
        \Magento\Framework\App\CacheInterface $cache
    ) {
        $this->_engineProvider = $engineProvider;
        $this->_searchHelper = $searchHelper;
        $this->_layerFilterPrice = $layerFilterPrice;
        $this->_cache = $cache;
    }

    /**
     * Hold commit at indexation start if needed
     *
     * @param \Magento\CatalogSearch\Model\Fulltext $subject
     * @param int|null $storeId Store View Id
     * @param int|array|null $productIds Product Entity Id
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeRebuildIndex(
        \Magento\CatalogSearch\Model\Fulltext $subject,
        $storeId = null,
        $productIds = null
    ) {
        if ($this->_searchHelper->isThirdPartyEngineAvailable()) {
            $engine = $this->_engineProvider->get();
            if ($engine->holdCommit() && is_null($productIds)) {
                $engine->setIndexNeedsOptimization();
            }
        }
    }

    /**
     * Apply changes in search engine index.
     * Make index optimization if documents were added to index.
     * Allow commit if it was held.
     *
     * @param \Magento\CatalogSearch\Model\Fulltext $subject
     * @param \Magento\CatalogSearch\Model\Fulltext $result
     *
     * @return \Magento\CatalogSearch\Model\Fulltext
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterRebuildIndex(
        \Magento\CatalogSearch\Model\Fulltext $subject,
        \Magento\CatalogSearch\Model\Fulltext $result
    ) {
        if ($this->_searchHelper->isThirdPartyEngineAvailable()) {

            $engine = $this->_engineProvider->get();
            if ($engine->allowCommit()) {

                if ($engine->getIndexNeedsOptimization()) {
                    $engine->optimizeIndex();
                } else {
                    $engine->commitChanges();
                }

                /**
                 * Cleaning MAXPRICE cache
                 */
                $cacheTag = $this->_layerFilterPrice->getCacheTag();
                $this->_cache->clean(array($cacheTag));
            }
        }

        return $result;
    }
}
