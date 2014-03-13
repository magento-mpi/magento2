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


namespace Magento\Catalog\Test\Block\Product\Grouped\AssociatedProducts;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class ListAssociatedProducts
 *
 * @package Magento\Catalog\Test\Block\Product\Grouped\AssociatedProducts
 */
class ListAssociatedProducts extends Block
{
    /**
     * @param string $productId
     * @param Element $context
     * @return ListAssociatedProducts\Product
     */
    private function getProductBlock($productId, Element $context = null)
    {
        $element = $context ? : $this->_rootElement;
        return Factory::getBlockFactory()
            ->getMagentoCatalogProductGroupedAssociatedProductsListAssociatedProductsProduct(
                $element->find(
                    sprintf("//tr[td/input[@data-role='id' and @value='%s']]", $productId),
                    Locator::SELECTOR_XPATH
                )
            );
    }

    /**
     * @param array $data
     * @param Element $element
     */
    public function fillProductOptions(array $data, Element $element = null)
    {
        $productBlock = $this->getProductBlock($data['product_id']['value'], $element);
        $productBlock->fillQty($data['selection_qty']['value']);
    }
}
