<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CheckoutOnePage
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * One page Checkout tests with different product types
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CheckoutOnePage_Existing_WithProductsTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Creating Simple and Virtual products</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $virtual = $this->loadDataSet('Product', 'virtual_product_visible');
        //Steps and Verification
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($virtual, 'virtual');
        $this->assertMessagePresent('success', 'success_saved_product');

        return array('simple'  => $simple['general_name'],
                     'virtual' => $virtual['general_name']);
    }

    /**
     * <p>Checkout with simple product.</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3188
     */
    public function withSimpleProductAndCustomerWithoutAddress($data)
    {
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $checkoutData = $this->loadDataSet(
            'OnePageCheckout',
            'exist_flatrate_checkmoney_usa',
            array(
                'general_name'   => $data['simple'],
                'email_address'  => $userData['email']
            )
        );
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Steps
        $this->frontend();
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Checkout with virtual product.</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3189
     */
    public function withVirtualProductAndCustomerWithoutAddress($data)
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $checkoutData = $this->loadDataSet(
            'OnePageCheckout',
            'exist_flatrate_checkmoney_virtual',
            array(
                'general_name'   => $data['virtual'],
                'email_address'  => $userData['email']
            )
        );
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->frontend();
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }
}
