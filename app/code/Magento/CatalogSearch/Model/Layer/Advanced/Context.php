<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Layer\Advanced;

class Context extends \Magento\Catalog\Model\Layer\Context
{
    /**
     * @param ItemCollectionProvider $collectionProvider
     * @param \Magento\CatalogSearch\Model\Layer\Search\StateKey $stateKey
     * @param CollectionFilter $collectionFilter
     */
    public function __construct(
        ItemCollectionProvider $collectionProvider,
        \Magento\CatalogSearch\Model\Layer\Search\StateKey $stateKey,
        CollectionFilter $collectionFilter
    ) {
        parent::__construct($collectionProvider, $stateKey, $collectionFilter);
    }
}
