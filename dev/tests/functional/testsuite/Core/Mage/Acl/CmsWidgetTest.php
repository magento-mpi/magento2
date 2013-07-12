<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Acl
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * ACL tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Acl_CmsWidgetTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->admin('log_in_to_admin');
    }

    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>Preconditions</p>
     * <p>Create Admin User with full CMS widget resources role</p>
     *
     * @return array
     * @test
     */
    public function roleResourceAccessCmsWidget()
    {
        //create specific role with test roleResource
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'content-elements-frontend_apps'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        return array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
    }

    /**
     * <p>Admin with Resource: CMS widget has access to CMS/widgets menu. All necessary elements are presented</p>
     *
     * @param $loginData
     *
     * @depends roleResourceAccessCmsWidget
     * @test
     * @TestlinkId TL-MAGE-6160
     */
    public function verifyScopeCmsWidgetOneRoleResource($loginData)
    {
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_cms_widgets'), $this->getParsedMessages());
        // Verify that navigation menu has only 1 parent element
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify that navigation menu has only 1 child elements
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_children_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify  that necessary elements are present on page
        $elements = $this->loadDataSet('CmsWidgetElements', 'manage_cms_widget_elements');
        $resultElementsArray = array();
        foreach ($elements as $key => $value) {
            $resultElementsArray = array_merge($resultElementsArray, (array_fill_keys(array_keys($value), $key)));
        }
        foreach ($resultElementsArray as $elementName => $elementType) {
            if (!$this->controlIsVisible($elementType, $elementName)) {
                $this->addVerificationMessage(
                    "Element type = '$elementType' name = '" . $elementName . "' is not present on the page");
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Admin with Resource: CMS/Widgets can create new widget with all fielded fields</p>
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Click "Add New Widget Instance" button</p>
     * <p>3. Create widget with all required field.</p>
     * <p>Expected results:</p>
     * <p>1. Widget is created</p>
     * <p>2. Success Message is appeared "The widget has been saved."</p>
     *
     * @param array $loginData
     *
     * @depends roleResourceAccessCmsWidget
     * @return array
     * @test
     * @TestlinkId TL-MAGE-6159
     */
    public function createNewWidget($loginData)
    {
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_cms_widgets'), $this->getParsedMessages());
        $widgetData = $this->loadDataSet('CmsWidget', 'cms_page_link_widget_req');
        $this->navigate('manage_cms_widgets');
        $this->cmsWidgetsHelper()->createWidget($widgetData);
        //Verifying
        $this->assertMessagePresent('success', 'successfully_saved_widget');
        return array(
            'filter_type' => $widgetData['settings']['type'],
            'filter_title' => $widgetData['frontend_properties']['widget_instance_title']
        );
    }

    /**
     * <p>Admin with Resource: CMS/Widgets can edit cms widget and save using "Save and Continue Edit" button</p>
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Find newly created test cms widget in grid and click</p>
     * <p>3. Create widget with all required field</p>
     * <p>4. Click "Save and Continue Edit" button</p>
     * <p>Expected results:</p>
     * <p>1. Widget is saved</p>
     * <p>2. Success Message is appeared "The widget has been saved."</p>
     *
     * @param $loginData
     * @param $widgetToDelete
     *
     * @depends roleResourceAccessCmsWidget
     * @depends createNewWidget
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-6158
     */
    public function editWidget($loginData, $widgetToDelete)
    {
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_cms_widgets'), $this->getParsedMessages());
        $this->cmsWidgetsHelper()->openWidget($widgetToDelete);
        $this->fillField('sort_order', '1');
        $this->saveAndContinueEdit('button', 'save_and_continue_edit');
        //Verifying
        $this->assertMessagePresent('success', 'successfully_saved_widget');
    }

    /**
     * <p>Admin with Resource: CMS/Widget can delete cms widget</p>
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Find test cms widget in grid and click</p>
     * <p>3. Click "Delete" button</p>
     * <p>4. Click "OK" button for confirm action</p>
     * <p>Expected results:</p>
     * <p>1. Widget is deleted</p>
     * <p>2. Success Message is appeared "The widget has been deleted."</p>
     *
     * @param $loginData
     * @param $widgetToDelete
     *
     * @depends roleResourceAccessCmsWidget
     * @depends createNewWidget
     *
     * @test
     * @TestlinkId TL-MAGE-6157
     */
    public function deleteNewWidget($loginData, $widgetToDelete)
    {
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_cms_widgets'), $this->getParsedMessages());
        $this->cmsWidgetsHelper()->deleteWidget($widgetToDelete);
        //Verifying
        $this->assertMessagePresent('success', 'successfully_deleted_widget');
    }
}
