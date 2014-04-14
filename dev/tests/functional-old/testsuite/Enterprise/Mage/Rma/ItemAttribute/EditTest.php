<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_RMA
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Edit RMA item attribute
 *
 * @package     Mage_RMA
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_Rma_ItemAttribute_EditTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Manage Attributes.</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_rma_items_attribute');
    }

    /**
     * <p>Edit RMA item Attribute</p>
     *
     * @test
     * @dataProvider customAttributeDataProvider
     * @TestlinkId TL-MAGE-6120
     */
    public function customAttribute($attributeType)
    {
        //Data
        $attrData = $this->loadDataSet('RMAItemsAttribute', $attributeType);
        //Steps
        $this->attributesHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $attrData['sort_order'] = 5;
        $attrData['attribute_properties']['attribute_label'] = 'Title after edit';
        $this->attributesHelper()->openAttribute(
            array('filter_attribute_code' => $attrData['attribute_properties']['attribute_code'])
        );
        $this->fillField('sort_order', 5);
        $this->fillField('attribute_label', 'Title after edit');
        $this->saveForm('save_attribute');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->attributesHelper()->openAttribute(
            array('filter_attribute_code' => $attrData['attribute_properties']['attribute_code'])
        );
        $this->attributesHelper()->verifyAttribute($attrData);
    }

    public function customAttributeDataProvider()
    {
        return array(
            array('rma_item_attribute_textfield'),
            array('rma_item_attribute_textarea'),
            array('rma_item_attribute_dropdown'),
            array('rma_item_attribute_image')
        );
    }
}
