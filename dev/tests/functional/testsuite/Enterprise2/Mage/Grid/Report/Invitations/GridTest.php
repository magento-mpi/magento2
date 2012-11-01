<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_GiftRegistry
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Registry creation into backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_Grid_Report_Invitation_GridTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Create invitation use as precondition and part of test
     * <p>Steps:</p>
     * <p>1. Log in to admin</p>
     * <p>2. Go to Customers-Manage Customers</p>
     * <p>3. Create new customer associated to Main website</p>
     * <p>4. Log out from backend</p>
     * <p>5. Log in to Main website with newly created customer</p>
     * <p>6. Go to My Account>My Invitations</p>
     * <p>7. Click Send Invitations button</p>
     * <p>8. Enter valid email in the field</p>
     * <p>9. Click Send Invitations button</p>
     * <p>Expected result</p>
     * <p>The message "Invitation for "email" has been sent." is displayed.</p>
     * @test
     * @TestlinkId TL-MAGE-6438
     */
    public function createInvitation()
    {
        $this->loginAdminUser();
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_customer');
        $loginData = array('email' => $userData['email'], 'password' => $userData['password']);
        $this->customerHelper()->frontLoginCustomer($loginData);
        $this->validatePage('customer_account');
        $this->navigate('my_invitations');
        $this->clickButton('send_invitation');
        $data = $this->generate('email', 15, 'valid');
        $this->fillField('email_1', $data);
        $this->addParameter('email', $data);
        $this->clickButton('send_invitation');
        //Verification
        //$this->assertMessagePresent('success','success_send');
        $this->assertMessagePresent('error', 'failed_send'); //for run test on localhost

    }

    /**
     * This method is described the main workflow on report invitations customers page
     * <Steps>
     * <p>1. Log in to admin</p>
     * <p>2. Navigate to Reports>Invitations>Customers</p>
     * <p>3. Enter current date in to the "From" and "To" field</p>
     * <p>4. Click Refresh button</p>
     *
     * @param $page
     */
    public function workflowWithReport($page)
    {
        $this->loginAdminUser();
        $this->navigate($page);
        $date = getdate();
        $validDate = $date['mon'] . '/' . $date['mday'] . '/' . $date['year'];
        $this->fillField('filter_from', $validDate);
        $this->fillField('filter_to', $validDate);
        $this->clickButton('refresh');
    }

    /**
     * Verifying that number of elements is increased after create new invitation
     * <p>Preconditions</p>
     * <p> At least one invitations is created</p>
     * <p>Steps:</p>
     * <p>1. Log in to admin</p>
     * <p>2. Navigate to Reports>Invitations>Customers</p>
     * <p>3. Enter current date in to the "From" and "To" field</p>
     * <p>4. Click Refresh button</p>
     * <p>5. Count qty of rows</p>
     * <p>6. Create new invitation</p>
     * <p>7. Log in to admin</p>
     * <p>8. Navigate to Reports>Invitations>Customers</p>
     * <p>9. Enter current date in to the "From" and "To" field</p>
     * <p>10. Click Refresh button</p>
     * <p>11. Count qty of rows</p>
     * <p>Expected result:</p>
     * <p>The count of rows is increased on 1 item</p>
     * @test
     * @TestlinkId TL-MAGE-6438
     */
    public function verifyCustomerGrid()
    {
        $this->workflowWithReport('report_invitations_customers');
        $gridXpath = $this->_getControlXpath('pageelement', 'report_invitations_customers_grid');
        $count = $this->getElementsByXpath($gridXpath . '/tbody/tr');
        $newCount = count($count) + 1;
        $this->logoutAdminUser();
        $this->createInvitation();
        $this->workflowWithReport('report_invitations_customers');
        $this->assertCount($newCount, $this->getElementsByXpath($gridXpath . '/tbody/tr'),
            'Wrong records number in grid report_invitations_customers');
    }
}