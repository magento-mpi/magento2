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
 * Product type changing while product creation/editing
 */
class Community2_Mage_Product_ChangeProductTypeTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * <p>Simple to Virtual/Downloadable product type switching on product creation</p>
     *
     * @param string $changedProduct
     * @param string $changedType
     *
     * @test
     * @dataProvider fromSimpleToVirtualDataProvider
     * @TestLinkId TL-MAGE-6426, TL-MAGE-6427
     */
    public function fromSimpleToVirtualDuringCreation($changedProduct, $changedType)
    {
        //Data
        $simpleProduct = array('product_attribute_set' => 'Default');
        $productData = $this->loadDataSet('Product', $changedProduct . '_product_visible');
        //Steps
        $this->productHelper()->selectTypeProduct($simpleProduct, 'simple');
        $this->fillCheckbox('weight_and_type_switcher', 'yes');
        $this->productHelper()->fillProductInfo($productData, $changedProduct);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('type' => $changedType, 'sku' => $productData['general_sku']));
        $this->assertTrue($this->controlIsVisible('field', 'general_weight_disabled'), 'Weight field is editable');
        $this->assertTrue($this->controlIsVisible('tab', 'downloadable_information'),
            'Downloadable Information is absent');
    }

    /**
     * <p>Data provider for changing product type from simple to virtual/downloadable</p>
     *
     * @return array
     */
    public function fromSimpleToVirtualDataProvider()
    {
        return array(
            array('virtual', 'Virtual Product'),
            array('downloadable', 'Downloadable Product')
        );
    }

    /**
     * <p>Virtual/Downloadable to Simple/Downloadable/Virtual product type switching on product creation</p>
     *
     * @param string $initialProduct
     * @param string $changedProduct
     * @param string $changedType
     *
     * @test
     * @dataProvider fromVirtualDownloadableDataProvider
     * @TestLinkId TL-MAGE-6428, TL-MAGE-6429, TL-MAGE-6430, TL-MAGE-6431
     */
    public function fromVirtualDownloadableDuringCreation($initialProduct, $changedProduct, $changedType)
    {
        //Data
        $initialProductData = array('product_attribute_set' => 'Default');
        $productData = $this->loadDataSet('Product', $changedProduct . '_product_visible');
        //Steps
        $this->productHelper()->selectTypeProduct($initialProductData, $initialProduct);
        $this->assertTrue($this->controlIsVisible('field', 'general_weight_disabled'), 'Weight field is editable');
        $this->assertTrue($this->isChecked($this->_getControlXpath('checkbox', 'weight_and_type_switcher')),
            'Weight checkbox is not selected');
        if ($changedProduct == 'simple') {
            $this->fillCheckbox('weight_and_type_switcher', 'no');
        }
        $this->productHelper()->fillProductInfo($productData, $changedProduct);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('type' => $changedType, 'sku' => $productData['general_sku']));
        if ($changedProduct == 'simple') {
            $this->assertTrue($this->controlIsVisible('field', 'general_weight'), 'Weight field is not editable');
            $this->assertFalse($this->controlIsVisible('tab', 'downloadable_information'),
                'Downloadable Information is absent');
        } else {
            $this->assertTrue($this->controlIsVisible('field', 'general_weight_disabled'), 'Weight field is editable');
            $this->assertTrue($this->controlIsVisible('tab', 'downloadable_information'),
                'Downloadable Information is absent');
        }
    }

    /**
     * <p>Data provider for changing virtual product type to simple/virtual/downloadable</p>
     *
     * @return array
     */
    public function fromVirtualDownloadableDataProvider()
    {
        return array(
            array('virtual', 'simple', 'Simple Product', 'Virtual Product'),
            array('virtual', 'downloadable', 'Downloadable Product', 'Virtual Product'),
            array('downloadable', 'simple', 'Simple Product', 'Downloadable Product'),
            array('downloadable', 'virtual', 'Virtual Product', 'Downloadable Product')
        );
    }

    /**
     * <p>Simple to Virtual/Downloadable product type switching while product editing</p>
     *
     * @param string $changedProduct
     * @param string $changedType
     *
     * @test
     * @dataProvider fromSimpleToVirtualDataProvider
     * @TestLinkId TL-MAGE-6432, TL-MAGE-6433
     */
    public function fromSimpleToVirtualDuringEditing($changedProduct, $changedType)
    {
        //Data
        $simpleProduct = $this->loadDataSet('Product', 'simple_product_visible');
        $productData = $this->loadDataSet('Product', $changedProduct . '_product_visible');
        //Steps
        $this->productHelper()->createProduct($simpleProduct);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('type' => 'Simple Product', 'sku' => $simpleProduct['general_sku']));
        $this->fillCheckbox('weight_and_type_switcher', 'yes');
        $this->productHelper()->fillProductInfo($productData, $changedProduct);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('type' => $changedType, 'sku' => $productData['general_sku']));
        $this->assertTrue($this->controlIsVisible('field', 'general_weight_disabled'), 'Weight field is editable');
        $this->assertTrue($this->controlIsVisible('tab', 'downloadable_information'),
            'Downloadable Information is absent');
    }

    /**
     * <p>Virtual/Downloadable to Simple/Downloadable/Virtual product type switching while product editing</p>
     *
     * @param string $initialProduct
     * @param string $changedProduct
     * @param string $changedType
     * @param string $initialType
     *
     * @test
     * @dataProvider fromVirtualDownloadableDataProvider
     * @TestLinkId TL-MAGE-6434, TL-MAGE-6435, TL-MAGE-6436, TL-MAGE-6437
     */
    public function fromVirtualDownloadableDuringEditing($initialProduct, $changedProduct, $changedType, $initialType)
    {
        //Data
        $initialProductData = $this->loadDataSet('Product', $initialProduct . '_product_visible');
        $productData = $this->loadDataSet('Product', $changedProduct . '_product_visible');
        //Steps
        $this->productHelper()->createProduct($initialProductData, $initialProduct);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('type' => $initialType, 'sku' => $initialProductData['general_sku']));
        if ($changedProduct == 'simple') {
            $this->fillCheckbox('weight_and_type_switcher', 'no');
        }
        $this->productHelper()->fillProductInfo($productData, $changedProduct);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('type' => $changedType, 'sku' => $productData['general_sku']));
        if ($changedProduct == 'simple') {
            $this->assertTrue($this->controlIsVisible('field', 'general_weight'), 'Weight field is not editable');
            $this->assertFalse($this->controlIsVisible('tab', 'downloadable_information'),
                'Downloadable Information is absent');
        } else {
            $this->assertTrue($this->controlIsVisible('field', 'general_weight_disabled'), 'Weight field is editable');
            $this->assertTrue($this->controlIsVisible('tab', 'downloadable_information'),
                'Downloadable Information is absent');
        }
    }
}
