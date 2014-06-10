<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Layer\AdvancedSearch;

use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;
use Magento\Catalog\Model\Layer\AdvancedSearch\CollectionFilter;
use Magento\Catalog\Model\Layer\AdvancedSearch\ItemCollectionProvider as CatalogCollectionProvider;
use Magento\Search\Model\Layer\Search\ItemCollectionProvider as SearchCollectionProvider;
use Magento\Catalog\Model\Layer\Search\StateKey;

class Context extends \Magento\Catalog\Model\Layer\AdvancedSearch\Context
{
    /**
     * @var \Magento\Search\Helper\Data
     */
    protected $helper;

    /**
     * @var SearchCollectionProvider
     */
    protected $searchProvider;

    /**
     * @param CatalogCollectionProvider $collectionProvider
     * @param StateKey $stateKey
     * @param CollectionFilter $collectionFilter
     * @param SearchCollectionProvider $searchProvider
     * @param \Magento\Search\Helper\Data $helper
     */
    public function __construct(
        CatalogCollectionProvider $collectionProvider,
        StateKey $stateKey,
        CollectionFilter $collectionFilter,
        SearchCollectionProvider $searchProvider,
        \Magento\Search\Helper\Data $helper
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
