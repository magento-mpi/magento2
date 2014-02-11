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
        $this->frontend();
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
     * @param string $message
     * @param string $messageType
     * @param array $data
     *
     * @test
     * @dataProvider qtyListDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3933, TL-MAGE-3889
     */
    public function qtyValidation($qty, $message, $messageType, $data)
    {
        //Preconditions:
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        //Steps:
        $this->navigate('order_by_sku');
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(
            array(array('sku' => $data['simple']['sku'], 'qty' => $qty))
        );
        $this->saveForm('add_to_cart_by_sku');
        //Verifying
        if ($messageType == 'validation') {
            $this->addFieldIdToMessage('field', 'qty');
            $this->assertMessagePresent('validation', $message);
        } else {
            $this->assertTrue($this->controlIsVisible('fieldset', 'products_requiring_attention'));
            $this->assertMessagePresent('error', 'required_attention_product');
            $this->assertMessagePresent('error', $message);
        }
    }

    public function qtyListDataProvider()
    {
        return array(
            array('non-num', 'enter_greater_than_zero', 'validation'),
            array('-5', 'enter_greater_than_zero', 'validation'),
            array('0', 'enter_greater_than_zero', 'validation'),
            array('', 'empty_required_field', 'validation'),
            array('0.00001', 'enter_valid_qty', 'error'),
            array('99999999.9999', 'max_allowed_qty', 'error')
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
        $this->clickButton('add_to_cart_by_sku');
        //Verifying
        $this->addFieldIdToMessage('field', 'sku');
        $this->assertMessagePresent('validation', 'empty_required_field');
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
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        //Steps:
        $this->navigate('order_by_sku');
        $this->clickButton('add_row', false);
        $this->addFieldIdToMessage('field', 'sku');
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array($data['simple']));
        $this->clickButton('add_to_cart_by_sku');
        //Verifying
        $this->assertMessagePresent('validation', 'empty_required_field');
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
            $data['simple'],
            array('sku' => $data['simple']['sku'], 'qty' => '-15')
        ));
        $this->saveForm('add_to_cart_by_sku');
        //Verifying
        $this->addFieldIdToMessage('field', 'qty');
        $this->assertMessagePresent('validation', 'enter_greater_than_zero');
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
        $this->assertFalse($this->controlIsPresent('link', 'order_by_sku'), 'There is "Order by SKU" tab on the page.');
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
        $this->assertFalse($this->controlIsPresent('link', 'order_by_sku'), 'There is "Order by SKU" tab on the page.');
    }
}
