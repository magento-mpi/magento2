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
        $this->addParameter('elementTitle', $attrData['attribute_properties']['attribute_label']);
        //Steps
        $this->attributesHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->searchAndOpen(array('filter_attribute_code' => $attrData['attribute_properties']['attribute_code']),
            'rma_item_atribute_grid');
        $this->fillField('sort_order', 5);
        $attrData['sort_order'] = 5;
        $this->fillField('attribute_label', 'Title after edit');
        $attrData['attribute_properties']['attribute_label'] = 'Title after edit';
        $this->clickButton('save_attribute', false);
        $this->waitForAjax();
        $this->waitForPageToLoad();
        $this->validatePage();
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->addParameter('elementTitle', $attrData['attribute_properties']['attribute_label']);
        $this->searchAndOpen(array('filter_attribute_code' => $attrData['attribute_properties']['attribute_code']),
            'rma_item_atribute_grid');
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
