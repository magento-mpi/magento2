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
 * Delete product attributes
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductAttribute_DeleteTest extends Mage_Selenium_TestCase
{

    /**
     * Log in to Backend.
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * Preconditions:
     * Navigate to System -> Manage Attributes.
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_attributes');
        $this->assertTrue($this->checkCurrentPage('manage_attributes'), 'Wrong page is opened');
        $this->addParameter('id', 0);
    }

    /**
     * Delete Product Attributes
     *
     * Steps:
     * 1.Click on "Add New Attribute" button
     * 2.Fill all required fields
     * 3.Click on "Save Attribute" button
     * 4.Search and open attribute
     * 5.Click on "Delete Attribute" button
     *
     * Expected result:
     * Attribute successfully deleted.
     * Success message: 'The product attribute has been deleted.' is displayed.
     *
     * @dataProvider data_DataName
     */
    public function test_DeleteProductAttribute_Deletable($dataName)
    {
        //Data
        $attrData = $this->loadData($dataName, null, array('attribute_code', 'admin_title'));
        $searchData = $this->loadData('attribute_search_data',
                        array(
                    'attribute_code' => $attrData['attribute_code'],
                    'attribute_lable' => $attrData['admin_title'],
                        )
        );
        //Step 1. Create attribute
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_attribute'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_attributes'),
                'After successful customer creation should be redirected to Manage Attributes page');
        //Step 2. Open attribute and delete.
        $this->productAttributeHelper()->openAttribute($searchData);
        $this->deleteElement('delete_attribute', 'delete_confirm_message');
        //Verifying
        $this->assertTrue($this->successMessage('success_deleted_attribute'), $this->messages);
    }

    public function data_DataName()
    {
        return array(
            array('product_attribute_textfield'),
            array('product_attribute_textarea'),
            array('product_attribute_date'),
            array('product_attribute_yesno'),
            array('product_attribute_multiselect'),
            array('product_attribute_dropdown'),
            array('product_attribute_price'),
            array('product_attribute_mediaimage'),
            array('product_attribute_fpt')
        );
    }

    /**
     * Delete system Product Attributes
     *
     * Steps:
     * 1.Search and open system attribute.
     *
     * Expected result:
     * "Delete Attribute" button isn't present.
     */
    public function test_ThatCannotBeDeleted_SystemAttribute()
    {
        $searchData = $this->loadData('attribute_search_data',
                        array(
                            'attribute_code' => 'price',
                            'attribute_lable' => 'Price',
                        )
        );
        //Step.
        $this->productAttributeHelper()->openAttribute($searchData);
        //Verifying
        $this->assertFalse($this->buttonIsPresent('delete_attribute'),
                '"Delete Attribute" button is present on the page');
    }

    /**
     * @TODO Waiting a tests for Configurable products
     */
    public function test_ThatCannotBeDeleted_DropdownAttributeUsedInConfigurableProduct()
    {
        $this->markTestIncomplete();
    }

}
