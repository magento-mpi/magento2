<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Solr\Model\Layer\AdvancedSearch;

use Magento\Catalog\Model\Layer\CollectionFilterInterface;
use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;
use Magento\Catalog\Model\Layer\StateKeyInterface;
use Magento\Solr\Model\Layer\Search\ItemCollectionProvider as SearchCollectionProvider;

class Context extends \Magento\Catalog\Model\Layer\Context
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
     * @param ItemCollectionProviderInterface $collectionProvider
     * @param StateKeyInterface $stateKey
     * @param CollectionFilterInterface $collectionFilter
     * @param SearchCollectionProvider $searchProvider
     * @param \Magento\Solr\Helper\Data $helper
     */
    public function __construct(
        ItemCollectionProviderInterface $collectionProvider,
        StateKeyInterface $stateKey,
        CollectionFilterInterface $collectionFilter,
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
