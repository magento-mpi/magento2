<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Product\Grouped;

use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class AssociatedProducts
 *
 * @package Magento\Catalog\Test\Block\Product\Grouped
 */
class AssociatedProducts extends Tab
{
    /**
     * 'Create New Option' button
     *
     * @var Element
     */
    protected $addNewOption = '#grouped-product-container>button';

    /**
     * Associated products grid
     *
     * @var string
     */
    protected $productSearchGrid = '[role=dialog][style*="display: block;"]';

    /**
     * Associated products list block
     *
     * @var string
     */
    protected $associatedProductsBlock = '[data-role=grouped-product-grid]';

    /**
     * Get search grid
     *
     * @param Element $context
     * @return AssociatedProducts\Search\Grid
     */
    protected function getSearchGridBlock(Element $context = null)
    {
        $element = $context ? : $this->_rootElement;

        return Factory::getBlockFactory()->getMagentoCatalogProductGroupedAssociatedProductsSearchGrid(
            $element->find($this->productSearchGrid)
        );
    }

    /**
     * Get associated products list block
     *
     * @param Element $context
     * @return \Magento\Catalog\Test\Block\Product\Grouped\AssociatedProducts\ListAssociatedProducts
     */
    protected function getListAssociatedProductsBlock(Element $context = null)
    {
        $element = $context ? : $this->_rootElement;

        return Factory::getBlockFactory()->getMagentoCatalogProductGroupedAssociatedProductsListAssociatedProducts(
            $element->find($this->associatedProductsBlock)
        );
    }

    /**
     * Fill data to fields on tab
     *
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element)
    {
        if (isset($fields['grouped_products'])) {
            foreach ($fields['grouped_products']['value'] as $groupedProduct) {
                $element->find($this->addNewOption)->click();
                $searchBlock = $this->getSearchGridBlock($element);
                $searchBlock->searchAndSelect($groupedProduct['search_data']);
                $searchBlock->addProducts();
                $this->getListAssociatedProductsBlock()->fillProductOptions($groupedProduct['data']);
            }
        }

        return $this;
    }
}
