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
 * Create new product attribute. Type: Date
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductAttribute_Create_CreateFromProductPage extends Mage_Selenium_TestCase
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
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->addParameter('id', 0);
    }

    /**
     * Checking of attributes creation functionality during product createion process
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Products
     * 2.Click on "Add Product" button
     * 3.Specify settings for product creation
     * 3.1.Select "Attribute Set"
     * 3.2.Select "Product Type"
     * 4.Click on "Continue" button
     * 5.Click on "Create New Attribute" button in the top of "General" fieldset under "General" tab
     * 6.Choose attribute type in 'Catalog Input Type for Store Owner' dropdown
     * 7.Fill all required fields.
     * 8.Click on "Save Attribute" button
     *
     * Expected result:
     * New attribute successfully created.
     * Success message: 'The product attribute has been saved.' is displayed.
     * Pop-up window is closed automatically
     *
     * @dataProvider data_attributeTypes
     */
    public function test_OnProductPage_WithRequiredFieldsOnly($attributeType)
    {
        //Data
        $productSettings = $this->loadData('settings_simple');
        $attrData = $this->loadData($attributeType, null, array('attribute_code', 'admin_title'));
        //Steps
        $this->clickButton('add_new_product');
        $this->productHelper()->fillProductSettings($productSettings);
        $this->productAttributeHelper()->createAttributeOnGeneralTab($attrData);
        //Verifying
        $this->selectWindow(null);
        $this->assertElementPresent("//*[contains(@id,'" . $attrData['attribute_code'] . "')]");
    }

    public function data_attributeTypes()
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

    protected function tearDown()
    {
        $a = $this->getAllWindowNames();
        foreach ($a as $value) {
            if ($value == 'new_attribute') {
                $this->selectWindow("name=" . $value);
                $this->close();
                $this->selectWindow(null);
            }
        }
    }

}
