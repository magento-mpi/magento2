<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

/**
 * Class AddCrossSellEntityTest
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create cross cell products
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Products > Catalog
 * 3. Click Add new product
 * 4. Fill data from dataSet
 * 5. Save product
 * 6. Perform all assertions
 *
 * @group Cross-sells_(MX)
 * @ZephyrId MAGETWO-29081
 */
class AddCrossSellEntityTest extends AbstractAddRelatedProductsEntityTest
{
    /**
     * Type of related products
     *
     * @var string
     */
    protected $typeRelatedProducts = 'cross_sell_products';
}
