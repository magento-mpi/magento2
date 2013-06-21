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
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('OrderBySkuSettings/add_by_sku_general_group');
    }

    protected function tearDownAfterTest()
    {
        $this->frontend();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
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
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps and Verification
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');

        return array(
            'simple' => array('sku' => $simple['general_sku'], 'qty' => 1),
            'customer' => array('email' => $userData['email'], 'password' => $userData['password'])
        );
    }

    /**
     * <p>Valid values for QTY field according SRS</p>
     *
     * @param string $qty
     * @param array $data
     *
     * @test
     * @dataProvider qtyListDataProvider
     * @depends      preconditionsForTests
     * @TestlinkId TL-MAGE-3933, TL-MAGE-3889
     */
    public function qtyValidation($qty, $data)
    {
        //Preconditions:    
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        //Steps:
        $this->navigate('order_by_sku');
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array(
            array('sku' => $data['simple']['sku'], 'qty' => $qty)
        ));
        $this->saveForm('add_to_cart');
        //Verifying
        $this->assertTrue($this->controlIsVisible('pageelement', 'requiring_attention_title'));
        $this->assertMessagePresent('error', 'required_attention_product');
        $this->assertMessagePresent('validation', 'enter_valid_qty');
    }

    public function qtyListDataProvider()
    {
        return array(
            array('non-num'),
            array('-5'),
            array('0'),
            array('0.00001'),
            array('999999999.9999'),
            array('')
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
        $this->customerHelper()->frontLoginCustomer($data['customer']);
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
        $this->markTestIncomplete('BUG: Add Row link does not work');
        //Preconditions:
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        //Steps:
        $this->navigate('order_by_sku');
        $this->clickButton('add_row', false);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array($data['simple']));
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
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        //Steps:
        $this->navigate('order_by_sku');
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array(
            array('sku' => $data['simple']['sku'], 'qty' => '#$%'),
            $data['simple']
        ));
        $this->saveForm('add_to_cart');
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
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('OrderBySkuSettings/add_by_sku_retailer_group');
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
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('OrderBySkuSettings/add_by_sku_disabled');
        //Steps:
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        $this->navigate('customer_account');
        //Verifying
        $this->assertFalse($this->controlIsPresent('link', 'sku_tab'), 'There is "Order by SKU" tab on the page. ');
    }
}
