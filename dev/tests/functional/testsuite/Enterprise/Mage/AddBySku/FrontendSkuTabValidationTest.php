<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AddBySku
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for Order by SKU functionality on the Frontend in My Account
 */
class Enterprise_Mage_AddBySku_FrontendSkuTabValidationTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        //Data
        $config = $this->loadDataSet('OrderBySkuSettings', 'add_by_sku_general_group');
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    /**
     * <p>Creating Simple product and customer</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps and Verification
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        return array(
            'simple_product' => array('sku' => $simple['general_sku'], 'qty' => 1),
            'customer' => array('email' => $userData['email'], 'password' => $userData['password'])
        );
    }

    /**
     * <p>Valid values for QTY field according SRS</p>
     *
     * @param string $qty
     * @param string $message
     * @param array $data
     *
     * @test
     * @dataProvider qtyListDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3933, TL-MAGE-3889
     */
    public function qtyValidation($qty, $message, $data)
    {
        //Preconditions:    
        $this->frontend();
        if ($this->controlIsPresent('link', 'log_in')) {
            $this->customerHelper()->frontLoginCustomer($data['customer']);
        }
        //Steps:
        $this->navigate('order_by_sku');
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array('sku' => $data['simple_product']['sku'], 'qty' => $qty));
        $this->clickButton('add_to_cart', false);
        $this->waitForAjax();
        //Verifying
        $this->addFieldIdToMessage('field', 'qty');
        $this->assertMessagePresent('validation', $message);
    }

    public function qtyListDataProvider()
    {
        return array(
            array('non-num', 'empty_required_field'),
            array('-5', 'empty_required_field'),
            array('0', 'empty_required_field'),
            array('0.00001', 'empty_required_field'),
            array('999999999.9999', 'empty_required_field'),
            array('-5', 'empty_required_field'),
            array('', 'empty_required_field')
        );
    }

    /**
     * <p>Validation rows, for which SKU and Qty values are empty</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3889
     */
    public function addEmptyRowQtyFields($data)
    {
        //Preconditions:
        $this->shoppingCartHelper()->frontClearShoppingCart();
        if ($this->controlIsPresent('link', 'log_in')) {
            $this->customerHelper()->frontLoginCustomer($data['customer']);
        }
        //Steps:
        $this->navigate('order_by_sku');
        $this->clickButton('add_to_cart');
        //Verifying
        $this->assertMessagePresent('error', 'no_product_added_by_sku');
    }

    /**
     * <p>Validation rows, for which SKU and Qty values are empty</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3889
     */
    public function addSimpleProductWithEmptyRow($data)
    {
        //Preconditions:
        $this->shoppingCartHelper()->frontClearShoppingCart();
        if ($this->controlIsPresent('link', 'log_in')) {
            $this->customerHelper()->frontLoginCustomer($data['customer']);
        }
        //Steps:
        $this->navigate('order_by_sku');
        $this->clickButton('add_row', false);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['simple_product']);
        $this->clickButton('add_to_cart');
        //Verifying
        $this->assertMessagePresent('success', 'product_added_to_cart_by_sku');
    }

    /**
     * <p>Adding to Cart by SKU after entering valid and invalid values in multiple fields</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @depends qtyValidation
     * @TestlinkId TL-MAGE-4057
     */
    public function addMultipleSimpleProductsFailure($data)
    {
        //Preconditions:
        $this->frontend();
        if ($this->controlIsPresent('link', 'log_in')) {
            $this->customerHelper()->frontLoginCustomer($data['customer']);
        }
        //Steps:
        $this->navigate('order_by_sku');
        $this->clickButton('add_row', false);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array('sku' => $data['simple_product']['sku'], 'qty' => '#$%'),
            array('1'));
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['simple_product'], array('2'));
        $this->clickButton('add_to_cart', false);
        //Verifying
        $this->assertMessagePresent('error', 'sku_invalid_number');
    }

    /**
     * <p>Disable order by SKU on My Account for for customers unselected group</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3878
     */
    public function orderBySkuForUnselectedCustomer($data)
    {
        //Preconditions:
        $config = $this->loadDataSet('OrderBySkuSettings', 'add_by_sku_retailer_group');
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        //Steps:
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        $this->navigate('customer_account');
        //Verifying
        $this->assertFalse($this->controlIsPresent('link', 'sku_tab'), 'There is "Order by SKU" tab on the page. ');
    }

    /**
     * <p>Disable Order by SKU on My Account in Front-end</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3868
     */
    public function orderBySkuDisabled($data)
    {
        //Preconditions:
        $config = $this->loadDataSet('OrderBySkuSettings', 'add_by_sku_disabled');
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        //Steps:
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        $this->navigate('customer_account');
        //Verifying
        $this->assertFalse($this->controlIsPresent('link', 'sku_tab'), 'There is "Order by SKU" tab on the page. ');
    }
}
