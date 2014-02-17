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
     * @var \Magento\Search\Helper\Data
     */
    protected $_searchHelper;

    /**
     * @var \Magento\Search\Model\Catalog\Layer\Filter\Price
     */
    protected $_layerFilterPrice;

    /**
     * @var \Magento\App\CacheInterface
     */
    protected $_cache;

    /**
     * @var \Magento\CatalogSearch\Model\Resource\EngineProvider
     */
    protected $_engineProvider = null;

    /**
     * @param \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider
     * @param \Magento\Search\Helper\Data $searchHelper
     * @param \Magento\Search\Model\Catalog\Layer\Filter\Price $layerFilterPrice
     * @param \Magento\App\CacheInterface $cache
     */
    public function __construct(
        \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider,
        \Magento\Search\Helper\Data $searchHelper,
        \Magento\Search\Model\Catalog\Layer\Filter\Price $layerFilterPrice,
        \Magento\App\CacheInterface $cache
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

     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeRebuildIndex(
        \Magento\CatalogSearch\Model\Fulltext $subject, $storeId = null, $productIds = null
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
