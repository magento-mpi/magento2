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
class Core_Mage_BatchUpdates_Customers_MassActionTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
    }

    /**
     * <p>Subscribe to newsletter created customers using Batch Updates</p>
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
            $this->searchAndChoose(${'searchData' . $i}, 'customers_grid');
        }
        //Steps
        $this->addParameter('qtySubscribeCustomers', $customerQty);
        $this->fillDropdown('mass_action_select_action', 'Subscribe to Newsletter');
        $this->clickButton('submit');
        //Verifying
        $this->assertMessagePresent('success', 'success_subscribe_unsubscribe_customer_massaction');

        return $affectedCustomers;
    }

    /**
     * <p>Unsubscribe  from newsletter customers using Batch Updates</p>
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
        $this->fillDropdown('mass_action_select_action', 'Unsubscribe from Newsletter');
        $this->clickButton('submit');
        //Verifying
        $this->assertMessagePresent('success', 'success_subscribe_unsubscribe_customer_massaction');
    }

    /**
     * <p>Assign customers to non-default Customer group via Batch Updates</p>
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
        $this->fillDropdown('mass_action_select_action', 'Assign a Customer Group');
        $this->fillDropdown('group', $customerGroupData['group_name']);
        $this->clickButton('submit');
        //Verifying
        $this->assertMessagePresent('success', 'success_subscribe_unsubscribe_customer_massaction');
    }

    /**
     * <p>Update Attributes customer using Batch Updates Negative test</p>
     *
     * @param string $actionValue
     * @dataProvider updateAttributesByBatchUpdatesNegativeDataProvider
     * @test
     */
    public function updateAttributesByBatchUpdatesNegative($actionValue)
    {
        //Steps
        $this->fillDropdown('mass_action_select_action', $actionValue);
        $this->clickButton('submit', false);
        //Verifying
        $this->assertSame('Please select items.', $this->alertText(),
            'actual and expected confirmation message does not match');
        $this->acceptAlert();
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
            $this->searchAndChoose(${'searchData' . $i}, 'customers_grid');
        }
        //Steps
        $this->addParameter('qtySubscribeCustomers', $customerQty);
        $this->fillDropdown('mass_action_select_action', 'Assign a Customer Group');
        $this->fillDropdown('group', $groupValue);
        $this->clickButton('submit');
        //Verifying
        $this->assertMessagePresent('success', 'success_subscribe_unsubscribe_customer_massaction');
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