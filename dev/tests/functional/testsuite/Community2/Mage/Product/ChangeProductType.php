<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Category assignment on general tab
 */
class Community2_Mage_Product_ChangeProductType extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * <p>Change product type after creation(from downloadable to simple)</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6426
     */
    public function changeDownloadableTypeToSimple()
    {
        //Data
        $downloadable = $this->loadDataSet('Product', 'downloadable_product_visible');
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $downloadable['general_sku']));
        $fillData = array('weight_and_type_switcher' => 'No', 'general_weight' => 11);
        $verify = array_merge($downloadable, $fillData);
        unset($verify['downloadable_information_data']);
        //Steps and verifying
        $this->productHelper()->createProduct($downloadable, 'downloadable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct($search);
        $this->assertTrue($this->controlIsVisible('tab', 'downloadable_information'),
            'Downloadable information tab is not present on the page');
        $this->fillTab($fillData, 'general');
        $this->assertFalse($this->controlIsVisible('tab', 'downloadable_information'),
            'Downloadable information tab is present on the page');
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        $column = $this->getColumnIdByName('Type');
        $productLocator = $this->formSearchXpath($search);
        $this->assertEquals('Simple Product', trim($this->getText($productLocator . "//td[$column]")), '');
        $this->productHelper()->openProduct($search);
        $this->assertFalse($this->controlIsVisible('tab', 'downloadable_information'),
            'Downloadable information tab is present on the page');
        $this->verifyForm($verify);
        $this->assertEmptyVerificationErrors();
    }
}
