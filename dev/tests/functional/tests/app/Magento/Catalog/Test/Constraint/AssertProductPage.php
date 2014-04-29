<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Mtf\Fixture\FixtureInterface;

/**
 * Class AssertProductPage
 *
 * @package Magento\Catalog\Test\Constraint
 */
class AssertProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that displayed product data on product page(front-end) equals passed from fixture (name, price, description)
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, FixtureInterface $product)
    {
        $catalogProductView->init($product);
        $catalogProductView->open();
        $fields = $product->getData();
        //1. Product Name
        \PHPUnit_Framework_Assert::assertEquals(
            $fields['name'],
            $catalogProductView->getViewBlock()->getProductName(),
            'Assert that displayed product data on product page(front-end) is NOT equals passed from fixture product name.'
        );
        //2. Price
        $price = (is_array($catalogProductView->getViewBlock()->getProductPrice())) ? $catalogProductView->getViewBlock(
        )->getProductPrice()['price_regular_price'] : $catalogProductView->getViewBlock()->getProductPrice();
        \PHPUnit_Framework_Assert::assertEquals(
            $fields['price'],
            $price,
            'Assert that displayed product data on product page(front-end) is NOT equals passed from fixture product price.'
        );

        //3. SKU
        \PHPUnit_Framework_Assert::assertEquals(
            $fields['sku'],
            $catalogProductView->getViewBlock()->getProductSku(),
            'Assert that displayed product data on product page(front-end) is NOT equals passed from fixture product sku.'
        );

        //4. Description
        if (isset($fields['description'])) {
            \PHPUnit_Framework_Assert::assertEquals(
                $fields['description'],
                $catalogProductView->getViewBlock()->getProductDescription(),
                'Assert that displayed product data on product page(front-end) is NOT equals passed from fixture product description.'
            );
        }

        //5. Short Description
        if (isset($fields['short_description'])) {
            \PHPUnit_Framework_Assert::assertEquals(
                $fields['short_description'],
                $catalogProductView->getViewBlock()->getProductShortDescription(),
                'Assert that displayed product data on product page(front-end) is NOT equals passed from fixture product short description.'
            );
        }
    }

    /**
     * @return string
     */
    public function toString()
    {
        return 'Product on product view page is not correct.';
    }
}
