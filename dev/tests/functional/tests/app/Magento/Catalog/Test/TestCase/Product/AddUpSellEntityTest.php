<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

/**
 * Class AddUpSellEntityTest
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create related products
 *
 * Steps:
 * 1. Login to the backend
 * 2. Navigate to Products > Catalog
 * 3. Start to create product according to dataset
 * 4. Save product
 * 5. Perform appropriate assertions
 *
 * @group Up-sells_(MX)
 * @ZephyrId MAGETWO-29105
 */
class AddUpSellEntityTest extends AbstractAddRelatedProductsEntityTest
{
    /**
     * Type of related products
     *
     * @var string
     */
    protected $typeRelatedProducts = 'up_sell_products';
}
