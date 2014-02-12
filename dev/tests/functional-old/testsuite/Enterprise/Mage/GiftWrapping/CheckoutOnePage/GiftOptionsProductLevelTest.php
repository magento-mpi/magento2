<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_GiftWrapping
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Tests Gift Options on Product Level
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_GiftWrapping_CheckoutOnePage_GiftOptionsProductLevelTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_disable_all');
    }

    public function assertPreconditions()
    {
        $this->loginAdminUser();
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
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_disable_all');
    }

    /**
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $product1 = $this->loadDataSet('Product', 'simple_product_visible');
        $product2 = $this->loadDataSet('Product', 'simple_product_visible');
        $defaultUser = $this->loadDataSet('Customers', 'customer_account_register');
        $wrapping = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product1);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($product2);
        $this->assertMessagePresent('success', 'success_saved_product');

        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($wrapping);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');

        $this->frontend();
        $this->customerHelper()->registerCustomer($defaultUser);
        $this->assertMessagePresent('success', 'success_registration');

        return array(
            'sku' => array($product1['general_sku'], $product2['general_sku']),
            'name' => array($product1['general_name'], $product2['general_name']),
            'defaultUser' => array('email' => $defaultUser['email'], 'password' => $defaultUser['password']),
            'wrapping_price' => $wrapping['gift_wrapping_price'],
            'wrapping' => $wrapping['gift_wrapping_design']
        );
    }

    /**
     * @TestlinkId TL-MAGE-826
     * <p>Gift Options on product level set to Yes. FrontEnd. Case1.</p>
     * <p>Verify that when "Allow Gift Wrapping" and Gift Messages for Order Items in SysConfig set to "No",
     * but Gift Wrapping and Gift Messages in a product Menu Gift Options set to "Yes",
     * then Gift Wrapping and Gift messages for that product in Frontend on item level are available.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftOptionsOnProductLevelSetToYes($testData)
    {
        //Data
        $checkout = $this->loadDataSet('OnePageCheckout', 'recount_gift_wrapping_no_image',
            array(
                'gift_wrapping_for_items' => '$' . $testData['wrapping_price'],
                'entire_order' => '%noValue%',
            ),
            array(
                'add_product_1' => $testData['name'][0],
                'gift_item_product_1' => $testData['name'][0],
                'product1wrapping' => $testData['wrapping']
            )
        );
        $override = array_merge(
            $checkout['shipping_data']['add_gift_options']['individual_items']['item_1']['gift_message'],
            array('sku_product' => $testData['sku'][0])
        );
        $verifyMessages = $this->loadDataSet('OnePageCheckout', 'verify_gift_data', $override);
        $verifyWrapping = $this->loadDataSet('OnePageCheckout', 'verify_wrapping_data', null, array(
            'product1wrapping' => $testData['wrapping'],
            'gift_item_product_1' => $testData['sku'][0],
            'price_product_1' => '$' . $testData['wrapping_price']
        ));
        //Steps
        $this->_updateProductGiftOptions($testData['name'][0], 'gift_options_message_yes_wrapping_yes');
        $this->customerHelper()->frontLoginCustomer($testData['defaultUser']);
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkout);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $this->orderHelper()->verifyGiftOptions($verifyMessages);
        $this->orderHelper()->verifyGiftOptions($verifyWrapping);
    }

    /**
     * @TestlinkId TL-MAGE-827
     * <p>Gift Options on product level set to No. FrontEnd. </p>
     * <p>Verify that when "Allow Gift Wrapping" and Gift Messages for Order Items in Config set to "Yes",
     * but Gift Wrapping and Gift Messages in a product Menu Gift Options set to "No",
     * then Gift Wrapping and Gift Messages for that product in Frontend on item level is not available.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftOptionsOnProductLevelSetToNoCase1($testData)
    {
        $this->markTestIncomplete('BUG: gift options for Individual Items is visible');
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('add_product_1' => $testData['name'][0]));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_all_yes_order_all_no');
        //Steps
        $this->_updateProductGiftOptions($testData['name'][0], 'gift_options_message_no_wrapping_no');
        $this->customerHelper()->frontLoginCustomer($testData['defaultUser']);
        foreach ($checkoutData['products_to_add'] as $data) {
            $this->productHelper()->frontOpenProduct($data['general_name']);
            $this->productHelper()->frontAddProductToCart();
        }
        $this->assertTrue($this->checkCurrentPage('shopping_cart'), $this->getParsedMessages());
        $this->clickButton('proceed_to_checkout');
        $this->checkoutOnePageHelper()->frontFillOnePageBillingAddress($checkoutData['billing_address_data']);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        //Verification
        $this->assertFalse($this->controlIsVisible('checkbox', 'add_gift_options'),
            '"Add gift options" checkbox is visible');
    }

    /**
     * @TestlinkId TL-MAGE-831
     * <p>Gift Options on product level set to No. FrontEnd</p>
     * <p>Verify that when "Allow Gift Wrapping" and Gift Messages for Order Items and Order in Config set to "Yes",
     * but Gift Wrapping and Gift Messages in a product Menu Gift Options set to "No",
     * then Gift Wrapping and Gift Messages for that product in Frontend on item level is not available.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftOptionsOnProductLevelSetToNoCase2($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('add_product_1' => $testData['name'][0]));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_all_yes_order_all_yes');
        //Steps
        $this->_updateProductGiftOptions($testData['name'][0], 'gift_options_message_no_wrapping_no');
        $this->customerHelper()->frontLoginCustomer($testData['defaultUser']);
        foreach ($checkoutData['products_to_add'] as $data) {
            $this->productHelper()->frontOpenProduct($data['general_name']);
            $this->productHelper()->frontAddProductToCart();
        }
        $this->assertTrue($this->checkCurrentPage('shopping_cart'), $this->getParsedMessages());
        $this->clickButton('proceed_to_checkout');
        $this->checkoutOnePageHelper()->frontFillOnePageBillingAddress($checkoutData['billing_address_data']);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        $this->fillCheckbox('add_gift_options', 'Yes');
        //Verification
        $this->addParameter('productName', $testData['name'][0]);
        $this->assertFalse($this->controlIsVisible('checkbox', 'gift_option_for_item'),
            '"Add gift options" for the Item checkbox is visible');
        $this->assertFalse($this->controlIsVisible('link', 'add_item_gift_message'),
            '"Gift Message" link is visible');
    }

    /**
     * @TestlinkId TL-MAGE-832
     * @TestlinkId TL-MAGE-849
     * <p>Gift Wrapping on product level set to No. FrontEnd. Case3.</p>
     * <p>Gift Messages on product level set to No. FrontEnd. Case4.</p>
     * <p>Verify that when "Allow Gift Message"s for Order Items in Config set to “Yes",
     * and Gift Messages in a product Menu Gift Options set to "No",
     * then Gift Messages for that product in Frontend on item level is not available.</p>
     *
     * @param string $settings
     * @param array $options
     * @param array $testData
     *
     * @test
     * @dataProvider giftOptionsOnProductLevelSetToNoCase3Case4DataProvider
     * @depends preconditionsForTests
     */
    public function giftOptionsOnProductLevelSetToNoCase3Case4($settings, $options, $testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('add_product_1' => $testData['name'][0]));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_all_yes_order_all_no');
        //Steps
        $this->_updateProductGiftOptions($testData['name'][0], 'gift_options_message_' . $settings);
        $this->customerHelper()->frontLoginCustomer($testData['defaultUser']);
        foreach ($checkoutData['products_to_add'] as $data) {
            $this->productHelper()->frontOpenProduct($data['general_name']);
            $this->productHelper()->frontAddProductToCart();
        }
        $this->assertTrue($this->checkCurrentPage('shopping_cart'), $this->getParsedMessages());
        $this->clickButton('proceed_to_checkout');
        $this->checkoutOnePageHelper()->frontFillOnePageBillingAddress($checkoutData['billing_address_data']);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        $this->fillCheckbox('add_gift_options', 'Yes');
        $this->fillCheckbox('gift_option_for_item', 'Yes');
        //Verification
        $this->addParameter('productName', $testData['name'][0]);
        $this->$options['wrapping'](
            $this->controlIsPresent('dropdown', 'item_gift_wrapping_design'),
            '"Gift Wrapping Design" is ' . (($options['wrapping'] == 'assertTrue') ? 'not visible' : 'visible')
        );
        $this->$options['message'](
            $this->controlIsPresent('link', 'add_item_gift_message'),
            '"Gift Message" link is ' . (($options['message'] == 'assertTrue') ? 'not visible' : 'visible')
        );
    }

    public function giftOptionsOnProductLevelSetToNoCase3Case4DataProvider()
    {
        return array(
            array('yes_wrapping_no', array('message' => 'assertTrue', 'wrapping' => 'assertFalse')),
            array('no_wrapping_yes', array('message' => 'assertFalse', 'wrapping' => 'assertTrue'))
        );
    }

    /**
     * @TestlinkId TL-MAGE-845
     * @TestlinkId TL-MAGE-851
     * <p>Gift Wrapping on product level set to No. FrontEnd. Case5.</p>
     * <p>Gift Messages on product level set to No. FrontEnd. Case6.</p>
     * <p>Verify that when "Allow Gift Wrapping" for Order Items in Config set to "Yes"
     * and Gift Wrapping in a product Menu Gift Options set to "No",
     * then Gift Wrapping for that product in Frontend on item level is not available.</p>
     *
     * @param $settings
     * @param $options
     * @param array $testData
     *
     * @test
     * @dataProvider giftOptionsOnProductLevelSetToNoCase5Case6DataProvider
     * @depends preconditionsForTests
     */
    public function giftOptionsOnProductLevelSetToNoCase5Case6($settings, $options, $testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null, array(
            'add_product_1' => $testData['name'][0],
            'add_product_2' => $testData['name'][1]
        ));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/' . $settings);
        //Steps
        $this->_updateProductGiftOptions($testData['name'][0], $options[0]);
        $this->_updateProductGiftOptions($testData['name'][1], $options[1]);
        $this->customerHelper()->frontLoginCustomer($testData['defaultUser']);
        foreach ($checkoutData['products_to_add'] as $data) {
            $this->productHelper()->frontOpenProduct($data['general_name']);
            $this->productHelper()->frontAddProductToCart();
        }
        $this->assertTrue($this->checkCurrentPage('shopping_cart'), $this->getParsedMessages());
        $this->clickButton('proceed_to_checkout');
        $this->checkoutOnePageHelper()->frontFillOnePageBillingAddress($checkoutData['billing_address_data']);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        $this->fillCheckbox('add_gift_options', 'Yes');
        $this->fillCheckbox('gift_option_for_item', 'Yes');
        //Verification
        $this->addParameter('productName', $testData['name'][0]);
        $this->assertFalse($this->controlIsPresent('pageelement', 'item_product_name'),
            $testData['name'][0] . ' product is visible in Gift Options for Individual items');
        $this->addParameter('productName', $testData['name'][1]);
        $this->assertTrue($this->controlIsPresent('pageelement', 'item_product_name'),
            $testData['name'][1] . ' product is not visible in Gift Options for Individual items');
    }

    public function giftOptionsOnProductLevelSetToNoCase5Case6DataProvider()
    {
        return array(
            array(
                'ind_items_gift_wrapping_yes_message_no',
                array('gift_options_message_no_wrapping_no', 'gift_options_message_no_wrapping_yes')
            ),
            array(
                'ind_items_gift_wrapping_no_message_yes',
                array('gift_options_message_no_wrapping_no', 'gift_options_message_yes_wrapping_no')
            )
        );
    }

    /**
     * @TestlinkId TL-MAGE-878
     * <p>Managing Price for Gift Wrapping on product level. Frontend. Case1</p>
     * <p>Verify that when Price for Gift Wrapping in a product Menu is different from
     * price setting in Manage Gift Wrapping Menu,
     * then price for Gift Wrapping for that product in Frontend is equal to first one.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function managingPriceForGiftWrappingOnProductLevelCase1($testData)
    {
        //$this->markTestSkipped('\BUG: data is duplicated for all item gift options');
        //Data
        $options = $this->loadDataSet('GiftWrapping', 'gift_options_custom_wrapping_price');
        $verifyWrapping = $this->loadDataSet('OnePageCheckout', 'verify_wrapping_data', null, array(
            'gift_item_product_1' => $testData['sku'][0],
            'price_product_1' => '$' . $options['autosettings_price_for_gift_wrapping'],
            'product1wrapping' => $testData['wrapping'],
            'product2wrapping' => $testData['wrapping'],
            'gift_item_product_2' => $testData['sku'][1],
            'price_product_2' => '$' . $testData['wrapping_price'],
            'order_wrapping' => $testData['wrapping'],
            'price_order' => '$' . $testData['wrapping_price']
        ));
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_wrapping_custom_price',
            array(
                'gift_wrapping_for_order' => '$' . $testData['wrapping_price'],
                'gift_wrapping_for_items' => '$' .
                    ($options['autosettings_price_for_gift_wrapping'] + $testData['wrapping_price'])
            ),
            array(
                'gift_item_product_1' => $testData['name'][0],
                'gift_item_product_2' => $testData['name'][1],
                'add_product_1' => $testData['name'][0],
                'add_product_2' => $testData['name'][1],
                'product1wrapping' => $testData['wrapping'],
                'product2wrapping' => $testData['wrapping'],
                'order_wrapping' => $testData['wrapping']
            )
        );
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_all_yes_order_all_yes');
        //Steps
        $this->_updateProductGiftOptions($testData['name'][0], $options);
        $this->_updateProductGiftOptions($testData['name'][1], 'gift_options_message_no_wrapping_yes');
        $this->customerHelper()->frontLoginCustomer($testData['defaultUser']);
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $this->orderHelper()->verifyGiftOptions($verifyWrapping);
    }

    /**
     * <p>Update product gift options</p>
     *
     * @param $productName
     * @param $settings
     */
    private function _updateProductGiftOptions($productName, $settings)
    {
        $settings = (is_string($settings)) ? $this->loadDataSet('GiftWrapping', $settings) : $settings;
        $this->navigate('manage_products');
        $this->productHelper()->openProduct(array('product_name' => $productName));
        $this->productHelper()->fillProductTab($settings, 'autosettings');
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
    }
}
