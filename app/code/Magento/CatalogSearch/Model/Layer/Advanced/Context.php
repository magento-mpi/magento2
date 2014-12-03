<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Layer\Advanced;

use \Magento\CatalogSearch\Model\Layer\Advanced\ItemCollectionProvider;
use \Magento\CatalogSearch\Model\Layer\Search\StateKey;
use \Magento\CatalogSearch\Model\Layer\Advanced\CollectionFilter;

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
        parent::__construct($collectionProvider, $stateKey, $collectionFilter);
    }
}