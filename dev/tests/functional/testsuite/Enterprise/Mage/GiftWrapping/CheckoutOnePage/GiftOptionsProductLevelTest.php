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
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_disable_all');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_use_default_per_website');
    }

    public function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        //Load default application settings
        $this->getConfigHelper()->getConfigAreas(true);
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_disable_all');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_use_default_per_website');
    }

    /**
     * <p>Preconditions:</p>
     * <p>Create Staging Website</p>
     *
     * @return array $website
     * @test
     */
    public function preconditionsCreateWebsite()
    {
        $this->markTestIncomplete("Enterprise_Staging is obsolete. The tests should be refactored.");
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('StagingWebsite/staging_website_enable_auto_entries');
        //Data
        $website = $this->loadDataSet('StagingWebsite', 'staging_website');
        //Steps
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->createStagingWebsite($website);
        //Verification
        $this->assertMessagePresent('success', 'success_created_website');
        return $website;
    }

    /**
     * <p>Creating Simple products</p>
     *
     * @param $website
     * @depends preconditionsCreateWebsite
     * @test
     * @return array $productData
     */
    public function preconditionsCreateProduct($website)
    {
        $products = array();
        for ($i = 0; $i < 2; $i++) {
            //Data
            $productData = $this->loadDataSet('Product', 'simple_product_visible');
            $productData['websites'] .= ',' . $website['general_information']['staging_website_name'];
            //Steps
            $this->navigate('manage_products');
            $this->productHelper()->createProduct($productData);
            //Verification
            $this->assertMessagePresent('success', 'success_saved_product');
            $products[] = $productData;
        }
        return $products;
    }

    /**
     * <p>Create Customer</p>
     *
     * @depends preconditionsCreateWebsite
     * @param array $website
     * @return array $userData
     * @test
     */
    public function preconditionsCreateCustomerForWebsite($website)
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps
        $newFrontendUrl = $this->stagingWebsiteHelper()->buildFrontendUrl(
            $website['general_information']['staging_website_code']);
        $this->getConfigHelper()->setAreaBaseUrl('frontend', $newFrontendUrl);
        $this->logoutCustomer();
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        //Verification
        $this->assertMessagePresent('success', 'success_registration');
        return array('email' => $userData['email'], 'password' => $userData['password']);
    }

    /**
     * <p>Create Customer</p>
     *
     * @return array $userData
     * @test
     */
    public function preconditionsCreateCustomer()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps
        $this->logoutCustomer();
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        //Verification
        $this->assertMessagePresent('success', 'success_registration');
        return array('email' => $userData['email'], 'password' => $userData['password']);
    }

    /**
     * <p>Create GiftWrapping</p>
     *
     * @param $website
     * @depends preconditionsCreateWebsite
     * @return array $giftWrappingData
     * @test
     */
    public function preconditionsGiftWrapping($website)
    {
        //Data
        $giftWrappingData = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $giftWrappingData['gift_wrapping_websites'] .= ',' . $website['general_information']['staging_website_name'];
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        return $giftWrappingData;
    }

    /**
     * <p>Update product gift options</p>
     *
     * @param $productName
     * @param $productGiftSettings
     */
    private function _updateProductGiftOptions($productName, $productGiftSettings)
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->openProduct(array('product_name' => $productName));
        $productGiftSettings = (is_string($productGiftSettings)) ?
            $this->loadDataSet('GiftWrapping', $productGiftSettings) : $productGiftSettings;
        $this->productHelper()->fillTab($productGiftSettings, 'gift_options');
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    /**
     * @TestlinkId TL-MAGE-826
     * <p>Gift Options on product level set to Yes. FrontEnd. Case1.</p>
     * <p>Verify that when "Allow Gift Wrapping" and Gift Messages for Order Items in SysConfig set to "No",
     * but Gift Wrapping and Gift Messages in a product Menu Gift Options set to "Yes",
     * then Gift Wrapping and Gift messages for that product in Frontend on item level are available.</p>
     *
     * @param $products
     * @param $userData
     * @param $giftWrappingData
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @depends preconditionsGiftWrapping
     * @test
     */
    public function giftOptionsOnProductLevelSetToYes($products, $userData, $giftWrappingData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'recount_gift_wrapping_no_img_one_page',
            array ('gift_wrapping_for_items'    => '$' . $giftWrappingData['gift_wrapping_price'],
                   'item_gift_wrapping_design' => $giftWrappingData['gift_wrapping_design']),
            array('product_1'                   => $products[0]['general_name'],
                  'validate_product_1'         => $products[0]['general_name']));
        $giftMsg = $checkoutData['shipping_data']['add_gift_options']['individual_items']['item_1'];
        $vrfGiftData = $this->loadDataSet('OnePageCheckout', 'verify_gift_data',
            array('sku_product'                 => $products[0]['general_sku'],
                  'from'                        => $giftMsg['gift_message']['item_gift_message_from'],
                  'to'                          => $giftMsg['gift_message']['item_gift_message_to'],
                  'message'                     => $giftMsg['gift_message']['item_gift_message']));
        $vrfGiftData = $this->clearDataArray($vrfGiftData);
        //Steps
        $this->_updateProductGiftOptions($products[0]['general_name'], 'gift_options_message_yes_wrapping_yes');
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->addParameter('elementTitle', '#' . $orderId);
        $this->searchAndOpen(array('filter_order_id' => $orderId), 'sales_order_grid');
        $this->orderHelper()->verifyGiftMessage($vrfGiftData);
    }

    /**
     * @TestlinkId TL-MAGE-827
     * <p>Gift Options on product level set to No. FrontEnd. </p>
     * <p>Verify that when "Allow Gift Wrapping" and Gift Messages for Order Items in Config set to "Yes",
     * but Gift Wrapping and Gift Messages in a product Menu Gift Options set to "No",
     * then Gift Wrapping and Gift Messages for that product in Frontend on item level is not available.</p>
     *
     * @param $products
     * @param $userData
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @test
     */
    public function giftOptionsOnProductLevelSetToNoCase1($products, $userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_all_yes_order_all_no');
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('product_1' => $products[0]['general_name']));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->_updateProductGiftOptions($products[0]['general_name'], 'gift_options_message_no_wrapping_no');
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->clickControl('link', 'back', false);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        //Verification
        if ($this->controlIsPresent('checkbox', 'add_gift_options')) {
            $this->assertFalse($this->controlIsVisible('checkbox', 'add_gift_options'),
                '"Add gift options" checkbox is visible');
        }
    }

    /**
     * @TestlinkId TL-MAGE-831
     * <p>Gift Options on product level set to No. FrontEnd</p>
     * <p>Verify that when "Allow Gift Wrapping" and Gift Messages for Order Items in Config set to "Yes",
     * but Gift Wrapping and Gift Messages in a product Menu Gift Options set to "No",
     * then Gift Wrapping and Gift Messages for that product in Frontend on item level is not available.</p>
     *
     * @param $products
     * @param $userData
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @test
     */
    public function giftOptionsOnProductLevelSetToNoCase2($products, $userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_all_yes_order_all_no');
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('product_1' => $products[0]['general_name']));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->_updateProductGiftOptions($products[0]['general_name'], 'gift_options_message_no_wrapping_no');
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->clickControl('link', 'back', false);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        $this->fillCheckbox('add_gift_options', 'Yes');
        //Verification
        $this->addParameter('productName', $products[0]['general_name']);
        if ($this->controlIsPresent('checkbox', 'gift_option_for_item')) {
            $this->assertFalse($this->controlIsVisible('checkbox', 'gift_option_for_item'),
                '"Add gift options" for the Item checkbox is visible');
        }
        if ($this->controlIsPresent('link', 'item_gift_message')) {
            $this->assertFalse($this->controlIsVisible('link', 'item_gift_message'),
                '"Gift Message" link is visible'  );
        }
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
     * @param $productGiftOptions
     * @param $assert
     * @param $products
     * @param $userData
     * @dataProvider giftOptionsOnProductLevelSetToNoCase3Case4DataProvider
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @test
     */
    public function giftOptionsOnProductLevelSetToNoCase3Case4($productGiftOptions, $assert, $products, $userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_all_yes_order_all_no');
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('product_1' => $products[0]['general_name']));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->_updateProductGiftOptions($products[0]['general_name'], $productGiftOptions);
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->clickControl('link', 'back', false);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        $this->fillCheckbox('add_gift_options', 'Yes');
        $this->fillCheckbox('gift_option_for_item', 'Yes');
        //Verification
        $this->addParameter('productName', $products[0]['general_name']);
        $this->$assert['wrapping']($this->controlIsPresent('dropdown', 'item_gift_wrapping_design'),
            '"Gift Wrapping Design" is ' . (($assert['wrapping'] == 'assertTrue') ? 'not visible' : 'visible'));
        $this->$assert['message']($this->controlIsPresent('link', 'item_gift_message'),
            '"Gift Message" link is ' . (($assert['message'] == 'assertTrue') ? 'not visible' : 'visible'));

    }

    public function giftOptionsOnProductLevelSetToNoCase3Case4DataProvider()
    {
        return array(
            array('gift_options_message_yes_wrapping_no',
                  array('message' => 'assertTrue', 'wrapping' => 'assertFalse')),
            array('gift_options_message_no_wrapping_yes',
                  array('message' => 'assertFalse', 'wrapping' => 'assertTrue'))
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
     * @param $sysSettings
     * @param $productGiftOptions
     * @param $products
     * @param $userData
     * @dataProvider giftOptionsOnProductLevelSetToNoCase5Case6DataProvider
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @test
     */
    public function giftOptionsOnProductLevelSetToNoCase5Case6($sysSettings, $productGiftOptions, $products, $userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/' . $sysSettings);
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('product_1' => $products[0]['general_name'],
                  'product_2' => $products[1]['general_name']));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->_updateProductGiftOptions($products[0]['general_name'], $productGiftOptions[0]);
        $this->_updateProductGiftOptions($products[1]['general_name'], $productGiftOptions[1]);
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->clickControl('link', 'back', false);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        $this->fillCheckbox('add_gift_options', 'Yes');
        $this->fillCheckbox('gift_option_for_item', 'Yes');
        //Verification
        $this->addParameter('productName', $products[0]['general_name']);
        $this->assertFalse($this->controlIsPresent('pageelement', 'item_product_name'),
            'Product is visible in Gift Options for Individual items');
        $this->addParameter('productName', $products[1]['general_name']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'item_product_name'),
            'Product is not visible in Gift Options for Individual items');
    }

    public function giftOptionsOnProductLevelSetToNoCase5Case6DataProvider()
    {
        return array(
            array('ind_items_gift_wrapping_yes_message_no',
                  array(0 => 'gift_options_message_no_wrapping_no', 1 => 'gift_options_message_no_wrapping_yes')),
            array('ind_items_gift_wrapping_no_message_yes',
                  array(0 => 'gift_options_message_no_wrapping_no', 1 => 'gift_options_message_yes_wrapping_no'))
        );
    }

    /**
     * @TestlinkId TL-MAGE-878
     * <p>Managing Price for Gift Wrapping on product level. Frontend. Case1</p>
     * <p>Verify that when Price for Gift Wrapping in a product Menu is different from
     * price setting in Manage Gift Wrapping Menu,
     * then price for Gift Wrapping for that product in Frontend is equal to first one.</p>
     *
     * @param $products
     * @param $userData
     * @param $giftWrappingData
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @depends preconditionsGiftWrapping
     * @test
     */
    public function managingPriceForGiftWrappingOnProductLevelCase1($products, $userData, $giftWrappingData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_all_yes_order_all_yes');
        //Data
        $productGiftOptions = $this->loadDataSet('GiftWrapping', 'gift_options_custom_wrapping_price');
        $vrfGiftWrapping = $this->loadDataSet('OnePageCheckout', 'verify_wrapping_data', null,
            array('gift_wrapping_design' => $giftWrappingData['gift_wrapping_design'],
                  'sku_product_1'             => $products[0]['general_sku'],
                  'sku_product_2'             => $products[1]['general_sku'],
                  'price_order'               => '$' . $giftWrappingData['gift_wrapping_price'],
                  'price_product_1'           => '$' . $productGiftOptions['gift_options_price_for_gift_wrapping'],
                  'price_product_2'           => '$' . $giftWrappingData['gift_wrapping_price']));
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_wrapping_custom_price',
            array('gift_wrapping_for_order'   => '$' . $giftWrappingData['gift_wrapping_price'],
                  'gift_wrapping_for_items'   => '$' . ($productGiftOptions['gift_options_price_for_gift_wrapping'] +
                                                        $giftWrappingData['gift_wrapping_price'])),
            array('product_1'                 => $products[0]['general_name'],
                  'product_2'                 => $products[1]['general_name'],
                  'validate_product_1'        => $products[0]['general_name'],
                  'validate_product_2'        => $products[1]['general_name'],
                  'gift_wrapping_design'      => $giftWrappingData['gift_wrapping_design']

            ));
        //Steps
        $this->_updateProductGiftOptions($products[0]['general_name'], $productGiftOptions);
        $this->_updateProductGiftOptions($products[1]['general_name'], 'gift_options_message_no_wrapping_yes');
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->addParameter('elementTitle', '#' . $orderId);
        $this->searchAndOpen(array('filter_order_id' => $orderId), 'sales_order_grid');
        $this->orderHelper()->verifyGiftWrapping($vrfGiftWrapping);
    }

    /**
     * @TestlinkId TL-MAGE-1041
     * <p>Managing Price for Gift Wrapping on product level for websites view. Frontend. Case2</p>
     * <p>Verify that when Price for Gift Wrapping in a product Menu (for store scope) is different from
     * prices setting in Manage Gift Wrapping Menu and Product Menu (for default values),
     * then price for Gift Wrapping for that product in Frontend is equal to first one (on selected Website scope).</p>
     *
     * @param $products
     * @param $userData
     * @param $website
     * @param $website
     * @param $giftWrappingData
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomerForWebsite
     * @depends preconditionsCreateWebsite
     * @depends preconditionsGiftWrapping
     * @test
     */
    public function managingPriceForGiftWrappingOnProductLevelCase2($products, $userData, $website, $giftWrappingData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_wrapping_all_enable');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_website_price_scope');
        //Data
        $productGiftOptions = $this->loadDataSet('GiftWrapping', 'gift_options_custom_wrapping_price');
        $productGOSite = $this->loadDataSet('GiftWrapping',
            'gift_options_custom_wrapping_price_on_store_view');
        $vrfGiftWrapping = $this->loadDataSet('OnePageCheckout', 'verify_wrapping_data', null,
            array('gift_wrapping_design'    => $giftWrappingData['gift_wrapping_design'],
                  'sku_product_1'           => $products[0]['general_sku'],
                  'sku_product_2'           => $products[1]['general_sku'],
                  'price_order'             => '$' . $giftWrappingData['gift_wrapping_price'],
                  'price_product_1'         => '$' . $productGOSite['gift_options_price_for_gift_wrapping'],
                  'price_product_2'         => '$' . $giftWrappingData['gift_wrapping_price']));
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_wrapping_custom_price_on_site',
            array('gift_wrapping_for_order' => '$' . $giftWrappingData['gift_wrapping_price'],
                  'gift_wrapping_for_items' => '$' . ($productGOSite['gift_options_price_for_gift_wrapping'] +
                                                      $giftWrappingData['gift_wrapping_price'])),
            array('product_1'               => $products[0]['general_name'],
                  'product_2'               => $products[1]['general_name'],
                  'validate_product_1'      => $products[0]['general_name'],
                  'validate_product_2'      => $products[1]['general_name'],
                  'gift_wrapping_design'    => $giftWrappingData['gift_wrapping_design']

            ));
        //Steps
        $this->_updateProductGiftOptions($products[1]['general_name'], 'gift_options_message_yes_wrapping_yes');
        $this->navigate('manage_products');
        $this->productHelper()->openProduct(array('product_name' => $products[0]['general_name']));
        $this->selectStoreScope('dropdown', 'choose_store_view', 'Default Values');
        $this->acceptAlert();
        $this->productHelper()->fillTab($productGiftOptions, 'gift_options');
        $this->clickButton('save_and_continue_edit');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->acceptAlert();
        $scope = $website['general_information']['staging_website_name'] . '/Main Website Store/Default Store View';
        $this->selectStoreScope('dropdown', 'choose_store_view', $scope);
        $this->acceptAlert();
        $this->waitForPageToLoad();
        $this->productHelper()->fillTab($productGOSite, 'gift_options');
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        $newFrontendUrl = $this->stagingWebsiteHelper()->buildFrontendUrl(
            $website['general_information']['staging_website_code']);
        $this->getConfigHelper()->setAreaBaseUrl('frontend', $newFrontendUrl);
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->addParameter('elementTitle', '#' . $orderId);
        $this->searchAndOpen(array('filter_order_id' => $orderId), 'sales_order_grid');
        $this->orderHelper()->verifyGiftWrapping($vrfGiftWrapping);
    }
}
