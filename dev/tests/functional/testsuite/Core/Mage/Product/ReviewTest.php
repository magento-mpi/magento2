<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Simple and virtual product review test
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Product_ReviewTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * <p>Review product on frontend.</p>
     * <p>Steps:</p>
     * <p>1. Create simple and virtual products in stock and out of stock;</p>
     * <p>2. Fill custom options for each product;</p>
     * <p>3. Navigate to frontend;</p>
     * <p>4. Validate the product details;</p>
     * <p>Expected result:</p>
     * <p>Products are created, Custom options are available for in stock product and disabled for out of stock;</p>
     *
     * @param string $productType
     * @param string $availability
     *
     * @test
     * @dataProvider reviewInfoInProductDetailsDataProvider
     * @TestlinkId TL-MAGE-3469
     * @group skip_due_to_bug1.12
     */
    public function reviewInfoInProductDetails($productType, $availability)
    {
        $productData = $this->loadDataSet('Product', 'frontend_' . $productType . '_product_details_validation',
                                          array('inventory_stock_availability' => $availability));
        $this->productHelper()->createProduct($productData, $productType);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->reindexInvalidedData();
        $this->clearInvalidedCache();
        $this->productHelper()->frontVerifyProductInfo($productData);
    }

    public function reviewInfoInProductDetailsDataProvider()
    {
        return array(
            array('simple', 'In Stock'),
            array('simple', 'Out of Stock'),
            array('virtual', 'In Stock'),
            array('virtual', 'Out of Stock')
        );
    }
}