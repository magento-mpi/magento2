<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Mtf\Constraint\AbstractConstraint;
use Magento\GiftCard\Test\Fixture\GiftCardProduct;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;

/**
 * Class AssertDuplicatedProductInGrid
 */
class AssertDuplicatedProductInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Mapping types of products
     *
     * @var array
     */
    protected $productTypeMapping = [
        'CatalogProductSimple' => 'Simple Product',
        'CatalogProductDownloadable' => 'Downloadable Product',
        'CatalogProductConfigurable' => 'Configurable Product',
        'GiftCardProduct' => 'Gift Card',

    ];

    /**
     * Assert that duplicated product is found by sku and has correct product type, product template,
     * product status disabled and out of stock
     *
     * @param FixtureInterface $product
     * @param CatalogProductIndex $productGrid
     * @return void
     */
    public function processAssert(FixtureInterface $product, CatalogProductIndex $productGrid)
    {
        $filter = [
            'name' => $product->getName(),
            'visibility' => $product->getVisibility(),
            'status' => 'Disabled',
            'sku' => $product->getSku() . '-1',
            'type' => $this->productTypeMapping[basename(get_class($product))]
        ];

        $productGrid->open()
            ->getProductGrid()
            ->search($filter);

        if (!($product instanceof GiftCardProduct)) {
            $filter['price_to'] = '$' . number_format($product->getPrice(), 2);
        }

        \PHPUnit_Framework_Assert::assertTrue(
            $productGrid->getProductGrid()->isRowVisible($filter, false),
            'Product duplicate is absent in Products grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'The product has been successfully found, according to the filters.';
    }
}
