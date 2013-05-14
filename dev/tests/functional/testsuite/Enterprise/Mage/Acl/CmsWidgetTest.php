<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ACL
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */
class Enterprise_Mage_Acl_CmsWidgetTest extends Mage_Selenium_TestCase
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
     * <p>Create Admin User with full CMS widget resources role and role scope: Main Website</p>
     *
     * @return array
     * @test
     */
    public function roleResourceAccessCmsWidget()
    {
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom_website',
            array('resource_acl' => 'content-elements-frontend_apps'));
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with specific role
        $this->navigate('manage_admin_users');
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
                $this->addVerificationMessage('Element type="' . $elementType . '" name="' . $elementName
                    . '" is not present on the page');
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Admin with Resource: CMS/Widgets and scope: Main Website can create new widget with all fielded fields</p>
     *
     * @param array $loginData
     *
     * @depends roleResourceAccessCmsWidget
     * @return array
     * @test
     * @TestlinkId TL-MAGE-6156
     */
    public function createNewWidgetOneWebsite($loginData)
    {
        $widgetData = $this->loadDataSet('CmsWidget', 'cms_page_link_widget_req',
            array('assign_to_store_views' => 'Default Store View'));
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_cms_widgets'), $this->getParsedMessages());
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
     *
     * @param $loginData
     * @param $widgetToDelete
     *
     * @depends roleResourceAccessCmsWidget
     * @depends createNewWidgetOneWebsite
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
     *
     * @param $loginData
     * @param $widgetToDelete
     *
     * @depends roleResourceAccessCmsWidget
     * @depends createNewWidgetOneWebsite
     *
     * @test
     * @TestlinkId TL-MAGE-6157
     */
    public function deleteNewWidget($loginData, $widgetToDelete)
    {
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_cms_widgets'), $this->getParsedMessages());
        $this->cmsWidgetsHelper()->openWidget($widgetToDelete);;
        $this->assertFalse($this->controlIsVisible('button', 'delete'),
            "This user doesn't have permission to delete pull.");
    }
}

