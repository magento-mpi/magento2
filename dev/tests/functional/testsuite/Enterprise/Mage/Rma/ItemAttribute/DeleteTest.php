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
 * Delete RMA item attribute
 *
 * @package     Mage_RMA
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_Mage_Rma_ItemAttribute_DeleteTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_rma_items_attribute');
    }

    /**
     * <p>Delete custom attribute</p>
     *
     * @param array $attributeType
     *
     * @test
     * @dataProvider allTypeCustomDataProvider
     * @TestlinkId TL-MAGE-6113
     */
    public function allTypeCustomAttribute($attributeType)
    {
        //Data
        $attrData = $this->loadDataSet('RMAItemsAttribute', $attributeType);
        $this->addParameter('elementTitle', $attrData['attribute_properties']['attribute_label']);
        //Steps
        $this->attributesHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->searchAndOpen(array('filter_attribute_code' => $attrData['attribute_properties']['attribute_code']),
            'rma_item_atribute_grid');
        $this->clickButtonAndConfirm('delete_attribute', 'delete_confirm_message');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_attribute');
    }

    public function allTypeCustomDataProvider()
    {
        return array(
            array('rma_item_attribute_textfield'),
            array('rma_item_attribute_textarea'),
            array('rma_item_attribute_dropdown'),
            array('rma_item_attribute_image')
        );
    }

    /**
     * <p>Verify that impossible Delete system RMA attribute</p>
     *
     * @param array $attributeLabel
     *
     * @test
     * @dataProvider systemAttributeDataProvider
     * @TestlinkId TL-MAGE-6115
     */
    public function systemAttribute($attributeLabel)
    {
        //Steps
        $this->addParameter('elementTitle', $attributeLabel);
        $this->searchAndOpen(array('filter_attribute_label' => $attributeLabel), 'rma_item_atribute_grid');
        //Verifying
        $this->assertFalse($this->controlIsPresent('button', 'delete_attribute'),
            'Delete button must be absent for system RMA attribute');
    }

    public function systemAttributeDataProvider()
    {
        return array(
            array('Resolution'),
            array('Item Condition'),
            array('Reason to Return'),
            array('Other')
        );
    }
}
