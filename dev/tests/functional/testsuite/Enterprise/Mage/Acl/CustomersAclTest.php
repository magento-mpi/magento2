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

/**
 * Creating Admin User
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_Acl_CustomersAclTest extends Mage_Selenium_TestCase
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
     *
     * @test
     * @TestlinkId TL-MAGE-5733
     */
    public function roleResourceAccessAttributeCustomer()
    {
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'stores-attributes-customer_attributes'));
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        //create specific role with test roleResource
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->logoutAdminUser();
        //Steps
        //login as admin user with specific(test) role
        $this->adminUserHelper()->loginAdmin($loginData);
        // Verify that navigation menu has only 1 parent element
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify that navigation menu has only 2 child elements
        $this->assertEquals(2, $this->getControlCount('pageelement', 'navigation_children_menu_items'),
            'Count of Top Navigation Menu elements not equal 2, should be equal');
        //Verifying that Global Search fieldset is present or not present
        $this->assertFalse($this->controlIsPresent('field', 'global_record_search'), 'Global Search is on the page');

        return $loginData;
    }

    /**
     * @test
     * @depends roleResourceAccessAttributeCustomer
     * @TestlinkId TL-MAGE-5580
     */
    public function customerAddressAttributeVerifying($loginData)
    {
        $attrData = $this->loadDataSet('CustomerAddressAttribute', 'customer_address_attribute_attach_file');
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->navigate('manage_customer_address_attributes');
        $this->attributesHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Opening
        $this->attributesHelper()->openAttribute(array('attribute_code' => $attrData['properties']['attribute_code']));
        //Verifying
        $this->productAttributeHelper()->verifyAttribute($attrData);
    }

    /**
     * @test
     * @depends roleResourceAccessAttributeCustomer
     * @TestlinkId TL-MAGE-5580
     */
    public function customerAttributeVerifying($loginData)
    {
        $attrData = $this->loadDataSet('CustomerAttribute', 'customer_attribute_attach_file');
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->navigate('manage_customer_attributes');
        $this->attributesHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Opening
        $this->attributesHelper()->openAttribute(array('attribute_code' => $attrData['properties']['attribute_code']));
        //Verifying
        $this->productAttributeHelper()->verifyAttribute($attrData);
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-5734
     */
    public function roleResourceAccessCustomerSegment()
    {
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'customers-segments'));
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        //Preconditions
        //create specific role with test roleResource
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->logoutAdminUser();
        //Steps
        //login as admin user with specific(test) role
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_customer_segments'), $this->getParsedMessages());
        // Verify that navigation menu has only 1 parent element
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify that navigation menu has only 1 child elements
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_children_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        //Verifying that Global Search fieldset is present or not present
        $this->assertFalse($this->controlIsPresent('field', 'global_record_search'), 'Global Search is on the page');

        return $loginData;
    }

    /**
     * @test
     * @depends roleResourceAccessCustomerSegment
     * @TestlinkId TL-MAGE-1827
     */
    public function withAllFieldsSegment($loginData)
    {
        $segmentData = $this->loadDataSet('CustomerSegment', 'segm_with_website');
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_customer_segments'), $this->getParsedMessages());
        $this->customerSegmentHelper()->createSegment($segmentData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_segment');
        //Opening and verifying
        $this->customerSegmentHelper()->openSegment(array(
                'segment_name' => $segmentData['general_properties']['segment_name'])
        );
        $this->assertTrue($this->verifyForm($segmentData, 'general_properties'), $this->getParsedMessages());
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-5735
     */
    public function roleResourceAccessCustomerInvitations()
    {
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'marketing-private_sales-invitations'));
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        //Preconditions
        //create specific role with test roleResource
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->logoutAdminUser();
        //Steps
        //login as admin user with specific(test) role
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_invitations'), $this->getParsedMessages());
        // Verify that navigation menu has only 1 parent element
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify that navigation menu has only 1 child elements
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_children_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        //Verifying that Global Search fieldset is present or not present
        $this->assertFalse($this->controlIsPresent('field', 'global_record_search'), 'Global Search is on the page');

        return $loginData;
    }

    /**
     * @test
     * @depends roleResourceAccessCustomerInvitations
     * @TestlinkId TL-MAGE-5736
     */
    public function createInvitations($loginData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_invitations'), $this->getParsedMessages());
        $this->clickButton('add_invitations');
        $this->assertTrue($this->checkCurrentPage('new_invitations'), $this->getParsedMessages());
        $this->fillField('email_on_new_line', $this->generate('email', 50, 'valid'));
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_send');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-5737
     */
    public function roleResourceAccessGiftRegistry()
    {
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'stores-other_settings-gift_registry'));
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        //Preconditions
        //create specific role with test roleResource
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->logoutAdminUser();
        //Steps
        //login as admin user with specific(test) role
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_gift_registry'), $this->getParsedMessages());
        // Verify that navigation menu has only 1 parent element
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify that navigation menu has only 1 child elements
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_children_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        //Verifying that Global Search fieldset is present or not present
        $this->assertFalse($this->controlIsPresent('field', 'global_record_search'), 'Global Search is on the page');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-5738
     */
    public function roleResourceAccessGiftCardAccount()
    {
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'marketing-promotions-gift_card_accounts'));
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        //Preconditions
        //create specific role with test roleResource
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->logoutAdminUser();
        //Steps
        //login as admin user with specific(test) role
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_gift_card_account'), $this->getParsedMessages());
        // Verify that navigation menu has only 1 parent element
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify that navigation menu has only 1 child elements
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_children_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        //Verifying that Global Search fieldset is present or not present
        $this->assertFalse($this->controlIsPresent('field', 'global_record_search'), 'Global Search is on the page');
    }

    /**
     *
     * @test
     *
     * @TestlinkId TL-MAGE-5739
     */
    public function roleResourceAccessRewardPoints()
    {
        $this->markTestIncomplete('MAGETWO-8404');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'stores-other_settings-reward_exchange_rates'));
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $rewardData = $this->loadDataSet('RewardPoint', 'reward_point_rate');
        //Preconditions
        //create specific role with test roleResource
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->logoutAdminUser();
        //Steps
        //login as admin user with specific(test) role
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_reward_rates'), $this->getParsedMessages());
        // Verify that navigation menu has only 1 parent element
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify that navigation menu has only 1 child elements
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_children_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        //Verifying that Global Search fieldset is present or not present
        $this->assertFalse($this->controlIsPresent('field', 'global_record_search'), 'Global Search is on the page');
        $this->clickButton('add_new_rate');
        $this->assertTrue($this->checkCurrentPage('new_reward_rate'), $this->getParsedMessages());
        $this->fillField('rate_value', $rewardData['reward_rate_properties']['rate_value']);
        $this->fillField('rate_equal_value', $rewardData['reward_rate_properties']['rate_equal_value']);
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_rate');

        $this->_prepareDataForSearch($rewardData);
        $xpathTR = $this->search($rewardData, 'reward_point_grid');
        $this->assertNotNull($xpathTR, 'Reward rate is not found');
        $cellId = $this->getColumnIdByName('ID');
        $param = $this->getElementsValue($xpathTR . '//td[' . $cellId . ']', 'text');
        $this->addParameter('id', end($param));
        $this->addParameter('elementTitle', '#' . end($param));
        $element = $this->getElement($xpathTR . '//td[' . $cellId . ']');
        $element->click();
        $this->waitForPageToLoad();
        $this->validatePage();
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete');
        $this->assertMessagePresent('success', 'success_delete_rate');
    }
}
