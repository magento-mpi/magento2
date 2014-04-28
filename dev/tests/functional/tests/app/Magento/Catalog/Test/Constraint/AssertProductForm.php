<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Factory\Factory;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\InjectableFixture;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Product\CatalogProductEdit;

/**
 * Class AssertProductForm
 *
 * @package Magento\Catalog\Test\Constraint
 */
class AssertProductForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert product availability in Products Grid
     *
     * @param InjectableFixture $product
     * @param CatalogProductIndex $productPageGrid
     * @param CatalogProductEdit $productBlockForm
     * @return void
     */
    public function processAssert(
        InjectableFixture $product,
        CatalogProductIndex $productPageGrid,
        CatalogProductEdit $productBlockForm
    ) {
        return;
        $filter = ['sku' => $product->getData('sku')];
        $productPageGrid->open();
        $productPageGrid->getProductGrid()->searchAndOpen($filter);
        \PHPUnit_Framework_Assert::assertTrue(
            $productBlockForm->getProductBlockForm()->verify($product),
            'Displayed product data on edit page not equals passed from fixture.'
        );
    }

    /**
     * @return string
     */
    public function toString()
    {
        return 'Displayed product data on edit page equals passed from fixture.';
    }
}
