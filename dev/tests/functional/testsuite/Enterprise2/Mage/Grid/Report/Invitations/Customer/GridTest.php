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
class Enterprise2_Mage_Grid_Report_Invitation_Customer_GridTest extends Mage_Selenium_TestCase
{

    /**
     * Create invitation use as precondition and part of test
     * @test
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
        //$this->assertMessagePresent('success','success_send');
        $this->assertMessagePresent('error', 'failed_send'); //for run test on localhost

    }

    /**
     * This method is described the main workflow on report invitations customers page
     */
    public function workflowWithReport()
    {
        $this->loginAdminUser();
        $this->navigate('report_invitations_customers');
        $date = getdate();
        $validDate = $date['mon'] . '/' . $date['mday'] . '/' . $date['year'];
        $this->fillField('filter_from', $validDate);
        $this->fillField('filter_to', $validDate);
        $this->clickButton('refresh');

    }

    /**
     * Verifying that number of elements is increased after create new invitation
     * @test
     */
    public function verifyGrid()
    {
        self::workflowWithReport();
        $gridXpath = $this->_getControlXpath('pageelement', 'report_invitations_customers_grid');
        $count = $this->getElementsByXpath($gridXpath . '/tbody/tr');
        $newCount = count($count) + 1;
        $this->logoutAdminUser();
        self::createInvitation();
        self::workflowWithReport();
        $gridXpath = $this->_getControlXpath('pageelement', 'report_invitations_customers_grid');
        $this->assertCount($newCount, $this->getElementsByXpath($gridXpath . '/tbody/tr'),
            'Wrong records number in grid report_invitations_customers');
    }

}