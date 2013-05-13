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
 * Grouped product creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Saas_Mage_Product_Create_GroupedTest extends Core_Mage_Product_Create_GroupedTest
{

    /**
     * <p>Creating Grouped product with Downloadable product</p>
     * <p>Override original testcase. Downloadable product is not available in Saas</p>
     *
     * @test
     */
    public function groupedWithDownloadableProduct()
    {
        $this->markTestIncomplete('Functionality is absent in Magento Go.');
    }

    /**
     * <p>Creating Grouped product with All types of products type</p>
     * <p>Override original testcase. Downloadable product is not available in Saas</p>
     *
     *
     * @param string $simpleSku
     * @param string $virtualSku
     * @param string $downloadableSku
     *
     * @test
     * @depends groupedWithSimpleProduct
     * @depends groupedWithVirtualProduct
     * @TestlinkId TL-MAGE-3403
     */
    public function groupedWithAllTypesProduct($simpleSku, $virtualSku, $downloadableSku = null)
    {
        //Data
        $groupedData =
            $this->loadDataSet('Product', 'grouped_product_required', array('associated_search_sku' => $simpleSku));
        $groupedData['general_grouped_data']['associated_grouped_2'] =
            $this->loadDataSet('Product', 'associated_grouped', array('associated_search_sku' => $virtualSku));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $groupedData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($groupedData, 'grouped');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($groupedData);
    }
}
