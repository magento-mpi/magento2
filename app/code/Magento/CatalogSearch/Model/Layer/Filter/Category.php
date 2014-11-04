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

        $category = $this->_categoryFactory->create()
            ->setStoreId(
                $this->getLayer()->getCurrentStore()->getId()
            )
            ->load(
                $attributeValue
            );
        $this->_coreRegistry->register('current_category_filter', $category, true);

        if (!$category->getId()) {
            $category = $this->getLayer()->getCurrentCategory();
        }
        $this->getLayer()->getProductCollection()->addFieldToFilter(
            'category_ids',
            $category->getId() ?: $this->getLayer()->getCurrentCategory()->getId()
        );
//        $this->getLayer()->getProductCollection()->addCategoryFilter($category);

        if ($request->getParam('id') != $category->getId() && $this->_isValidCategory($category)) {
            $this->getLayer()->getState()->addFilter($this->_createItem($category->getName(), $attributeValue));
        }
        return $this;
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $productCollection = $this->getLayer()->getProductCollection();
        $optionsFacetedData = $productCollection->getFacetedData('category');
        $category = $this->getCategory();
        $categories = $category->getChildrenCategories();

        if ($category->getIsActive()) {
            foreach ($categories as $category) {
                if ($category->getIsActive() && isset($optionsFacetedData[$category->getId()])) {
                    $this->itemDataBuilder->addItemData(
                        $this->_escaper->escapeHtml($category->getName()),
                        $category->getId(),
                        $optionsFacetedData[$category->getId()]['count']
                    );
                }
            }
        }
        return $this->itemDataBuilder->build();
    }
}
