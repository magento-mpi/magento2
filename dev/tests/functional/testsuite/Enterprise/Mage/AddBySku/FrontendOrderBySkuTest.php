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
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('OrderBySkuSettings/add_by_sku_all');
    }

    protected function tearDownAfterTest()
    {
        $this->frontend();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('OrderBySkuSettings/add_by_sku_disabled');
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
            'customer' => array('email' => $userData['email'], 'password' => $userData['password']),
            'nonExistent' => array('sku' => $this->generate('string', 10, ':alnum:'), 'qty' => 1)
        );
    }

    /**
     * <p>Adding to Cart by SKU after entering values in multiple fields</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3954
     */
    public function addMultipleSimpleProducts($data)
    {
        //Preconditions:
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        //Steps:
        $this->navigate('order_by_sku');
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array($data['simple'], $data['simple']));
        $this->saveForm('add_to_cart');
        //Verifying
        $this->addParameter('number', '2');
        $this->assertMessagePresent('success', 'products_added_to_cart_by_sku');
    }

    /**
     * <p>Successful and unsuccessful messages are located in frames different color</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4045
     */
    public function checkSuccessfulAndUnsuccessfulMessages($data)
    {
        //Preconditions:
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        //Steps:
        $this->navigate('order_by_sku');
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array($data['simple'], $data['nonExistent']));
        $this->saveForm('add_to_cart');
        //Verifying
        $this->assertMessagePresent('success', 'product_added_to_cart_by_sku');
        $this->assertMessagePresent('error', 'required_attention_product');
    }

    /**
     * <p>Adding/Removing all items from Products Requiring Attention grid</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4234
     */
    public function removeAllProductFromAttentionGrid($data)
    {
        $this->markTestIncomplete('BUG: remove_all button does not work');
        //Preconditions:
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        //Steps:
        $this->navigate('order_by_sku');
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array($data['nonExistent']));
        $this->saveForm('add_to_cart');
        $this->assertTrue($this->controlIsVisible('pageelement', 'requiring_attention_title'));
        $this->assertMessagePresent('error', 'required_attention_product');
        $this->assertMessagePresent('validation', 'sku_not_found');
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
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4235
     */
    public function removeAllProductsSeparately($data)
    {
        //Preconditions:
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        //Steps:
        $this->navigate('order_by_sku');
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array(
            array('sku' => $data['nonExistent']['sku'] . '-1', 'qty' => $data['nonExistent']['qty']),
            array('sku' => $data['nonExistent']['sku'] . '-2', 'qty' => $data['nonExistent']['qty']),
            array('sku' => $data['nonExistent']['sku'] . '-3', 'qty' => $data['nonExistent']['qty']),
            array('sku' => $data['nonExistent']['sku'] . '-4', 'qty' => $data['nonExistent']['qty']),
        ));
        $this->clickButton('add_to_cart');
        $this->addBySkuHelper()->frontDeleteItems(array('4', '3', '2', '1'));
        //Verifying
        $this->assertFalse($this->controlIsPresent('fieldset', 'products_requiring_attention'),
            'Products Requiring Attention section is present. ');
    }
}
