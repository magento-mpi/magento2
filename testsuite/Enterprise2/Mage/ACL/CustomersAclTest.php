<?php
# Magento
#
# {license_notice}
#
# @category    Magento
# @package     Mage_AdminUser
# @subpackage  functional_tests
# @copyright   {copyright}
# @license     {license_link}
#

/**
 * Creating Admin User
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_ACL_CustomersAclTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->admin('log_in_to_admin', false);
        $this->logoutAdminUser();
        $this->loginAdminUser();
    }

    /**
     * <p>Post conditions:</p>
     * <p>Log out from Backend.</p>
     */
    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>Preconditions</p>
     * <p>1. Login to backend as admin</p>
     * <p>2. Go to System>Permissions>Role and click "Add New Role" button</p>
     * <p>3. Fill "Role Name" field</p>
     * <p>4. Click Role Resource Tab</p>
     * <p>5. In Role Resources fieldset  select only one test scope checkbox[Customers>Attributes]</p>
     * <p>6. Click "Save Role" button for save roleSource</p>
     * <p>7. Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>8. Fill all required fields (User Info Tab)</p>
     * <p>9. Click User Role Tab</p>
     * <p>10. Select testRole</p>
     * <p>11. Click "Save User" button for save testAdminUser</p>
     * <p>12. Log out </p>
     * <p>Steps:</p>
     * <p>1. Log in as testAdminUser</p>
     * <p>Expected results:</p>
     * <p>1. Access only to Customers>Attributes is available.</p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-5733
     */
    public function roleResourceAccessAttributeCustomer()
    {
        //create specific role with test roleResource
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Customers/Attributes'));
        $this->adminUserHelper()->createRole($roleSource);
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->logoutAdminUser();
        //Steps
        //login as admin user with specific(test) role
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verifying  count of main menu elements
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $navigationElements = $this->getElementsByXpath($xpath);
        $this->assertEquals('1', count($navigationElements));
        //Verifying that Global Search fieldset is present or not present
        $globSearchXpath = $this->_getControlXpath('field', 'global_record_search');
        $globSearchCount = $this->getElementsByXpath($globSearchXpath, 'value');
        $this->assertEquals('0', count($globSearchCount));

        return $loginData;
    }

    /**
     *  <p>Steps:</p>
     * <p>1. Navigate to Customer>Attribute>Manage Customer Address Attributes</p>
     * <p>2. Create new customer attribute//new customer address attribute.</p>
     * <p>3. Open created customer attribute//new customer address attribute and verify that all data is correct.</p>
     * <p>Expected results:</p>
     * <p>1. After step 2: Attribute is successfully created.</p>
     * <p>2. After step 3: All data is equal to data from previous step.</p>
     *
     * @test
     *
     * @depends roleResourceAccessAttributeCustomer
     *
     * @TestlinkId TL-MAGE-5580
     */
    public function customerAddressAttributeVerifying($loginData)
    {
        $this->admin('log_in_to_admin', false);
        $this->logoutAdminUser();
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->navigate('manage_customer_address_attributes');
        //Steps
        $attrData = $this->loadDataSet('CustomerAddressAttribute', 'generic_customer_address_attribute');
        $this->attributesHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Opening
        $this->attributesHelper()
            ->openAttribute(array('attribute_code' => $attrData['properties']['attribute_code']));
        //Verifying
        $this->productAttributeHelper()->verifyAttribute($attrData);
    }

    /**
     *  <p>Steps:</p>
     * <p>1. Navigate to Customer>Attribute>Manage Customer Attributes</p>
     * <p>2. Create new customer attribute.</p>
     * <p>3. Open created customer attribute and verify that all data is correct.</p>
     * <p>Expected results:</p>
     * <p>1. After step 2: Attribute is successfully created.</p>
     * <p>2. After step 3: All data is equal to data from previous step.</p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-5580
     *
     * @depends roleResourceAccessAttributeCustomer
     */
    public function customerAttributeVerifying($loginData)
    {
        $this->admin('log_in_to_admin', false);
        $this->logoutAdminUser();
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->navigate('manage_customer_attributes');
        //Steps
        $attrData = $this->loadDataSet('CustomerAttribute', 'generic_customer_attribute');
        $this->attributesHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Opening
        $this->attributesHelper()
            ->openAttribute(array('attribute_code' => $attrData['properties']['attribute_code']));
        //Verifying
        $this->productAttributeHelper()->verifyAttribute($attrData);
    }

    /**
     * <p>Preconditions</p>
     * <p>1. Login to backend as admin</p>
     * <p>2. Go to System>Permissions>Role and click "Add New Role" button</p>
     * <p>3. Fill "Role Name" field</p>
     * <p>4. Click Role Resource Tab</p>
     * <p>5. In Role Resources fieldset  select only one test scope checkbox[Customers>Customers Segment]</p>
     * <p>6. Click "Save Role" button for save roleSource</p>
     * <p>7. Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>8. Fill all required fields (User Info Tab)</p>
     * <p>9. Click User Role Tab</p>
     * <p>10. Select testRole</p>
     * <p>11. Click "Save User" button for save testAdminUser</p>
     * <p>12. Log out </p>
     * <p>Steps:</p>
     * <p>1. Log in as testAdminUser</p>
     * <p>Expected results:</p>
     * <p>1. Manage customers segment page is available</p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-5734
     */
    public function roleResourceAccessCustomerSegment()
    {
        $this->admin('log_in_to_admin', false);
        $this->logoutAdminUser();
        $this->loginAdminUser();
        //Preconditions
        //create specific role with test roleResource
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Customers/Customer Segments'));
        $this->adminUserHelper()->createRole($roleSource);
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->logoutAdminUser();
        //Steps
        //login as admin user with specific(test) role
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_customer_segments');
        //Verifying  count of main menu elements
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $navigationElements = $this->getElementsByXpath($xpath);
        $this->assertEquals('1', count($navigationElements));
        //Verifying that Global Search fieldset is present or not present
        $globSearchXpath = $this->_getControlXpath('field', 'global_record_search');
        $globSearchCount = $this->getElementsByXpath($globSearchXpath, 'value');
        $this->assertEquals('0', count($globSearchCount));

        return $loginData;
    }

    /**
     * <p>1. Go to Customers-> Customer Segment <p>
     * <p>2. Press "Add Segment" button. <p>
     * <p>3. Fill all required fields in "General Properties" tab <p>
     * <p>4. Press "Save" button. <p>
     * <p> Expected result <p>
     * <p>1.In the top area the message "The segment has been saved." appears. <p>
     * <p>2. New row is presented in Customer Segment grid. <p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-1827
     *
     * @depends roleResourceAccessCustomerSegment
     */
    public function withAllFieldsSegment($loginData)
    {
        $this->admin('log_in_to_admin', false);
        $this->logoutAdminUser();
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->navigate('manage_customer_segments');
        $segmData = 'name_' . $this->generate('string', 16, ':lower:');
        $this->customerSegmentHelper()->createSegment($segmData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_segment');
        //Opening and verifying
        $this->customerSegmentHelper()->openSegment(array('segment_name' => $segmData['segment_name']));
    }

    /**
     * <p>Preconditions</p>
     * <p>1. Login to backend as admin</p>
     * <p>2. Go to System>Permissions>Role and click "Add New Role" button</p>
     * <p>3. Fill "Role Name" field</p>
     * <p>4. Click Role Resource Tab</p>
     * <p>5. In Role Resources fieldset  select only one test scope checkbox[Customers>Invitation]</p>
     * <p>6. Click "Save Role" button for save roleSource</p>
     * <p>7. Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>8. Fill all required fields (User Info Tab)</p>
     * <p>9. Click User Role Tab</p>
     * <p>10. Select testRole</p>
     * <p>11. Click "Save User" button for save testAdminUser</p>
     * <p>12. Log out </p>
     * <p>Steps:</p>
     * <p>1. Log in as testAdminUser</p>
     * <p>Expected results:</p>
     * <p>1. Manage customers invitations page is available</p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-5735
     */
    public function roleResourceAccessCustomerInvitations()
    {
        //Preconditions
        //create specific role with test roleResource
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Customers/Invitations'));
        $this->adminUserHelper()->createRole($roleSource);
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->logoutAdminUser();
        //Steps
        //login as admin user with specific(test) role
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_invitations');
        //Verifying  count of main menu elements
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $navigationElements = $this->getElementsByXpath($xpath);
        $this->assertEquals('1', count($navigationElements));
        //Verifying that Global Search fieldset is present or not present
        $globSearchXpath = $this->_getControlXpath('field', 'global_record_search');
        $globSearchCount = $this->getElementsByXpath($globSearchXpath, 'value');
        $this->assertEquals('0', count($globSearchCount));

        return $loginData;
    }

    /**
     * <p>1.Navigate to Customers> Invitations.<p>
     * <p>2. Click "Add Invitations" button. <p>
     * <p>3. Add valid email in to "Enter Each Email on New Line" <p>
     * <p>4. Click "Save" button. <p>
     * <p>Expected result<p>
     * <p>1. The invitation is successfully saved. <p>
     * <p>2. The message "1 invitation(s) were sent." is presented. <p>
     *
     * @test
     *
     * @depends roleResourceAccessCustomerInvitations
     *
     * @TestlinkId TL-MAGE-5736
     */
    public function createInvitations($loginData)
    {
        $this->admin('log_in_to_admin', false);
        $this->logoutAdminUser();
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->navigate('manage_invitations');
        $this->clickButton('add_invitations');
        $this->validatePage('new_invitations');
        $this->fillField('email_on_new_line', $this->generate('email', 128, 'valid'));
        $this->clickButton('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_send');
    }

    /**
     * <p>Preconditions</p>
     * <p>1. Login to backend as admin</p>
     * <p>2. Go to System>Permissions>Role and click "Add New Role" button</p>
     * <p>3. Fill "Role Name" field</p>
     * <p>4. Click Role Resource Tab</p>
     * <p>5. In Role Resources fieldset  select only one test scope checkbox[Customers>Gift Registry]</p>
     * <p>6. Click "Save Role" button for save roleSource</p>
     * <p>7. Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>8. Fill all required fields (User Info Tab)</p>
     * <p>9. Click User Role Tab</p>
     * <p>10. Select testRole</p>
     * <p>11. Click "Save User" button for save testAdminUser</p>
     * <p>12. Log out </p>
     * <p>Steps:</p>
     * <p>1. Log in as testAdminUser</p>
     * <p>Expected results:</p>
     * <p>1. Manage gift registry page is available</p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-5737
     */
    public function roleResourceAccessGiftRegistry()
    {
        //Preconditions
        //create specific role with test roleResource
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Customers/Gift Registry'));
        $this->adminUserHelper()->createRole($roleSource);
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->logoutAdminUser();
        //Steps
        //login as admin user with specific(test) role
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_gift_registry');
        //Verifying  count of main menu elements
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $navigationElements = $this->getElementsByXpath($xpath);
        $this->assertEquals('1', count($navigationElements));
        //Verifying that Global Search fieldset is present or not present
        $globSearchXpath = $this->_getControlXpath('field', 'global_record_search');
        $globSearchCount = $this->getElementsByXpath($globSearchXpath, 'value');
        $this->assertEquals('0', count($globSearchCount));
    }

    /**
     * <p>Preconditions</p>
     * <p>1. Login to backend as admin</p>
     * <p>2. Go to System>Permissions>Role and click "Add New Role" button</p>
     * <p>3. Fill "Role Name" field</p>
     * <p>4. Click Role Resource Tab</p>
     * <p>5. In Role Resources fieldset  select only one test scope checkbox[Customers>Gift Card Account]</p>
     * <p>6. Click "Save Role" button for save roleSource</p>
     * <p>7. Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>8. Fill all required fields (User Info Tab)</p>
     * <p>9. Click User Role Tab</p>
     * <p>10. Select testRole</p>
     * <p>11. Click "Save User" button for save testAdminUser</p>
     * <p>12. Log out </p>
     * <p>Steps:</p>
     * <p>1. Log in as testAdminUser</p>
     * <p>Expected results:</p>
     * <p>1. Manage gift card account page is available </p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-5738
     */
    public function roleResourceAccessGiftCardAccount()
    {
        //Preconditions
        //create specific role with test roleResource
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Customers/Gift Card Accounts'));
        $this->adminUserHelper()->createRole($roleSource);
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->logoutAdminUser();
        //Steps
        //login as admin user with specific(test) role
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_gift_card_account');
        //Verifying  count of main menu elements
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $navigationElements = $this->getElementsByXpath($xpath);
        $this->assertEquals('1', count($navigationElements));
        //Verifying that Global Search fieldset is present or not present
        $globSearchXpath = $this->_getControlXpath('field', 'global_record_search');
        $globSearchCount = $this->getElementsByXpath($globSearchXpath, 'value');
        $this->assertEquals('0', count($globSearchCount));
    }

    /**
     * <p>Preconditions</p>
     * <p>1. Login to backend as admin</p>
     * <p>2. Go to System>Permissions>Role and click "Add New Role" button</p>
     * <p>3. Fill "Role Name" field</p>
     * <p>4. Click Role Resource Tab</p>
     * <p>5. In Role Resources fieldset  select only one test scope checkbox[Customers>Reward Exchange Rates ]</p>
     * <p>6. Click "Save Role" button for save roleSource</p>
     * <p>7. Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>8. Fill all required fields (User Info Tab)</p>
     * <p>9. Click User Role Tab</p>
     * <p>10. Select testRole</p>
     * <p>11. Click "Save User" button for save testAdminUser</p>
     * <p>12. Log out </p>
     * <p>Steps:</p>
     * <p>1. Log in as testAdminUser</p>
     * <p>Expected results:</p>
     * <p>1. Manage reward point exchange rates page is available </p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-5739
     */
    public function roleResourceAccessRewardPoints()
    {
        //Preconditions
        //create specific role with test roleResource
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Customers/Reward Exchange Rates'));
        $this->adminUserHelper()->createRole($roleSource);
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->logoutAdminUser();
        //Steps
        //login as admin user with specific(test) role
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_reward_rates');
        //Verifying  count of main menu elements
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $navigationElements = $this->getElementsByXpath($xpath);
        $this->assertEquals('1', count($navigationElements));
        //Verifying that Global Search fieldset is present or not present
        $globSearchXpath = $this->_getControlXpath('field', 'global_record_search');
        $globSearchCount = $this->getElementsByXpath($globSearchXpath, 'value');
        $this->assertEquals('0', count($globSearchCount));
    }
}

