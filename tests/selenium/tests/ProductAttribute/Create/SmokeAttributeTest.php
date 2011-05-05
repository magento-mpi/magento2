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
 * Create SMOKE product attributes for ProductAttribute_DeleteTest.
 * Types: textfield
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductAttribute_Create_SmokeTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions
     * Admin user should be logged in.
     * Should stay on the Admin Dashboard page after login
     */
    protected function assertPreConditions()
    {
        $this->assertTrue($this->loginAdminUser());
        $this->assertTrue($this->admin('dashboard'));
    }


    /**
     * Action_helper method for Create Attribute action
     *
     * @param array $attrData Array which contains DataSet for filling of the current form
     *
     */
    public function creteAttribute($attrData)
    {
        $this->clickButton('add_new_attribute');
        $this->fillForm($attrData, 'properties');
        $this->clickControl('tab', 'manage_lables_options',false);
        $this->fillForm($attrData, 'manage_lables_options');
    }

    /**
     * @TODO
     */
    public function testNavigation()
    {
      // @TODO
    }

    /**
     * Create "Date" type Product Attribute (required fields only)
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Date" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Fill all required fields
     * 5.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Date" type] successfully created.
     *                  Success message: 'The product attribute has been saved.' is displayed.
     */
    public function test_smokeWithRequiredFieldsOnly()
    {
        $this->assertTrue($this->navigate('manage_attributes'),'Wrong page is displayed');
        $attrData = $this->loadData('product_attribute_smoke_add', null, null);
        $this->creteAttribute($attrData);
        $this->clickButton('save_attribute',true);
        $this->assertFalse($this->successMessage('success_saved_attribute'), $this->messages);
    }
}