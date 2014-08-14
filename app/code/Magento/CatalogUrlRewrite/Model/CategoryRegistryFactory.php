<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model;

use Magento\Catalog\Model\Product;
use Magento\Framework\ObjectManager;

class CategoryRegistryFactory
{
    /** @var \Magento\Framework\ObjectManager */
    protected $objectManager;

    protected $categoryRegistryClassName = 'Magento\CatalogUrlRewrite\Model\CategoryRegistry';
    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param Product $product
     * @return \Magento\CatalogUrlRewrite\Model\CategoryRegistry
     */
    public function create(Product $product)
    {
        $categoriesCollection = $product->getCategoryCollection()
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('url_path');
        $categories = [];
        foreach ($categoriesCollection as $category) {
            $categories[$category->getId()] = $category;
        }
        return $this->objectManager->create($this->categoryRegistryClassName, ['categories' => $categories]);
    }
}
