<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Resource;
use Magento\Framework\Object;

class Search extends \Magento\Catalog\Model\Layer
{
    /**
     * @param Search\Context $context
     * @param \Magento\Catalog\Model\Layer\StateFactory $layerStateFactory
     * @param Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param Resource\Product $catalogProduct
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param CategoryRepositoryInterface $categoryRepository
     * @param array $data
     */
    public function __construct(
        Search\Context $context,
        StateFactory $layerStateFactory,
        Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        Resource\Product $catalogProduct,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $layerStateFactory,
            $attributeCollectionFactory,
            $catalogProduct,
            $storeManager,
            $registry,
            $categoryRepository,
            $data
        );
    }
}
