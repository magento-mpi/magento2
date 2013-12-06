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
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Catalog\Test\Block\Product\Grouped\AssociatedProducts\ListAssociatedProducts;

class AssociatedProducts extends Tab
{
    /**
     * Tab where Grouped options section is placed
     */
    const GROUP_PRODUCT_DETAILS = '#product_info_tabs_product-details';

    /**
     * 'Create New Option' button
     *
     * @var Element
     */
    protected $addNewOption = '#grouped-product-container>button';

    /**
     * @param Element $context
     * @return AssociatedProducts\Search\Grid
     */
    private function getSearchGridBlock(Element $context = null)
    {
        $element = $context ? : $this->_rootElement;

        return Factory::getBlockFactory()
            ->getMagentoCatalogProductGroupedAssociatedProductsSearchGrid(
                $element->find('[role=dialog][style*="display: block;"]')
            );
    }

    /**
     * @param Element $context
     * @return ListAssociatedProducts
     */
    private function getListAssociatedProductsBlock(Element $context = null)
    {
        $element = $context ? : $this->_rootElement;

        return Factory::getBlockFactory()
            ->getMagentoCatalogProductGroupedAssociatedProductsListAssociatedProducts(
                $element->find("[data-role=grouped-product-grid]")
            );
    }

    /**
     * Fill Grouped options
     *
     * @param array $fields
     * @param Element $element
     */
    public function fillFormTab(array $fields, Element $element)
    {
        foreach ($fields['grouped_products']['value'] as $groupedProduct) {
            $element->find($this->addNewOption)->click();
            $searchBlock = $this->getSearchGridBlock($element);
            $searchBlock->searchAndSelect($groupedProduct['search_data']);
            $searchBlock->addProducts();
            $this->getListAssociatedProductsBlock()->fillProductOptions($groupedProduct['data']);
        }
    }
}
