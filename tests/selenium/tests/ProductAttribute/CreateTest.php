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
 * Test Product Attribute Creation
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ProductAttributesCreateTest extends Mage_Selenium_TestCase
{
    /*
     * Preconditions
     * Admin user should be logged in.
     * Should stays on the Admin Dashboard page after login
     */
    protected function assertPreConditions()
    {
        $this->assertTrue($this->loginAdminUser());
        $this->assertTrue($this->admin('dashboard'));
    }

    public function testNavigation()
    {
        $this->assertTrue($this->navigate('manage_attributes'));
        $this->assertTrue($this->clickButton('add_new_attribute'), 'There is no "Add New Attribute" button on the page');
        $this->assertTrue($this->navigated('new_product_attribute'), 'Wrong page is displayed');
        $this->assertTrue($this->navigate('new_product_attribute'), 'Wrong page is displayed when accessing direct URL');        
        $this->assertTrue($this->controlIsPresent('field','attribute_code'), 'There is no "Attribute Code" field on the page');
        $this->assertTrue($this->controlIsPresent('field','apply_to'), 'There is no "Apply To" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('field','admin_title'), 'There is no "Admin Title" field on the page');
    }

    public function testAddNewProductAttribute_Smoke()
    {
        $this->assertTrue(
            $this->navigate('manage_attributes')->clickButton('add_new_attribute')->navigated('new_product_attribute'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->data('new_product_attribute', null, 'attribute_code'));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }
}