<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ProductAttribute
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Create new product attribute. Type: Date
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_ProductAttribute_Create_CreateFromProductPageTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Manage Attributes.</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    protected function tearDownAfterTest()
    {
        $this->closeLastWindow();
    }

    /**
     * <p>Checking of attributes creation functionality during product creation process</p>
     *
     * @param $attributeType
     *
     * @test
     * @dataProvider onProductPageWithRequiredFieldsOnlyDataProvider
     * @TestlinkId    TL-MAGE-3322
     */
    public function onProductPageWithRequiredFieldsOnly($attributeType)
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', $attributeType);
        //Steps
        $this->productHelper()->selectTypeProduct('simple');
        $this->productAttributeHelper()->createAttributeOnProductTab($attrData);
        //Verifying
        $code = ($attributeType != 'product_attribute_fpt')
            ? $attrData['attribute_code']
            : $attrData['attribute_code'] . '_table';
        $this->addParameter('elementId', $code);
        $this->assertTrue($this->controlIsPresent('pageelement', 'element_by_id'));
    }

    public function onProductPageWithRequiredFieldsOnlyDataProvider()
    {
        return array(
            array('product_attribute_textfield'),
            array('product_attribute_textarea'),
            array('product_attribute_date'),
            array('product_attribute_yesno'),
            array('product_attribute_multiselect_with_options'),
            array('product_attribute_dropdown_with_options'),
            array('product_attribute_price'),
            array('product_attribute_fpt')
        );
    }
}