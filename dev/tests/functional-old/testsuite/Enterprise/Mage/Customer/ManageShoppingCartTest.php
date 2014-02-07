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
class Enterprise_Mage_Customer_ManageShoppingCartTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Verification Manage Shopping Cart Button.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5964
     */
    public function isManageShoppingCartButtonPresent()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $searchData = $this->loadDataSet('Customers', 'search_customer', array('email' => $userData['email']));
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer($searchData);
        //Verifying
        $this->assertTrue(
            $this->buttonIsPresent('manage_shopping_cart'),
            'There is no "Manage Shopping Cart" button on the page'
        );
        $this->addBySkuHelper()->openShoppingCart();
    }
}
