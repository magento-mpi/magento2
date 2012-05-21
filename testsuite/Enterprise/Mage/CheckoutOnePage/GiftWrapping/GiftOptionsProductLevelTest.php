<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Tests Gift Options on Product Level
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_CheckoutOnePage_GiftWrapping_GiftOptionsProductLevelTest extends Mage_Selenium_TestCase
{
    public function assertPreconditions()
    {
        $this->loginAdminUser();
        //Load default application settings
        $this->_configHelper->getConfigAreas(true);
    }

    protected function tearDownAfterTest()
    {
        //Load default application settings
        $this->_configHelper->getConfigAreas(true);
    }

    /**
     * <p>Preconditions:</p>
     * <p>Create Staging Website</p>
     *
     * @return array $website
     * @test
     */
    public function createWebsite()
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('staging_website_enable_auto_entries');
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
     * @depends createWebsite
     * @test
     * @return array $productData
     */
    public function preconditionsCreateProduct($website)
    {
        $products = array();
        for ($i = 0; $i < 2; $i++){
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
     * @depends createWebsite
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
        $this->_configHelper->setAreaBaseUrl('frontend', $newFrontendUrl);
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
     * @depends createWebsite
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
    public function updateProductGiftOptions($productName, $productGiftSettings)
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
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Wrapping" for Order Items" and
     * "Allow Gift Message"s for Order Items"is set to "No".</p>
     * <p>Steps:</p>
     * <p>1. In backend Go to Catalog > Manage Products.</p>
     * <p>2. Select some simple product and choose Gift Options.</p>
     * <p>3. Set "Allow Gift Wrapping"  and "Allow Gift Message"s  to "Yes" and Save the product.</p>
     * <p>4. In Frontend add the above product to the shopping cart and proceed to onepage checkout</p>
     * <p>5. Fill all required fields in billing and shipping addresses and press "Continue" button. </p>
     * <p>6. Select checkbox "Add gift options".</p>
     * <p>7. Select checkbox "Add gift options for Individual Items".</p>
     * <p>Expected result:</p>
     * <p>Item has dropdown "Gift Wrapping Design" and Gift Messages fields.<p>
     * <p>8. Choose "Gift Wrapping design" and fill Gift Messages fields, press "Continue" button.</p>
     * <p>9. Select Payment Method "Check/Money order" and press "Continue" button.</p>
     * <p>Expected result:</p>
     * <p>Cost of Gift Wrapping is correctly included in Grand Total<p>
     * <p>10. Press "PLACE ORDER" button.</p>
     * <p>Expected result:</p>
     * <p>New page with message "Your order has been received" is displayed.
     * In Backend new order is present and contains all and correct information (price, design, text, names, etc.)
     * about the selected Gift Wrapping and Gift Messages.
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
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_message_and_wrapping_all_disable');
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
        $this->updateProductGiftOptions($products[0]['general_name'], 'gift_options_message_yes_wrapping_yes');
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->addParameter('order_id', '#' . $orderId);
        $this->searchAndOpen(array('filter_order_id' => $orderId));
        $this->orderHelper()->verifyGiftMessage($vrfGiftData);
    }

    /**
     * @TestlinkId TL-MAGE-827
     * <p>Gift Options on product level set to No. FrontEnd. </p>
     * <p>Verify that when "Allow Gift Wrapping" and Gift Messages for Order Items in Config set to "Yes",
     * but Gift Wrapping and Gift Messages in a product Menu Gift Options set to "No",
     * then Gift Wrapping and Gift Messages for that product in Frontend on item level is not available.</p>
     * <p>Preconditions:</p>
     * <p>1. "Allow Gift Wrapping" on Order Level" set to "No"</p>
     * <p>2. "Allow Gift Message" on Order Level" set to "No"</p>
     * <p>3. "Allow Gift Message"s for Order Items" set to "Yes"</p>
     * <p>4. "Allow Gift Wrapping" for Order Items" set to "Yes"</p>
     * <p>Steps:</p>
     * <p>1. In backend Go to Catalog > Manage Products</p>
     * <p>2. Select some simple product and choose Gift Options.</p>
     * <p>3. Set "Allow Gift Wrapping" and "Allow Gift Message"s to "No", then Save the product.</p>
     * <p>4. In Frontend add the above product to the shopping cart and proceed to onepage checkout.</p>
     * <p>5. Fill all required fields in billing and shipping addresses and press "Continue" button.</p>
     * <p>Expected result:</p>
     * <p>"Add gift options" with checkbox is absent on the checkout page when you choose shipping method.<p>
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
        $this->systemConfigurationHelper()->configure('gift_options_disable_all');
        $this->systemConfigurationHelper()->configure('ind_items_all_yes_order_all_no');
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('product_1' => $products[0]['general_name']));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->updateProductGiftOptions($products[0]['general_name'], 'gift_options_message_no_wrapping_no');
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->clickControl('link', 'back', false);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        //Verification
        if ($this->controlIsPresent('checkbox', 'add_gift_options')) {
            $xpath = $this->_getControlXpath('checkbox', 'add_gift_options');
            $this->assertFalse($this->isVisible($xpath), '"Add gift options checkbox is visible');
        }
    }

    /**
     * @TestlinkId TL-MAGE-831
     * <p>Gift Options on product level set to No. FrontEnd</p>
     * <p>Verify that when "Allow Gift Wrapping" and Gift Messages for Order Items in Config set to "Yes",
     * but Gift Wrapping and Gift Messages in a product Menu Gift Options set to "No",
     * then Gift Wrapping and Gift Messages for that product in Frontend on item level is not available.</p>
     * <p>Preconditions:</p>
     * <p>1. "Allow Gift Wrapping" on Order Level" set to "Yes"</p>
     * <p>2. "Allow Gift Message" on Order Level" set to "Yes"</p>
     * <p>3. "Allow Gift Message"s for Order Items" set to "No"</p>
     * <p>4. "Allow Gift Wrapping" for Order Items" set to "No"</p>
     * <p>Steps:</p>
     * <p>1. In backend Go to Catalog > Manage Products</p>
     * <p>2. Select some simple product and choose Gift Options.</p>
     * <p>3. Set "Allow Gift Wrapping" and "Allow Gift Message"s to "No", then Save the product.</p>
     * <p>4. In Frontend add the above product to the shopping cart and proceed to onepage checkout.</p>
     * <p>5. Fill all required fields in billing and shipping addresses and press "Continue" button.</p>
     * <p>6. Select checkbox "Add gift options".</p>
     * <p>Expected result:</p>
     * <p>"Add gift options" with checkbox is absent on the checkout page when you choose shipping method.<p>
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
        $this->systemConfigurationHelper()->configure('gift_options_disable_all');
        $this->systemConfigurationHelper()->configure('ind_items_all_yes_order_all_no');
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('product_1' => $products[0]['general_name']));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->updateProductGiftOptions($products[0]['general_name'], 'gift_options_message_no_wrapping_no');
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->clickControl('link', 'back', false);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        $this->fillCheckbox('add_gift_options', 'Yes');
        //Verification
        $this->addParameter('productName', $products[0]['general_name']);
        if ($this->controlIsPresent('checkbox', 'gift_option_for_item')) {
            $xpath = $this->_getControlXpath('checkbox', 'gift_option_for_item');
            $this->assertFalse($this->isVisible($xpath), '"Add gift options" for the Item checkbox is visible');
        }
        if ($this->controlIsPresent('link', 'item_gift_message')) {
            $xpath = $this->_getControlXpath('link', 'item_gift_message');
            $this->assertFalse($this->isVisible($xpath), '"Gift Message" link is visible'  );
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
     * <p>Preconditions:</p>
     * <p>1. "Allow Gift Wrapping" on Order Level" set to "Yes"</p>
     * <p>2. "Allow Gift Message" on Order Level" set to "Yes"</p>
     * <p>3. "Allow Gift Message"s for Order Items" set to "Yes"</p>
     * <p>4. "Allow Gift Wrapping" for Order Items" set to "Yes"</p>
     * <p>Steps:</p>
     * <p>1. In backend Go to Catalog > Manage Products</p>
     * <p>2. Select some simple product and choose Gift Options.</p>
     * <p>3. Set "Allow Gift Wrapping" to "No" and Save the product. (TL-MAGE-832)</p>
     * <p>3. Set "Allow Gift Message" to "No" and Save the product. (TL-MAGE-849)</p>
     * <p>4. In Frontend add the above product to the shopping cart and proceed to onepage checkout.</p>
     * <p>5. Fill all required fields in billing and shipping addresses and press "Continue" button.</p>
     * <p>6. Select checkbox "Add gift options".</p>
     * <p>7. Select checkbox "Add gift options for Individual Items".</p>
     * <p>Expected result:</p>
     * <p>Item does not have "Gift Wrapping Design" dropdown.<p>
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
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_options_disable_all');
        $this->systemConfigurationHelper()->configure('ind_items_all_yes_order_all_no');
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('product_1' => $products[0]['general_name']));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->updateProductGiftOptions($products[0]['general_name'], $productGiftOptions);
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
     * <p>Preconditions:</p>
     * <p>1. "Allow Gift Wrapping" on Order Level" set to "No"</p>
     * <p>2. "Allow Gift Message" on Order Level" set to "No"</p>
     * <p>3. "Allow Gift Message"s for Order Items" set to "No"</p>
     * <p>4. "Allow Gift Wrapping" for Order Items" set to "Yes"</p>
     * <p>Steps:</p>
     * <p>1. In backend Go to Catalog > Manage Products</p>
     * <p>2. Select some simple product and choose Gift Options.</p>
     * <p>3. Set "Allow Gift Wrapping" to "No" and Save the product. (TL-MAGE-845)</p>
     * <p>3. Set "Allow Gift Message" to "No" and Save the product. (TL-MAGE-851)</p>
     * <p>4. In Frontend add two or more products (includung the product from step 2) to the shopping cart
     * and proceed to onepage checkout.</p>
     * <p>5. Fill all required fields in billing and shipping addresses and press "Continue" button.</p>
     * <p>6. Select checkbox "Add gift options".</p>
     * <p>7. Select checkbox "Add gift options for Individual Items".</p>
     * <p>Expected result:</p>
     * <p>Item from step 2 is absent in list from "Gift Options for Individual Items" <p>
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
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_options_disable_all');
        $this->systemConfigurationHelper()->configure($sysSettings);
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('product_1' => $products[0]['general_name'],
                  'product_2' => $products[1]['general_name']));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->updateProductGiftOptions($products[0]['general_name'], $productGiftOptions[0]);
        $this->updateProductGiftOptions($products[1]['general_name'], $productGiftOptions[1]);
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
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Wrapping" for Order Items" and
     * "Allow Gift Message"s for Order Items"is set to "Yes".</p>
     * <p>Steps:</p>
     * <p>1. Go to Sales > Gift Wrapping and note price (for example 2.25) for Gift Wrapping in your store.</p>
     * <p>2. In backend Go to Catalog > Manage Products.</p>
     * <p>3. Select some simple product and choose Gift Options.</p>
     * <p>4. In  "Price for Gift Wrapping" field a set any price different from the above (for example 10).</p>
     * <p>5. In Frontend add the above product and some other product with config gift option to the shopping cart and
     * proceed to checkout.</p>
     * <p>6. Fill all required fields in billing and shipping addresses and press "Continue" button.</p>
     * <p>7. Select checkbox "Add gift options".</p>
     * <p>8. Select checkboxes "Add gift options for the Entire Order" and "Gift Options for Individual Items".</p>
     * <p>9. Select Gift Wrapping Design for entire order and individual items.</p>
     * <p>Expected result:</p>
     * <p>Price on Gift Wrapping for item from step 3 is equal to 10<p>
     * <p>Prices on Gift Wrappings for another item and entire order is equal to 2.25.<p>
     * <p>10. Press "Continue" button and  Select Payment Method "Check/Money order" then press "Continue" button
     * one more.</p>
     * <p>Expected result:</p>
     * <p>Cost of Gift Wrapping is correctly included in Grand Total<p>
     * <p>11. Press "PLACE ORDER" button.</p>
     * <p>Expected result:</p>
     * <p>New page with message "Your order has been received" is displayed.<p>
     * <p>In Backend new order is present and contains all and correct information (price, design, etc.) about
     * the selected Gift Wrapping.<p>
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
        $this->systemConfigurationHelper()->configure('gift_options_disable_all');
        $this->systemConfigurationHelper()->configure('gift_message_and_wrapping_all_enable');
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
        $this->updateProductGiftOptions($products[0]['general_name'], $productGiftOptions);
        $this->updateProductGiftOptions($products[1]['general_name'], 'gift_options_message_no_wrapping_yes');
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->addParameter('order_id', '#' . $orderId);
        $this->searchAndOpen(array('filter_order_id' => $orderId));
        $this->orderHelper()->verifyGiftWrapping($vrfGiftWrapping);
    }

    /**
     * @TestlinkId TL-MAGE-1041
     * <p>Managing Price for Gift Wrapping on product level for websites view. Frontend. Case2</p>
     * <p>Verify that when Price for Gift Wrapping in a product Menu (for store scope) is different from
     * prices setting in Manage Gift Wrapping Menu and Product Menu (for dafault values),
     * then price for Gift Wrapping for that product in Frontend is equal to first one (on selected Website scope).</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Wrapping" for Order Items" and
     * "Allow Gift Message"s for Order Items" are set to "Yes".</p>
     * <p>Steps:</p>
     * <p>1. Go to Sales > Gift Wrapping and note price (for example 2.25) for Gift Wrapping in your store.</p>
     * <p>2. In backend Go to Catalog > Manage Products.</p>
     * <p>3. Select some simple product and choose Gift Options.</p>
     * <p>4. Notice that dropdown "Choose Store View" should display "Default Values".
     * In "Price for Gift Wrapping" field set any price different from the above (for example 10).
     * Press "Save and Continue" button. </p>
     * <p>5. Select in dropdown "Choose Store View" your store.</p>
     * <p>6. Press OK in notification window .</p>
     * <p>Expected result:</p>
     * <p>Field "Price for Gift Wrapping" is disabled (vith value 10 in our example) and checkbox "Use Default Value"
     * must be selected for one.</p>
     * <p>7. Unselect checkbox "Use Default Value" for field "Price for Gift Wrapping" and set field value to
     * different from the both values above (for example 20). Save the product changes.</p>
     * <p>8. In Frontend add the above product and some other product with config gift option to the shopping cart
     * and proceed to checkout.</p>
     * <p>9. Fill all required fields in billing and shipping addresses and press "Continue" button.</p>
     * <p>10. Select checkbox "Add gift options".</p>
     * <p>11. Select checkboxes "Add gift options for the Entire Order" and "Gift Options for Individual Items".</p>
     * <p>12. Select Gift Wrapping Design for entire order and individual items.</p>
     * <p>Expected result:</p>
     * <p>Price on Gift Wrapping for item from step 3 is equal to 20<p>
     * <p>Prices on Gift Wrappings for another item and entire order are equal to 2.25.<p>
     * <p>13. Press "Continue" button, select Payment Method "Check/Money order", then press "Continue" again.</p>
     * <p>Expected result:</p>
     * <p>Cost of Gift Wrapping is correctly included in Grand Total<p>
     * <p>14. Press "PLACE ORDER" button.	After step 6.</p>
     * <p>Expected result:</p>
     * <p>New page with message "Your order has been received" is displayed.<p>
     * <p>In Backend new order is present and contains all and correct information (price, design, etc.) about
     * the selected Gift Wrapping.<p>
     *
     * @param $products
     * @param $userData
     * @param $website
     * @param $website
     * @param $giftWrappingData
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomerForWebsite
     * @depends createWebsite
     * @depends preconditionsGiftWrapping
     * @test
     */
    public function managingPriceForGiftWrappingOnProductLevelCase2($products, $userData, $website, $giftWrappingData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_wrapping_all_enable');
        $this->systemConfigurationHelper()->configure('gift_options_website_price_scope');
        //Data
        $productGiftOptions = $this->loadDataSet('GiftWrapping', 'gift_options_custom_wrapping_price');
        $productGiftOptionsSite = $this->loadDataSet('GiftWrapping',
            'gift_options_custom_wrapping_price_on_store_view');
        $vrfGiftWrapping = $this->loadDataSet('OnePageCheckout', 'verify_wrapping_data', null,
            array('gift_wrapping_design'    => $giftWrappingData['gift_wrapping_design'],
                  'sku_product_1'           => $products[0]['general_sku'],
                  'sku_product_2'           => $products[1]['general_sku'],
                  'price_order'             => '$' . $giftWrappingData['gift_wrapping_price'],
                  'price_product_1'         => '$' . $productGiftOptionsSite['gift_options_price_for_gift_wrapping'],
                  'price_product_2'         => '$' . $giftWrappingData['gift_wrapping_price']));
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_wrapping_custom_price_on_site',
            array('gift_wrapping_for_order' => '$' . $giftWrappingData['gift_wrapping_price'],
                  'gift_wrapping_for_items' => '$' . ($productGiftOptionsSite['gift_options_price_for_gift_wrapping'] +
                                               $giftWrappingData['gift_wrapping_price'])),
            array('product_1'               => $products[0]['general_name'],
                  'product_2'               => $products[1]['general_name'],
                  'validate_product_1'      => $products[0]['general_name'],
                  'validate_product_2'      => $products[1]['general_name'],
                  'gift_wrapping_design'    => $giftWrappingData['gift_wrapping_design']

            ));
        //Steps
        $this->updateProductGiftOptions($products[1]['general_name'], 'gift_options_message_yes_wrapping_yes');
        $this->navigate('manage_products');
        $this->productHelper()->openProduct(array('product_name' => $products[0]['general_name']));
        $this->chooseOkOnNextConfirmation();
        $this->fillForm(array('choose_store_view' => 'Default Values'));
        $this->productHelper()->fillTab($productGiftOptions, 'gift_options');
        $this->addParameter('tabId', '0');
        $this->addParameter('storeId', '0');
        $this->clickButton('save_and_continue_edit');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->chooseOkOnNextConfirmation();
        $this->productHelper()->chooseStoreView($website['general_information']['staging_website_name'] .
                                                '/Main Website Store/Default Store View');
        $this->getConfirmation();
        $this->waitForPageToLoad();
        $this->productHelper()->fillTab($productGiftOptionsSite, 'gift_options');
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        $newFrontendUrl = $this->stagingWebsiteHelper()->buildFrontendUrl(
            $website['general_information']['staging_website_code']);
        $this->_configHelper->setAreaBaseUrl('frontend', $newFrontendUrl);
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->addParameter('order_id', '#' . $orderId);
        $this->searchAndOpen(array('filter_order_id' => $orderId));
        $this->orderHelper()->verifyGiftWrapping($vrfGiftWrapping);
    }
}
