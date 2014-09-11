<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Layer;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Resource;
use Magento\Framework\Object;

class Advanced extends \Magento\Catalog\Model\Layer
{
    /**
     * @param Advanced\Context $context
     * @param \Magento\Catalog\Model\Layer\StateFactory $layerStateFactory
     * @param CategoryFactory $categoryFactory
     * @param Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param Resource\Product $catalogProduct
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        Advanced\Context $context,
        \Magento\Catalog\Model\Layer\StateFactory $layerStateFactory,
        CategoryFactory $categoryFactory,
        Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        Resource\Product $catalogProduct,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $layerStateFactory,
            $categoryFactory,
            $attributeCollectionFactory,
            $catalogProduct,
            $storeManager,
            $registry,
            $data
        );
    }
}
