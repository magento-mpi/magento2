<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Resource;
use Magento\Object;

class Search extends \Magento\Catalog\Model\Layer
{
    /**
     * @param Search\Context $context
     * @param StateFactory $layerStateFactory
     * @param CategoryFactory $categoryFactory
     * @param Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param Resource\Product $catalogProduct
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Search\Context $context,
        \Magento\Catalog\Model\Layer\StateFactory $layerStateFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Catalog\Model\Resource\Product $catalogProduct,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        parent::__construct($context, $layerStateFactory, $categoryFactory, $attributeCollectionFactory,
            $catalogProduct, $storeManager, $registry, $data
        );
    }
} 
