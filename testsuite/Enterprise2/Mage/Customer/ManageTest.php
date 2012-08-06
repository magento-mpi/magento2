<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Customer
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Verification Manage Shopping Cart Button
 */
class Enterprise2_Mage_Customer_ManageShoppingCartButtonTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate Manage Customers</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
    }

    /**
     * <p>Create customer for verification Manage Shopping Cart Button</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $searchData = $this->loadDataSet('Customers', 'search_customer', array('email' => $userData['email']));
        $this->addParameter('customer_first_last_name', $userData['first_name'] . ' ' . $userData['last_name']);
        //Steps
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');

        return $searchData;
    }

    /**
     * <p>Verification Manage Shopping Cart Button.</p>
     * <p>Steps:</p>
     *  <p>1. Search and open customer.</p>
     *  <p>2. Click 'Manage Shopping Cart' button.</p>
     *
     * <p>Expected result:</p>
     *  <p>1. 'Manage Shopping Cart' button is present on customer page.</p>
     *  <p>2. Customer Shopping Cart page is opened.</p>
     *
     * @param array $searchData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5964
     */
    public function isManageShoppingCartButtonPresent(array $searchData)
    {
        //Steps
        $this->customerHelper()->openCustomer($searchData);
        //Verifying
        $this->assertTrue($this->buttonIsPresent('manage_shopping_cart'),
            'There is no "Manage Shopping Cart" button on the page');
        $this->clickButton('manage_shopping_cart', false);
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->addParameter('store', $this->defineParameterFromUrl('store'));
        $this->addParameter('customer', $this->defineParameterFromUrl('customer'));
        $this->validatePage('customer_shopping_cart');
    }
}
