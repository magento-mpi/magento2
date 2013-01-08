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
 * General flow for testing Order by SKU functionality on the Frontend
 */
class Enterprise_Mage_AddBySku_FrontendOrderBySkuTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Enable Order by SKU functionality on Frontend</p>
     */
    public function setUpBeforeTests()
    {
        //Data
        $configSku = $this->loadDataSet('OrderBySkuSettings', 'add_by_sku_all');
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($configSku);
    }

    /**
     * <p>Creating customer</p>
     *
     * @return array
     *
     * @test
     */
    public function createCustomer()
    {
        $this->loginAdminUser();
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        return array('email' => $userData['email'], 'password' => $userData['password']);
    }

    /**
     * <p>Create simple and non-existent products for testing SKU functionality</p>
     *
     * @return array
     *
     * @test
     */
    public function preconditionsForTests()
    {
        $this->loginAdminUser();
        //Create simple products
        $simpleProduct = $this->loadDataSet('SkuProducts', 'simple_sku');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simpleProduct);
        $this->assertMessagePresent('success', 'success_saved_product');

        return array(
            'simple' => array('sku' => $simpleProduct['general_sku'], 'qty' => 1),
            'nonExistentProduct' => array('sku' => $this->generate('string', 10, ':alnum:'), 'qty' => 1)
        );
    }

    /**
     * <p>Adding to Cart by SKU after entering values in multiple fields</p>
     *
     * @param array $data
     * @param array $customer
     *
     * @test
     * @depends preconditionsForTests
     * @depends createCustomer
     * @TestlinkId TL-MAGE-3954
     */
    public function addMultipleSimpleProducts($data, $customer)
    {
        //Preconditions:
        $this->frontend();
        if ($this->controlIsPresent('link', 'log_in')) {
            $this->customerHelper()->frontLoginCustomer($customer);
        }
        $this->shoppingCartHelper()->frontClearShoppingCart();
        //Steps:
        $this->navigate('order_by_sku');
        $this->clickButton('add_row', false);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['simple'], array('1', '2'));
        $this->clickButton('add_to_cart');
        //Verifying
        $this->addParameter('number', '2');
        $this->assertMessagePresent('success', 'products_added_to_cart_by_sku');
    }

    /**
     * <p>Successful and unsuccessful messages are located in frames different color</p>
     *
     * @param array $data
     * @param array $customer
     *
     * @test
     * @depends preconditionsForTests
     * @depends createCustomer
     * @TestlinkId TL-MAGE-4045
     */
    public function checkSuccessfulAndUnsuccessfulMessages($data, $customer)
    {
        //Preconditions:
        $this->frontend();
        if ($this->controlIsPresent('link', 'log_in')) {
            $this->customerHelper()->frontLoginCustomer($customer);
        }
        $this->shoppingCartHelper()->frontClearShoppingCart();
        //Steps:
        $this->navigate('order_by_sku');
        $this->clickButton('add_row', false);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['simple']);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['nonExistentProduct'], array('2'));
        $this->clickButton('add_to_cart');
        //Verifying
        $this->assertMessagePresent('success', 'product_added_to_cart_by_sku');
        $this->assertMessagePresent('error', 'required_attention_product');
    }

    /**
     * <p>Adding/Removing all items from Products Requiring Attention grid</p>
     *
     * @param array $data
     * @param array $customer
     *
     * @test
     * @depends preconditionsForTests
     * @depends createCustomer
     * @TestlinkId TL-MAGE-4234
     */
    public function removeAllProductFromAttentionGrid($data, $customer)
    {
        //Preconditions:
        $this->frontend();
        if ($this->controlIsPresent('link', 'log_in')) {
            $this->customerHelper()->frontLoginCustomer($customer);
        }
        $this->shoppingCartHelper()->frontClearShoppingCart();
        //Steps:
        $this->navigate('order_by_sku');
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['nonExistentProduct']);
        $this->clickButton('add_to_cart');
        $this->clickButton('remove_all');
        //Verifying
        $this->assertMessagePresent('success', 'items_removed');
        $this->assertFalse($this->controlIsPresent('fieldset', 'products_requiring_attention'),
            'Products Requiring Attention section is present. ');
    }

    /**
     * <p>Adding/Removing each attention product separately</p>
     *
     * @param array $data
     * @param array $customer
     *
     * @test
     * @depends preconditionsForTests
     * @depends createCustomer
     * @TestlinkId TL-MAGE-4235
     */
    public function removeAllProductsSeparately($data, $customer)
    {
        //Preconditions:
        $this->frontend();
        if ($this->controlIsPresent('link', 'log_in')) {
            $this->customerHelper()->frontLoginCustomer($customer);
        }
        $this->shoppingCartHelper()->frontClearShoppingCart();
        //Steps:
        $this->navigate('order_by_sku');
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array('sku' => $data['nonExistentProduct']['sku'] . '-1',
            'qty' => $data['nonExistentProduct']['qty']));
        $this->clickButton('add_row', false);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array('sku' => $data['nonExistentProduct']['sku'] . '-2',
            'qty' => $data['nonExistentProduct']['qty']), array('2'));
        $this->clickButton('add_row', false);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array('sku' => $data['nonExistentProduct']['sku'] . '-3',
            'qty' => $data['nonExistentProduct']['qty']), array('3'));
        $this->clickButton('add_row', false);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array('sku' => $data['nonExistentProduct']['sku'] . '-4',
            'qty' => $data['nonExistentProduct']['qty']), array('4'));
        $this->clickButton('add_to_cart');
        $this->addBySkuHelper()->frontDeleteItems(array('4', '3', '2', '1'));
        //Verifying
        $this->assertFalse($this->controlIsPresent('fieldset', 'products_requiring_attention'),
            'Products Requiring Attention section is present. ');
    }
}
