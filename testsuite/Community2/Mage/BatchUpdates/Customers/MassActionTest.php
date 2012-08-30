<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_BatchUpdates
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customers updating using batch updates tests for Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_BatchUpdates_Customers_MassActionTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Customers -> Manage Customers</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
    }

    /**
     * <p>Subscribe to newsletter created customers using Batch Updates</p>
     * <p>Steps:</p>
     * <p>1. Create two customers
     * <p>2. Select created customers by checkboxes</p>
     * <p>3. Select value "Subscribe to Newsletter" in action dropdown</p>
     * <p>4. Click button "Submit"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the customers has been updated.</p>
     *
     * @return array
     * @test
     */
    public function subscribeToNewsletterCustomers()
    {
        $affectedCustomers = array();
        $customerQty = 2;
        //Data
        for ($i = 1; $i <= $customerQty; $i++) {
            $customerData = $this->loadDataSet('Customers', 'generic_customer_account');
            ${'searchData' . $i} = $this->loadDataSet('Customers', 'search_customer',
                array('email' => $customerData['email']));
            $this->customerHelper()->createCustomer($customerData);
            $this->assertMessagePresent('success', 'success_saved_customer');
            $affectedCustomers[] = $customerData;
        }
        for ($i = 1; $i <= $customerQty; $i++) {
            $this->searchAndChoose(${'searchData' . $i});
        }
        //Steps
        $this->addParameter('qtySubscribeCustomers', $customerQty);
        $this->fillDropdown('grid_massaction_select', 'Subscribe to Newsletter');
        $this->clickButton('submit');
        //Verifying
        $this->assertMessagePresent('success', 'success_suscribe_unsuscribe_customer_massaction');

        return $affectedCustomers;
    }

    /**
     * <p>Unsubscribe  from newsletter customers using Batch Updates</p>
     * <p>Steps:</p>
     * <p>1. Select created customers by checkboxes from previous test</p>
     * <p>8. Select value "Unsubscribe from Newsletter" in action dropdown</p>
     * <p>9. Click button "Submit"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the customers has been updated.</p>
     *
     * @param array|string $affectedCustomers
     *
     * @test
     * @depends subscribeToNewsletterCustomers
     */
    public function unsubscribeToNewsletterCustomers($affectedCustomers)
    {
        foreach ($affectedCustomers as $customerData) {
            $searchData = $this->loadDataSet('Customers', 'search_customer', array('email' => $customerData['email']));
            $this->searchAndChoose($searchData, 'customers_grid');
        }
        //Steps
        $this->fillDropdown('grid_massaction_select', 'Unsubscribe from Newsletter');
        $this->clickButton('submit');
        //Verifying
        $this->assertMessagePresent('success', 'success_suscribe_unsuscribe_customer_massaction');
    }

    /**
     * <p>Assign customers to non-default Customer group via Batch Updates</p>
     * <p>Steps:</p>
     * <p>1. Select created customers by checkboxes from previous test</p>
     * <p>2. Select value "Assign a Customer Group" in action dropdown</p>
     * <p>3. Select non-default Customer group which was created for this test</p>
     * <p>9. Click button "Submit"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the customers has been updated.</p>
     *
     * @param array|string $affectedCustomers
     *
     * @test
     * @depends subscribeToNewsletterCustomers
     */
    public function assignCustomerToNonDefaultGroup($affectedCustomers)
    {
        //Steps
        $customerGroupData = $this->loadDataSet('CustomerGroup', 'new_customer_group');
        $this->navigate('manage_customer_groups');
        $this->customerGroupsHelper()->createCustomerGroup($customerGroupData);
        $this->navigate('manage_customers');

        foreach ($affectedCustomers as $customerData) {
            $searchData = $this->loadDataSet('Customers', 'search_customer', array('email' => $customerData['email']));
            $this->searchAndChoose($searchData, 'customers_grid');
        }
        $this->fillDropdown('grid_massaction_select', 'Assign a Customer Group');
        $this->fillDropdown('group', $customerGroupData['group_name']);
        $this->clickButton('submit');
        //Verifying
        $this->assertMessagePresent('success', 'success_suscribe_unsuscribe_customer_massaction');
    }

    /**
     * <p>Update Attributes customer using Batch Updates Negative test</p>
     * <p>Steps:</p>
     * <p>1. Select any value in "Action" dropdown</p>
     * <p>2. Click button "Submit"</p>
     * <p>Expected result:</p>
     * <p>Received the popup message "Please select items.".</p>
     *
     * @param string $actionValue
     * @dataProvider updateAttributesByBatchUpdatesNegativeDataProvider
     * @test
     */
    public function updateAttributesByBatchUpdatesNegative($actionValue)
    {
        //Steps
        $this->fillDropdown('grid_massaction_select', $actionValue);
        $this->clickButton('submit', false);
        if (!$this->isAlertPresent()) {
            $this->fail('confirmation message not found on page');
        }
        $actualAlertText = $this->getAlert();
        //Verifying
        $this->assertSame('Please select items.', $actualAlertText, 'actual and expected confirmation message does not match');
    }

    public function updateAttributesByBatchUpdatesNegativeDataProvider()
    {
        return array(
            array('Delete'),
            array('Subscribe to Newsletter'),
            array('Unsubscribe from Newsletter'),
            array('Assign a Customer Group')
        );
    }

    /**
     * <p>Assign customers to default customers group using Batch Updates</p>
     * <p>Steps:</p>
     * <p>1. Create two customers
     * <p>2. Select created customers by checkboxes</p>
     * <p>3. Select value "Assign a Customer Group" in action dropdown</p>
     * <p>4. In dropdown "Group" select default groups
     * <p>5. Click button "Submit"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the customers has been updated.</p>
     *
     * @param array|string $groupValue
     *
     * @test
     * @dataProvider assignToDefaultCustomersGroupDataProvider
     */
    public function assignToDefaultCustomersGroup($groupValue)
    {
        $customerQty = 2;
        //Data
        for ($i = 1; $i <= $customerQty; $i++) {
            $customerData = $this->loadDataSet('Customers', 'generic_customer_account');
            ${'searchData' . $i} = $this->loadDataSet('Customers', 'search_customer',
                array('email' => $customerData['email']));
            $this->customerHelper()->createCustomer($customerData);
            $this->assertMessagePresent('success', 'success_saved_customer');
        }
        for ($i = 1; $i <= $customerQty; $i++) {
            $this->searchAndChoose(${'searchData' . $i});
        }
        //Steps
        $this->addParameter('qtySubscribeCustomers', $customerQty);
        $this->fillDropdown('grid_massaction_select', 'Assign a Customer Group');
        $this->fillDropdown('group', $groupValue);
        $this->clickButton('submit');
        //Verifying
        $this->assertMessagePresent('success', 'success_suscribe_unsuscribe_customer_massaction');
    }

    public function assignToDefaultCustomersGroupDataProvider()
    {
        return array(
            array('Wholesale'),
            array('Retailer'),
            array('General')
        );
    }

}