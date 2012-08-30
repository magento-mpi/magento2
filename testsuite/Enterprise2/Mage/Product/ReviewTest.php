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
 * Gift Card product product review test
 *
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @license     {license_link}
 */
class Enterprise2_Mage_Product_ReviewTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog -> Manage Products</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * <p>Review Gift Card on frontend.</p>
     * <p>Steps:</p>
     * <p>1. Create Gift Card product in stock and out of stock;</p>
     * <p>2. Navigate to frontend;</p>
     * <p>3. Validate the product details;</p>
     * <p>Expected result:</p>
     * <p>Products are created. All details displays according to settings</p>
     *
     * @param string $availability
     *
     * @test
     * @dataProvider reviewInfoDataProvider
     * @TestlinkId TL-MAGE-5868
     */
    public function reviewGiftCardFrontend($availability)
    {
        $productData = $this->loadDataSet('Product', 'frontend_gift_card_validation',
            array('inventory_stock_availability' => $availability));
        $this->productHelper()->createProduct($productData, 'giftcard');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->reindexInvalidedData();
        $this->clearInvalidedCache();
        $this->productHelper()->frontVerifyGiftCardInfo($productData);
    }

    public function reviewInfoDataProvider()
    {
        return array(
            array('In Stock'),
            array('Out of Stock'),
        );
    }
}