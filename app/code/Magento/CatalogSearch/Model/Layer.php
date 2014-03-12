<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Model;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Layer as ModelLayer;
use Magento\Catalog\Model\Resource;
use Magento\Object;

class Layer extends ModelLayer
{

    public function __construct(
        \Magento\Catalog\Model\Layer\StateFactory $layerStateFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Catalog\Model\Resource\Product $catalogProduct,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Registry $coreRegistry,
        ModelLayer\Search\ItemCollectionProvider $collectionProvider,
        ModelLayer\Search\StateKey $stateKey,
        ModelLayer\Search\CollectionFilter $collectionFilter,
        array $data = array()
    ) {
        ModelLayer::__construct(
            $layerStateFactory,
            $categoryFactory,
            $attributeCollectionFactory,
            $catalogProduct,
            $storeManager,
            $coreRegistry,
            $collectionProvider,
            $stateKey,
            $collectionFilter,
            $data
        );
    }
}
