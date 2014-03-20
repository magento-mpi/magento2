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
        \Magento\Catalog\Model\Layer\Search\ItemCollectionProvider $collectionProvider,
        \Magento\Catalog\Model\Layer\Search\StateKey $stateKey,
        \Magento\Catalog\Model\Layer\Search\CollectionFilter $collectionFilter
    ) {
        parent::__construct($collectionProvider, $stateKey, $collectionFilter);
    }
}
