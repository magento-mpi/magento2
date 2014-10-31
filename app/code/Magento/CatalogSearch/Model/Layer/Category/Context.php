<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Layer\Category;

class Context extends \Magento\Catalog\Model\Layer\Context
{
    /**
     * @param ItemCollectionProvider $collectionProvider
     * @param \Magento\Catalog\Model\Layer\Category\StateKey $stateKey
     * @param \Magento\Catalog\Model\Layer\Category\CollectionFilter $collectionFilter
     */
    public function __construct(
        ItemCollectionProvider $collectionProvider,
        \Magento\Catalog\Model\Layer\Category\StateKey $stateKey,
        \Magento\Catalog\Model\Layer\Category\CollectionFilter $collectionFilter
    ) {
        parent::__construct($collectionProvider, $stateKey, $collectionFilter);
    }
}
