<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Customer
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test deletion customer.
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Customer_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Manage Customers</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
    }

    /**
     * <p>Delete customer.</p>
     * <p>Preconditions: Create Customer</p>
     * <p>Steps:</p>
     * <p>1. Search and open customer.</p>
     * <p>2. Click 'Delete Customer' button.</p>
     * <p>Expected result:</p>
     * <p>Customer is deleted.</p>
     * <p>Success Message is displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3237
     */
    public function single()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $searchData = $this->loadDataSet('Customers', 'search_customer', array('email' => $userData['email']));
        //Preconditions
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Steps
        $param = $userData['first_name'] . ' ' . $userData['last_name'];
        $this->addParameter('customer_first_last_name', $param);
        $this->customerHelper()->openCustomer($searchData);
        $this->clickButtonAndConfirm('delete_customer', 'confirmation_for_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_customer');
    }

    /**
     * <p>Delete customers.</p>
     * <p>Preconditions: Create several customers</p>
     * <p>Steps:</p>
     * <p>1. Search and choose several customers.</p>
     * <p>3. Select 'Actions' to 'Delete'.</p>
     * <p>2. Click 'Submit' button.</p>
     * <p>Expected result:</p>
     * <p>Customers are deleted.</p>
     * <p>Success Message is displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3238
     */
    public function throughMassAction()
    {
        $customerQty = 2;
        for ($i = 1; $i <= $customerQty; $i++) {
            //Data
            $userData = $this->loadDataSet('Customers', 'generic_customer_account');
            ${'searchData' . $i} =
                $this->loadDataSet('Customers', 'search_customer', array('email' => $userData['email']));
            //Steps
            $this->customerHelper()->createCustomer($userData);
            $this->assertMessagePresent('success', 'success_saved_customer');
        }
        for ($i = 1; $i <= $customerQty; $i++) {
            $this->searchAndChoose(${'searchData' . $i});
        }
        $this->addParameter('qtyDeletedCustomers', $customerQty);
        $xpath = $this->_getControlXpath('dropdown', 'grid_massaction_select');
        $this->select($xpath, 'Delete');
        $this->clickButtonAndConfirm('submit', 'confirmation_for_massaction_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_customer_massaction');
    }
}