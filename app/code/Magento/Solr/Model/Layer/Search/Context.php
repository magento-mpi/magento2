<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Layer\Search;

use Magento\Catalog\Model\Layer\CollectionFilterInterface;
use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;
use Magento\Catalog\Model\Layer\StateKeyInterface;
use Magento\Solr\Helper\Data;

class Context extends \Magento\Catalog\Model\Layer\Context
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ItemCollectionProvider
     */
    protected $searchProvider;

    /**
     * @param ItemCollectionProviderInterface $collectionProvider
     * @param StateKeyInterface $stateKey
     * @param CollectionFilterInterface $collectionFilter
     * @param ItemCollectionProvider $searchProvider
     * @param Data $helper
     */
    public function __construct(
        ItemCollectionProviderInterface $collectionProvider,
        StateKeyInterface $stateKey,
        CollectionFilterInterface $collectionFilter,
        ItemCollectionProvider $searchProvider,
        Data $helper
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
