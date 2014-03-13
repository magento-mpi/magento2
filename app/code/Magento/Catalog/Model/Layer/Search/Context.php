<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer\Search;

class Context extends \Magento\Catalog\Model\Layer\Context
{
    /**
     * @param ItemCollectionProvider $collectionProvider
     * @param StateKey $stateKey
     * @param CollectionFilter $collectionFilter
     */
    public function __construct(
        ItemCollectionProvider $collectionProvider,
        StateKey $stateKey,
        CollectionFilter $collectionFilter
    ) {
        $this->collectionProvider = $collectionProvider;
        $this->stateKey = $stateKey;
        $this->collectionFilter = $collectionFilter;
    }
}
