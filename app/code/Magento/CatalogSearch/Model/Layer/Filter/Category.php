<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Layer\Filter;

/**
 * Layer category filter
 */
class Category extends \Magento\Catalog\Model\Layer\Filter\Category
{
    /**
     * Apply category filter to product collection
     *
     * @param   \Magento\Framework\App\RequestInterface $request
     * @return  $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $attributeValue = $request->getParam($this->_requestVar) ?: $request->getParam('id');
        if (empty($attributeValue)) {
            return $this;
        }
        $productCollection = $this->getLayer()->getProductCollection();
        $productCollection->applyFilterToCollection('category_ids', $attributeValue);
        return $this;
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $category = $this->getCategory();
        $categories = $category->getChildrenCategories();

        $this->getLayer()->getProductCollection()->addCountToCategories($categories);

        if ($category->getIsActive()) {
            foreach ($categories as $category) {
                if ($category->getIsActive() && $category->getProductCount()) {
                    $this->itemDataBuilder->addItemData(
                        $this->_escaper->escapeHtml($category->getName()),
                        $category->getId(),
                        $category->getProductCount()
                    );
                }
            }
        }
        return $this->itemDataBuilder->build();
    }
}
