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
     * API Roles Action Log
     *
     * @package     selenium
     * @subpackage  tests
     * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     */
class Enterprise2_Mage_AdminActionLog_CreateTest extends Mage_Selenium_TestCase
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
     * <p>1. Create API Role</p>
     * <p>2. Open Admin Actions Logs page</p>
     * <p>3. Check that Save action log is created
     * <p>Expected result:</p>
     * <p>Save action log is created</p>
     *
     * @test
     * @author Michael Banin
     * @TestlinkId TL-MAGE-6377
     */
    public function saveActionLog()
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
        $roleId = ($this->defineParameterFromUrl('role_id'));
        //Open Admin Actions Logs page
        $this->navigate('admin_action_log_report');
        //Use filter with Role data and open it
        $userSearch =array('filter_role_name' => $roleId, 'action' => 'Save');
        $this->searchAndOpen($userSearch);
        //Check that log info page is opened
        $this->assertSame($this->getTitle(),
            'View Entry / Report / Admin Actions Logs / System / Magento Admin', 'Wrong page');
    }

    /**
     * <p>API Role Required fields</p>
     * <p>Steps</p>
     * <p>1. Create API Role and delete it</p>
     * <p>2. Open Admin Actions Logs page</p>
     * <p>3. Check that Save action log is created
     * <p>Expected result:</p>
     * <p>Save action log is created</p>
     *
     * @test
     * @author Michael Banin
     * @TestlinkId TL-MAGE-6379
     */
    public function deleteActionLog()
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
        $roleId = ($this->defineParameterFromUrl('role_id'));
        //Click Delete API Role button
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete', true);
        //Verify that message "The role has been deleted." is displayed
        $this->assertMessagePresent('success', 'success_deleted_role');
        //Open Admin Actions Logs page
        $this->navigate('admin_action_log_report');
        //Use filter with Role data and open it
        $userSearch =array('filter_role_name' => $roleId, 'action' => 'Delete');
        $this->searchAndOpen($userSearch);
        //Check that log info page is opened
        $this->assertSame($this->getTitle(),
            'View Entry / Report / Admin Actions Logs / System / Magento Admin', 'Wrong page');
    }

    /**
     * <p>API Role Required fields</p>
     * <p>Steps</p>
     * <p>1. Create API Role and open it from roles grid for edit</p>
     * <p>2. Open Admin Actions Logs page</p>
     * <p>3. Check that Save action log is created
     * <p>Expected result:</p>
     * <p>Save action log is created</p>
     *
     * @test
     * @author Michael Banin
     * @TestlinkId TL-MAGE-6378
     */
    public function editActionLog()
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
        $this->refresh();
        $roleId = ($this->defineParameterFromUrl('role_id'));
        //Open Admin Actions Logs page
        $this->navigate('admin_action_log_report');
        //Use filter with Role data and open it
        $userSearch =array('filter_role_name' => $roleId, 'action' => 'Edit');
        $this->searchAndOpen($userSearch);
        //Check that log info page is opened
        $this->assertSame($this->getTitle(),
            'View Entry / Report / Admin Actions Logs / System / Magento Admin', 'Wrong page');
    }
}