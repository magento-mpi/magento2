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
 * Delete SMOKE product attributes created by ProductAttribute_Create_SmokeTest.
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductAttribute_DeleteTest extends Mage_Selenium_TestCase
{

    /*
     * Preconditions
     * Admin user should be logged in.
     * Should stay on the Admin Dashboard page after login
     */
    protected function assertPreConditions()
    {
        $this->assertTrue($this->loginAdminUser());
        $this->assertTrue($this->admin('dashboard'));
        $this->addParameter('id', 0);
    }

    /**
     * @TODO
     */
    public function testNavigation()
    {
      // @TODO
    }

    /**
     * @TODO
     */
    public function testDeleteProductAttribute_Smoke()
    {
        $this->assertTrue($this->navigate('manage_attributes'), 'Wrong page is displayed');
        $attrData = $this->loadData('product_attribute_smoke_del', null,null);
        $this->assertTrue($this->searchAndOpen($attrData), 'Element not found.');
        $this->assertTrue($this->deleteElement('delete', 'delete_confirm_message'), $this->messages);
        $this->assertFalse($this->successMessage('success_deleted_attribute'), $this->messages);
    }

    /**
     * @TODO
     */
    public function test_Deletable()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_ThatCannotBeDeleted_SystemAttribute()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_ThatCannotBeDeleted_DropdownAttributeUsedInConfigurableProduct()
    {
        // @TODO
    }
}
