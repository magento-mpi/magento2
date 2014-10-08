<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

/**
 * Class AddRelatedProductsEntityTest
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create simple Product
 * 2. Create Configurable Product

 * Steps:
 * Open Backend
 * Go to Products> Catalog
 * Add Product
 * Fill data according to dataSet
 * Save product
 * Perform all assertions
 *
 * @group Related_Products_(MX)
 * @ZephyrId MAGETWO-29352
 */
class AddRelatedProductsEntityTest extends AbstractAddRelatedProductsEntityTest
{
    /**
     * Type of related products
     *
     * @var string
     */
    protected $typeRelatedProducts = 'related_products';
}
