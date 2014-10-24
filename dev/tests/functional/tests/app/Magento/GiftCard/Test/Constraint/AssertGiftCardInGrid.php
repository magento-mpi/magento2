<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\GiftCard\Test\Fixture\GiftCardProduct;

/**
 * Class AssertGiftCardInGrid
 * Assert that gift card product is present in products grid.
 */
class AssertGiftCardInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that gift card is present in products grid and can be found by sku, type, status and attribute set.
     *
     * @param GiftCardProduct $product
     * @param CatalogProductIndex $productGrid
     * @return void
     */
    public function processAssert(GiftCardProduct $product, CatalogProductIndex $productGrid)
    {
        $productGrid->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $productGrid->getProductGrid()->isRowVisible($this->prepareFilter($product)),
            'Gift card \'' . $product->getName() . '\' is absent in Products grid.'
        );
    }

    /**
     * Prepare filter for product grid.
     *
     * @param GiftCardProduct $product
     * @return array
     */
    protected function prepareFilter(GiftCardProduct $product)
    {
        $productStatus = ($product->getStatus() === null || $product->getStatus() === 'Product online')
            ? 'Enabled'
            : 'Disabled';
        $filter = [
            'type' => 'Gift Card',
            'sku' => $product->getSku(),
            'status' => $productStatus,
        ];
        if ($product->hasData('attribute_set_id')) {
            $filter['set_name'] = $product->getAttributeSetId();
        }

        return $filter;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift card is present in products grid.';
    }
}
