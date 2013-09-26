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
     * @var \Magento\Core\Model\CacheInterface
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
     * @param \Magento\Core\Model\CacheInterface $cache
     */
    public function __construct(
        \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider,
        \Magento\Search\Helper\Data $searchHelper,
        \Magento\Search\Model\Catalog\Layer\Filter\Price $layerFilterPrice,
        \Magento\Core\Model\CacheInterface $cache
    ) {
        $this->_engineProvider = $engineProvider;
        $this->_searchHelper = $searchHelper;
        $this->_layerFilterPrice = $layerFilterPrice;
        $this->_cache = $cache;
    }

    /**
     * Hold commit at indexation start if needed
     *
     * @param array $arguments
     * @return array
     */
    public function beforeRebuildIndex(array $arguments)
    {
        /* input parameters processing (with default values emulation) */
        if (isset($arguments[1])) {
            list(,$productIds) = $arguments;
        } else {
            $productIds = null;
        }

        if ($this->_searchHelper->isThirdPartyEngineAvailable()) {
            $engine = $this->_engineProvider->get();
            if ($engine->holdCommit() && is_null($productIds)) {
                $engine->setIndexNeedsOptimization();
            }
        }

        return $arguments;
    }

    /**
     * Apply changes in search engine index.
     * Make index optimization if documents were added to index.
     * Allow commit if it was held.
     *
     * @param \Magento\CatalogSearch\Model\Fulltext $result
     * @return \Magento\CatalogSearch\Model\Fulltext
     */
    public function afterRebuildIndex(\Magento\CatalogSearch\Model\Fulltext $result)
    {
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
