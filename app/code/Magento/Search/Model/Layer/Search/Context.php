<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Layer\Search;

use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;
use Magento\Catalog\Model\Layer\Search\CollectionFilter;
use Magento\Catalog\Model\Layer\Search\ItemCollectionProvider as CatalogCollectionProvider;
use Magento\Catalog\Model\Layer\Search\StateKey;

class Context extends \Magento\Catalog\Model\Layer\Search\Context
{
    /**
     * @var \Magento\Search\Helper\Data
     */
    protected $helper;

    /**
     * @var ItemCollectionProvider
     */
    protected $searchProvider;

    /**
     * @param CatalogCollectionProvider $collectionProvider
     * @param StateKey $stateKey
     * @param CollectionFilter $collectionFilter
     * @param ItemCollectionProvider $searchProvider
     * @param \Magento\Search\Helper\Data $helper
     */
    public function __construct(
        CatalogCollectionProvider $collectionProvider,
        StateKey $stateKey,
        CollectionFilter $collectionFilter,
        ItemCollectionProvider $searchProvider,
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
