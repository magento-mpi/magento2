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
class Enterprise_Mage_Grid_Reports_Invitations_GridTest extends Mage_Selenium_TestCase
{
    /**
     * <p> Preconditions before test</p>
     * <p>Create invitation
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->invitationHelper()->sendInvitationWithNewlyCreatedCustomer(1);
    }

    /**
     * Verifying that number of elements is increased after create new invitation in Customer Invitations Report Grid
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
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $firstDate = $this->customerHelper()->getCustomerRegistrationDate(array('email' => $userData['email']));
        //Count qty of rows
        $this->navigate('reports_invitations_customers');
        $this->gridHelper()->fillDateFromTo($firstDate, $firstDate);
        $this->clickButton('refresh');
        $count = $this->getControlCount('pageelement', 'report_invitations_customers_grid_line');
        $this->invitationHelper()->sendInvitationWithNewlyCreatedCustomer(1);
        $this->loginAdminUser();
        $this->navigate('reports_invitations_customers');
        $this->gridHelper()->fillDateFromTo($firstDate, $firstDate);
        $this->clickButton('refresh');
        //Verifying
        $this->assertEquals(
            $count + 1,
            $this->getControlCount('pageelement', 'report_invitations_customers_grid_line'),
            'Wrong records number in reports_invitations_customers grid'
        );
    }

    /**
     * Verifying that number of elements is increased after create new invitation in General Invitations Report Grid
     * <p>Preconditions</p>
     * <p> At least one invitations is created</p>
     * <p>Steps:</p>
     * <p>1. Log in to admin</p>
     * <p>2. Navigate to Reports>Invitations>General</p>
     * <p>3. Enter current date in to the "From" and "To" field</p>
     * <p>4. Click Refresh button</p>
     * <p>5. See qty in Sent column</p>
     * <p>6. Create new invitation</p>
     * <p>7. Log in to admin</p>
     * <p>8. Navigate to Reports>Invitations>General</p>
     * <p>9. Enter current date in to the "From" and "To" field</p>
     * <p>10. Click Refresh button</p>
     * <p>11. See qty in Sent column</p>
     * <p>Expected result:</p>
     * <p>The qty in Sent column is increased on 1 item</p>
     * @test
     * @TestlinkId TL-MAGE-6441
     */
    public function verifyGeneralGrid()
    {
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $firstDate = $this->customerHelper()->getCustomerRegistrationDate(array('email' => $userData['email']));
        $this->navigate('reports_invitations_general');
        $this->gridHelper()->fillDateFromTo($firstDate, $firstDate);
        $this->clickButton('refresh');
        $gridXpath = $this->_getControlXpath('pageelement', 'report_invitations_general_grid') . '/tbody/tr/td[2]';
        //See qty in Sent column
        $count = $this->getElement($gridXpath)->text();
        $this->invitationHelper()->sendInvitationWithNewlyCreatedCustomer(1);
        $this->loginAdminUser();
        $this->navigate('reports_invitations_general');
        $this->gridHelper()->fillDateFromTo($firstDate, $firstDate);
        $this->clickButton('refresh');
        $newCount = $this->getElement($gridXpath)->text();
        //Verifying
        $this->assertEquals($count + 1, $newCount, 'Wrong records number in reports_invitations_general grid');
    }
}