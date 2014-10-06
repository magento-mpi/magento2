<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Layer\AdvancedSearch;

use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;
use Magento\CatalogSearch\Model\Layer\Advanced\CollectionFilter;
use Magento\CatalogSearch\Model\Layer\Advanced\ItemCollectionProvider as CatalogCollectionProvider;
use Magento\CatalogSearch\Model\Layer\Search\StateKey;
use Magento\Solr\Model\Layer\Search\ItemCollectionProvider as SearchCollectionProvider;

class Context extends \Magento\CatalogSearch\Model\Layer\Advanced\Context
{
    /**
     * @var \Magento\Solr\Helper\Data
     */
    protected $helper;

    /**
     * @var SearchCollectionProvider
     */
    protected $searchProvider;

    /**
     * @param CatalogCollectionProvider $collectionProvider
     * @param \Magento\CatalogSearch\Model\Layer\Search\StateKey $stateKey
     * @param CollectionFilter $collectionFilter
     * @param SearchCollectionProvider $searchProvider
     * @param \Magento\Solr\Helper\Data $helper
     */
    public function __construct(
        CatalogCollectionProvider $collectionProvider,
        StateKey $stateKey,
        CollectionFilter $collectionFilter,
        SearchCollectionProvider $searchProvider,
        \Magento\Solr\Helper\Data $helper
    ) {
        $this->helper = $helper;
        $this->searchProvider = $searchProvider;
        parent::__construct($collectionProvider, $stateKey, $collectionFilter);
    }

    /**
     * @return ItemCollectionProviderInterface
     */
    public function getCollectionProvider()
    {
        if ($this->helper->isThirdPartSearchEngine() && $this->helper->isActiveEngine()) {
            return $this->searchProvider;
        }
        return parent::getCollectionProvider();
    }
}
