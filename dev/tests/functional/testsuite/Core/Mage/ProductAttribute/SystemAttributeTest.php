<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ProductAttribute
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Check the impossibility to edit Apply to values for system attributes
 */
class Core_Mage_ProductAttribute_SystemAttributeTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     * Navigate to System - Manage Attributes.
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_attributes');
    }

    /**
     * Values of Apply To dropdown and multiselect are defined and can't be changed for all system attributes
     *
     * @test
     * @TestLinkId TL-MAGE-6423
     */
    public function checkApplyProductTypeOptionDisabled()
    {
        //Steps
        $systemAttributesData = $this->loadDataSet('SystemAttributes', 'system_attributes');
        $i = 0;
        foreach ($systemAttributesData as $systemAttribute){
            $this->addParameter('attribute_code', $systemAttribute['attribute_code']);
            switch ($systemAttribute['attribute_code']) {
                case 'price':
                    $search = array(
                        'attribute_code' => 'price',
                        'scope' => 'Website',
                        'use_in_layered_navigation' => 'Filterable (with results)'
                    );
                    break;
                case 'status':
                    $search = array('attribute_label' => 'Status');
                    break;
                case 'image':
                    $search = array('attribute_label' => 'Base Image');
                    break;
                default:
                    $search = array('attribute_code' => $systemAttribute['attribute_code']);
                    break;
            }
            $this->productAttributeHelper()->openAttribute($search);
            //Verifying
            $this->assertFalse($this->getControlElement('dropdown', 'apply_to')->enabled());
            if ($systemAttribute['apply_to'] == 'All Product Types') {
                $this->assertFalse($this->controlIsPresent('multiselect', 'apply_product_types'));
            } else {
                $this->assertTrue($this->controlIsPresent('multiselect', 'apply_product_types'),
                    'Apply To multiselect is absent');
                $element = $this->getControlElement('multiselect', 'apply_product_types');
                $this->assertFalse($element->enabled(), 'Apply To multiselect is enabled');
                $this->assertEquals($systemAttribute['apply_product_types'], $this->select($element)->selectedValues());
            }
            $systemAttributesData['attribute_' . $i]++;
            $this->navigate('manage_attributes');
        }
    }
}
