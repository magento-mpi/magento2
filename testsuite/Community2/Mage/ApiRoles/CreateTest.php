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
 * API Roles Admin Page
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_ApiRoles_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $windowQty = $this->getAllWindowNames();
        if (count($windowQty) > 1 && end($windowQty) != 'null') {
            $this->selectWindow("name=" . end($windowQty));
            $this->close();
            $this->selectWindow(null);
        }
    }

    /**
     * <p>API Role Required fields</p>
     * <p>Steps</p>
     * <p>1. Click Add New Role button</p>
     * <p>2. Click Save API Role button</p>
     * <p>Expected result:</p>
     * <p>API Role is not created</p>
     * <p>Message that Role name is required field is appear</p>
     *
     * @test
     * @author Michael Banin
     * @TestlinkId TL-MAGE-6358
     */
    public function requiredFields()
    {
        //Open API Roles Management page
        $this->navigate('api_roles_management');
        //Click Add New Role button
        $this->clickButton('add_new_role', true);
        //Click Save API Role button
        $this->clickButton('save', false);
        //Verify that validation message appear
        $xpath = $this->_getControlXpath('field', 'role_name');
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('error', 'empty_required_field');
    }

    /**
     * <p>Create API Role (Full Access)</p>
     * <p>Steps</p>
     * <p>1. Click Add New Role button</p>
     * <p>2. Fill Role name field</p>
     * <p>3. Click resources tab</p>
     * <p>4. At Resources tab select All at Resource Access</p>
     * <p>5. Click Save API role</p>
     * <p>Expected result:</p>
     * <p>API Role is created</p>
     * <p>Message "The API role has been saved." is displayed</p>
     *
     * @test
     * @author Michael Banin
     * @TestlinkId TL-MAGE-6291
     */
    public function roleWithAllAccess()
    {
        //Load data
        $fieldData = $this->loadDataSet('ApiRoles', 'api_role_new');
        //Open API Roles Management page
        $this->navigate('api_roles_management');
        //Click Add New Role button
        $this->clickButton('add_new_role', true);
        //Fill Role name field
        $this->fillField('role_name', $fieldData['role_name']);
        //Open Resources Tab
        $this->openTab('resources');
        //Selecting All at Role Access Dropdown
        $this->fillDropdown('role_access', 'All');
        //Saving API Role
        $this->clickButton('save');
        //Verify that role is saved
        $this->assertMessagePresent('success', 'success_saved_role');
    }

    /**
     * <p>Create API Role (Custom Access)</p>
     * <p>Steps</p>
     * <p>1. Click Add New Role button</p>
     * <p>2. Fill Role name field</p>
     * <p>3. Click resources tab</p>
     * <p>4. At Resources tab select Custom at Resource Access</p>
     * <p>5. Open Resources tab and check that Get Customer checkbox are set</p>
     * <p>6. Click Save API role</p>
     * <p>Expected result:</p>
     * <p>API Role is created</p>
     * <p>Message "The API role has been saved." is displayed</p>
     *
     * @test
     * @author Michael Banin
     * @TestlinkId TL-MAGE-6292
     */
    public function roleWithCustomAccess()
    {
        $fieldData = $this->loadDataSet('ApiRoles', 'api_role_new');
        //Open API Roles Management page
        $this->navigate('api_roles_management');
        //Click Add New Role button
        $this->clickButton('add_new_role', true);
        //Fill Role name field
        $this->fillField('role_name', $fieldData['role_name']);
        //Open Resources Tab
        $this->openTab('resources');
        //Selecting Custom at Role Access Dropdown
        $this->fillDropdown('role_access', 'Custom');
        //Selecting Get Customer checkbox
        $this->addParameter('subName', 'Create');
        $this->clickControl('link', 'sub_root', false);
        //Saving API Role
        $this->clickButton('save');
        //Verify that role is saved
        $this->assertMessagePresent('success', 'success_saved_role');
    }

    /**
     * <p>Delete API Role</p>
     * <p>Steps</p>
     * <p>1. Open created role</p>
     * <p>2. Click Delete API Roles button</p>
     * <p>3. Verify that success deleted message appear</p>
     * <p>Expected result:</p>
     * <p>The role has been deleted.</p>
     * <p>Message "The role has been deleted." is displayed</p>
     *
     * @test
     * @author Michael Banin
     * @TestlinkId TL-MAGE-6366
     */
    public function roleDelete()
    {
        //Load data
        $fieldData = $this->loadDataSet('ApiRoles', 'api_role_new');
        //Open API Roles Management page
        $this->navigate('api_roles_management');
        //Click Add New Role button
        $this->clickButton('add_new_role', true);
        //Fill Role name field
        $this->fillField('role_name', $fieldData['role_name']);
        //Open Resources Tab
        $this->openTab('resources');
        //Selecting All at Role Access Dropdown
        $this->fillDropdown('role_access', 'All');
        //Saving API Role
        $this->clickButton('save');
        //Verify that role is saved
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->clickButton('save');
        //Open created role from the role grid
        $userSearch =array('filter_role_name' => $fieldData['role_name']);
        $this->searchAndOpen($userSearch);
        //Click Delete API Role button
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete', true);
        //Verify that message "The role has been deleted." is displayed
        $this->assertMessagePresent('success', 'success_deleted_role');
    }
}
