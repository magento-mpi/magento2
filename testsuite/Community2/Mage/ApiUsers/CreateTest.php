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
     * Xml Sitemap Admin Page
     *
     * @package     selenium
     * @subpackage  tests
     * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     */
class Community2_Mage_ApiUsers_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>1. Login to Admin page</p>
     * <p>2. Disable Secret key</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Create API User</p>
     * <p>Steps</p>
     * <p>1. Click "Add new API Users button</p>
     * <p>2. Fill User Name, API Secret, User Role fields</p>
     * <p>3. Push "Saave API user" button
     * <p>Expected result:</p>
     * <p>New API User Created</p>
     *
     * @return array
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-6296
     */
    public function withRequiredFieldsCreateUser ()
    {
        //Create new Role
        $productData = $this->loadDataSet('ApiUsers', 'new_api_users_create');
        $this->navigate('api_roles_management');
        $this->clickButton('add_new_role', true);
        $this->fillField('role_name', $productData['role_name']);
        $this->openTab('resources');
        $this->fillDropdown('role_access', 'All');
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_role');

        //Create Data and open APi Users page
        $this->navigate('api_users');
        $this->clickButton('add_new_api_users', true);
        $this->fillField('api_user_name', $productData['api_user_name']);
        $this->fillField('api_user_secret', $productData['api_user_secret']);
        $this->fillDropdown('api_user_role', $productData['role_name']);

        $this->clickButton('save', true);
        $this->assertMessagePresent('success', 'success_user_saved');

        return $productData;
    }

    /**
     * <p>Create API User</p>
     * <p>Steps</p>
     * <p>1. Click "Add new API Users button</p>
     * <p>2. Create duplicate of some User</p>
     * <p>3. Push "Save API user" button
     * <p>Expected result:</p>
     * <p>"User Name already exists' massage should be appear</p>
     *
     * @param array $userData
     * @test
     * @author denis.poloka
     * @depends withRequiredFieldsCreateUser
     * @TestlinkId TL-MAGE-6359
     */
    public function withRequiredFieldsDefaultValue ($userData)
    {
        $this->navigate('api_users');
        $this->clickButton('add_new_api_users', true);
        $this->fillField('api_user_name', $userData['api_user_name']);
        $this->fillField('api_user_secret', $userData['api_user_secret']);
        $this->fillDropdown('api_user_role', $userData['role_name']);

        $this->clickButton('save');
        $this->assertMessagePresent('err', 'user_exist');
    }
}