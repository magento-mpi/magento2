<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer\Search;

use Magento\CatalogSearch\Model\Layer\Search\CollectionFilter;
use Magento\CatalogSearch\Model\Layer\Search\StateKey;

class Context extends \Magento\Catalog\Model\Layer\Context
{
    /**
     * @param ItemCollectionProvider $collectionProvider
     * @param StateKey $stateKey
     * @param CollectionFilter $collectionFilter
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Search\ItemCollectionProvider $collectionProvider,
        \Magento\CatalogSearch\Model\Layer\Search\StateKey $stateKey,
        \Magento\CatalogSearch\Model\Layer\Search\CollectionFilter $collectionFilter
    ) {
        parent::__construct($collectionProvider, $stateKey, $collectionFilter);
    }
}
