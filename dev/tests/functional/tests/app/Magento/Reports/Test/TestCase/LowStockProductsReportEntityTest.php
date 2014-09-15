<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Test Creation for LowStockProductsReportEntityTest
 *
 * Test Flow:
 * Preconditions:
 * 1. Product is created.
 *
 * Steps:
 * 1. Login to backend.
 * 2. Open Reports > Low Stock.
 * 3. Perform appropriate assertions.
 *
 * @group Reports_(MX)
 * @ZephyrId MAGETWO-27193
 */
class LowStockProductsReportEntityTest extends Injectable
{
    /**
     * Create product
     *
     * @param CatalogProductSimple $product
     * @return void
     */
    public function test(CatalogProductSimple $product)
    {
        // Preconditions
        $product->persist();
    }
}
