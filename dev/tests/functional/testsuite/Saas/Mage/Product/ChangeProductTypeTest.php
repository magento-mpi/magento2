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
class Saas_Mage_Product_ChangeProductTypeTest extends Core_Mage_Product_ChangeProductTypeTest
{
    /**
     * <p>Data provider for changing product type from simple to virtual</p>
     *
     * @return array
     */
    public function fromSimpleToVirtualDataProvider()
    {
        return array(
            array('virtual', 'Virtual Product')
        );
    }

    /**
     * <p>Data provider for changing virtual product type to simple/virtual</p>
     *
     * @return array
     */
    public function fromVirtualDownloadableDataProvider()
    {
        return array(
            array('virtual', 'simple', 'Simple Product', 'Virtual Product')
        );
    }

    public function toConfigurableDataProvider()
    {
        return array(
            array('simple'),
            array('virtual')
        );
    }

    /**
     * <p>Simple to Virtual product type switching on product creation</p>
     *
     * @param string $changedProduct
     * @param string $changedType
     *
     * @test
     * @dataProvider fromSimpleToVirtualDataProvider
     * @TestLinkId TL-MAGE-6426
     */
    public function fromSimpleToVirtualDuringCreation($changedProduct, $changedType)
    {
        //Data
        $productData = $this->loadDataSet('Product', $changedProduct . '_product_visible');
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->selectTypeProduct('simple');
        $this->fillCheckbox('general_weight_and_type_switcher', 'yes');
        $this->productHelper()->fillProductInfo($productData);
        $this->productHelper()->saveProduct();
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals($changedType, $this->productHelper()->getProductDataFromGrid($search, 'Type'),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct($search);
        $this->assertTrue($this->controlIsVisible('field', 'general_weight_disabled'),
            'Weight field is editable or is not visible');
    }

    /**
     * <p>Simple to Virtual product type switching while product editing</p>
     *
     * @param string $changedProduct
     * @param string $changedType
     *
     * @test
     * @dataProvider fromSimpleToVirtualDataProvider
     * @TestLinkId TL-MAGE-6432
     */
    public function fromSimpleToVirtualDuringEditing($changedProduct, $changedType)
    {
        //Data
        $simpleProduct = $this->loadDataSet('Product', 'simple_product_required');
        $productData = $this->loadDataSet('Product', $changedProduct . '_product_visible');
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($simpleProduct);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('sku' => $simpleProduct['general_sku']));
        $this->fillCheckbox('general_weight_and_type_switcher', 'yes');
        $this->productHelper()->fillProductInfo($productData);
        $this->productHelper()->saveProduct();
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals($changedType, $this->productHelper()->getProductDataFromGrid($search, 'Type'),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct($search);
        $this->assertTrue($this->controlIsVisible('field', 'general_weight_disabled'),
            'Weight field is editable or is not visible');
    }
}
