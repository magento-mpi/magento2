<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\TargetRule\Model\Indexer\TargetRule\Plugin;

class Category extends AbstractPlugin
{
    /**
     * Invalidate target rule indexer after deleting category
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\Catalog\Model\Category
     */
    public function afterDelete(\Magento\Catalog\Model\Category $category)
    {
        $this->invalidateIndexers();
        return $category;
    }

    /**
     * Invalidate target rule indexer after changing category products
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\Catalog\Model\Category
     */
    public function afterSave(\Magento\Catalog\Model\Category $category)
    {
        $isChangedProductList = $category->getData('is_changed_product_list');
        if ($isChangedProductList) {
            $this->invalidateIndexers();
        }
        return $category;
    }
}
