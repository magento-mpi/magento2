<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Create customer attributes
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_CustomerAddressAttribute_CreateTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customer_address_attributes');
    }
    /**
    * @test
    * @dataProvider allFieldsData
    */
    public function withAllFields($attrData)
    {
        //Steps
        $this->customerAddressAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
    }
    public function allFieldsData()
    {
        return array(
            array(array('input_type'=>'Text Field',
                  'attribute_code'=>'attr_'. $this->generate('string', 16, ':lower:'),
                  'default_text_field_value'=>'attr_default_' . $this->generate('string', 10, ':lower:'),
                  'values_required'=>'Yes',
                  'input_validation'=>'None',
                  'min_text_length'=>'1',
                  'max_text_length'=>'255',
                  'input_filter'=>'None',
                  'used_for_customer_segment'=>'No',
                  'visible_on_frontend'=>'Yes',
                  'sort_order'=>'1',
                  'used_in_forms'=>"Customer Account Address, Customer Address Registration",
                  'admin_title'=>'Text_Field_Admin_' . $this->generate('string', 5, ':lower:'),
                  'store_view_titles' => array(
                                                 'Default Store View' =>'Text_Field_StoreView'))
        ));
    }
}