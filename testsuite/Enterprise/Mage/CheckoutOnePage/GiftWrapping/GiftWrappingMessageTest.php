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
 * Tests Gift Wrapping.
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_CheckoutOnePage_GiftWrapping_GiftWrappingMessageTest extends Mage_Selenium_TestCase
{
    public function assertPreconditions()
    {
        $this->loginAdminUser();
        //load default application settings
        $this->_configHelper->getConfigAreas();
    }

    protected function tearDownAfterTest()
    {
        //load default application settings
        $this->_configHelper->getConfigAreas();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Create Staging Website</p>
     *
     * @return array $website
     *
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
     * <p>Creating Simple product</p>
     *
     * @test
     * @return array $productData
     */
    public function preconditionsCreateProduct()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @depends createWebsite
     * @param array $website
     * @return array $productData
     *
     * @test
     *
     */
    public function preconditionsCreateProductForWebsite($website)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible',
            array('websites' => $website['general_information']['staging_website_name']));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * <p>Create Customer</p>
     *
     * @test
     * @return array $userData
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
     * <p>Create Customer</p>
     *
     * @depends createWebsite
     * @param array $website
     * @return array $userData
     *
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
     * <p>Create GiftWrapping without image</p>
     *
     * @test
     * @return array $giftWrappingData
     */
    public function preconditionsGiftWrapping()
    {
        //Data
        $giftWrappingDataWithoutImg = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $giftWrappingDataWithImg = $this->loadDataSet('GiftWrapping', 'gift_wrapping_with_image');
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingDataWithoutImg);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        //Steps
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingDataWithImg);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        return array('img' => $giftWrappingDataWithImg, 'noImg' => $giftWrappingDataWithoutImg);
    }

    /**
     * @TestlinkId TL-MAGE-850
     * <p>Gift Message for entire Order is allowed</p>
     * <p>Verify that when setting "Allow Gift Messages on Order Level" is set to "Yes",customer in process of
     * OnePageCheckout have ability to add Gift Message to entire Order, prompt for which should be present on
     * Shipping Method step, after checking the appropriate checkbox</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Messages on Order Level" is set to "Yes"</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend</p>
     * <p>2. Add any product to a Cart and proceed to checkout</p>
     * <p>3. Select shipping/billing addresses and shipping method</p>
     * <p>4. Check the "Add gift options" checkbox, then check the "Add Gift Options for Entire Order" checkbox</p>
     * <p>Expected result:</p>
     * <p>Link "Gift Message" should be present</p>
     * <p>5. Click on "Gift Message" link</p>
     * <p>Expected result:</p>
     * <p>Prompt for entering Gift Message for Entire Order should appear with From/To/Message fields</p>
     * <p>6. Fill the fields, related to Gift Message for Entire Order (From/To/Message), click on "Continue" button</p>
     * <p>7. On Payment Method step select any payment method (for example, "Check/Money order"), click on "Continue" button</p>
     * <p>8. On Order Review step click on "Place Order" button</p>
     * <p>9. Open newly created Order page in backend</p>
     * <p>Expected result:</p>
     * <p>Gift Options block of Order page should contain information about Gift Message, with the same data (From/To/Message), as specified at Step 6 <p>
     *
     * @param $productData
     * @param $userData
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @test
     */
    public function giftMessageForEntireOrder($productData,$userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_message_for_order_enable');
        //Data
        $checkoutData = $this->loadData('gift_data_general', array (
                                                             'products_to_add/product_1' => $productData,
                                                             'shipping_data/add_gift_options' => $this->loadData('gift_message_with_gift_wrapping_one_page')));
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->addParameter('order_id', '#' . $orderId);
        $this->searchAndOpen(array('filter_order_id' => $orderId));
        $this->orderHelper()->verifyGiftMessage($checkoutData['shipping_data']['add_gift_options']);
    }

    /**
     * @TestlinkId
     * @TestlinkId
     * <p>TL-MAGE-881: Gift Message for entire Order is not allowed (message-no; wrapping-no)</p>
     * <p>TL-MAGE-905: Gift Message for Individual Items is not allowed (message-no; wrapping-no)</p>
     * <p>Verify that when setting "Allow Gift Messages on Order Level" is set to "No",customer in process of
     * OnePageCheckout not able to add Gift Message to entire Order</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Messages on Order Level" is set to "No"</p>
     * <p>2. In system configuration setting "Allow Gift Wrapping on Order Level" is set to "No"</p>
     * <p>3. In system configuration setting "Allow Printed Card" is set to "Yes"</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend</p>
     * <p>2. Add any product to a Cart and proceed to checkout</p>
     * <p>3. Select any address in billing information and press button "Continue"</p>
     * <p>4. Select any address in shipping information and press button "Continue"</p>
     * <p>5. Check the "Add gift options" checkbox</p>
     * <p>Expected result:</p>
     * <p>"Add gift options for the Entire Order" checkbox and "Gift Message" link respectively  should not be visible<p>
     *
     * @param $productData
     * @param $userData
     * @param $entity
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @dataProvider giftMessageIsNotAllowedWrappingNoDataProvider
     * @test
     */
    public function giftMessageIsNotAllowedWrappingNo($entity,$productData,$userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_message_' . $entity . '_disable');
        $this->systemConfigurationHelper()->configure('gift_wrapping_' . $entity . '_disable');
        $this->systemConfigurationHelper()->configure('gift_printed_card_enable');
        $entity = substr($entity,4,strlen($entity));
        //Data
        $checkoutData = $this->loadData('gift_data_general', array('products_to_add/product_1' => $productData));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->fillForm(array('add_gift_options' => 'Yes'));
        //Verification
        $this->addParameter('productName', $productData['general_name']);
        if ($this->controlIsPresent('checkbox', 'gift_option_for_' . $entity)) {
            $xpath = $this->_getControlXpath('checkbox','gift_option_for_' . $entity);
            $this->assertFalse($this->isVisible($xpath), '"Add gift options for the Entire Order" checkbox is visible');
        }
        if ($this->controlIsPresent('link', $entity . '_gift_message')) {
            $xpath = $this->_getControlXpath('link', $entity . '_gift_message');
            $this->assertFalse($this->isVisible($xpath), '"Gift Message" link is visible' . $entity );
        }
    }

    public function giftMessageIsNotAllowedWrappingNoDataProvider()
    {
        return array(
            array('per_item'),
            array('for_order')
        );
    }

    /**
     * @TestlinkId
     * @TestlinkId
     * <p>TL-MAGE-891: Gift Message for entire Order is not allowed (message-no; wrapping-yes)</p>
     * <p>TL-MAGE-906: Gift Message for Individual Items is not allowed (message-no; wrapping-yes)</p>
     * <p>Verify that when setting "Allow Gift Messages on Order Level" is set to "No",customer in process of
     * OnePageCheckout not able to add Gift Message to entire Order</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Messages on Order Level" is set to "No"</p>
     * <p>2. In system configuration setting "Allow Gift Wrapping on Order Level" is set to "Yes"</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend</p>
     * <p>2. Add any product to a Cart and proceed to checkout</p>
     * <p>3. Select any address in billing information and press button "Continue"</p>
     * <p>4. Select any address in shipping information and press button "Continue"</p>
     * <p>5. Check the "Add gift options" checkbox</p>
     * <p>6. Check "Add gift options for the Entire Order" checkbox</p>
     * <p>Expected result:</p>
     * <p>"Gift Message" link  should not be visible<p>
     *
     * @param $productData
     * @param $userData
     * @param $entity
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @dataProvider giftMessageIsNotAllowedWrappingYesDataProvider
     * @test
     */
    public function giftMessageIsNotAllowedWrappingYes($entity,$productData,$userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_message_' . $entity . '_disable');
        $this->systemConfigurationHelper()->configure('gift_wrapping_' . $entity . '_enable');
        $entity = substr($entity,4,strlen($entity));
        //Data
        $checkoutData = $this->loadData('gift_data_general', array('products_to_add/product_1' => $productData));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->fillForm(array('add_gift_options' => 'Yes', 'gift_option_for_order' => 'Yes'));
        //Verification
        $this->addParameter('productName', $productData['general_name']);
        if ($this->controlIsPresent('link', $entity . '_gift_message')) {
            $xpath = $this->_getControlXpath('link', $entity . '_gift_message');
            $this->assertFalse($this->isVisible($xpath), '"Gift Message" link is visible' . $entity );
        }
    }

    public function giftMessageIsNotAllowedWrappingYesDataProvider()
    {
        return array(
            array('per_item'),
            array('for_order')
        );
    }

    /**
     * @TestlinkId
     * <p>TL-MAGE-900: Gift Message for Individual Items is allowed</p>
     * <p>Verify that when setting "Allow Gift Messages for Order Items" is set to "Yes",customer in process of
     * OnePageCheckout have ability to add Gift Message to Individual Items, prompt for which should be present on
     * Shipping Method step, after checking the appropriate checkbox</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Messages for Order Items" is set to "Yes"</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend</p>
     * <p>2. Add any product to a Cart and proceed to checkout</p>
     * <p>3. Select shipping/billing addresses and shipping method</p>
     * <p>4. Check the "Add gift options" checkbox</p>
     * <p>5. Check the "Add gift options for Individual Items" checkbox</p>
     * <p>6. Click on "Gift Message" link</p>
     * <p>Expected result:</p>
     * <p>Prompt for entering Gift Message for  Individual Items should be present there with From/To/Message fields</p>
     * <p>7. Full fill fields, related to Gift Message forIndividual Items (From/To/Message), click on "Continue" button</p>
     * <p>8. On Payment Method step select any payment method (for example, "Check/Money order"), click on "Continue" button</p>
     * <p>9. On Order Review step click on "Place Order" button</p>
     * <p>10. Open newly created Order page in backend</p>
     * <p>Expected result:</p>
     * <p>Gift Options block of Order page should contain information about Gift Message, with the same data (From/To/Message), as specified at Step 7<p>
     *
     * @param $productData
     * @param $userData
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @test
     */
    public function giftMessageForIndividualItemsIsAllowed($productData,$userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_message_per_item_enable');
        //Data
        $individualItemsMessage = $this->loadData('gift_message_for_individual_items_one_page');
        $indItems = array($productData['general_name'] => array('gift_message' => $individualItemsMessage));
        $checkoutData = $this->loadData('gift_data_general', array ('products_to_add/product_1' => $productData));
        $checkoutData['shipping_data']['add_gift_options']['individual_items'] = $indItems;
        $vrfGiftData = $this->loadData('verify_gift_data');
        $vrfGiftData['individual']['product_1']['sku_product'] = $productData['general_sku'];
        $vrfGiftData['individual']['product_1']['product_gift_message_from'] =
            $individualItemsMessage['item_gift_message_from'];
        $vrfGiftData['individual']['product_1']['product_gift_message_to'] =
            $individualItemsMessage['item_gift_message_to'];
        $vrfGiftData['individual']['product_1']['product_gift_message'] = $individualItemsMessage['item_gift_message'];
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->addParameter('order_id','#' . $orderId);
        $this->searchAndOpen(array('filter_order_id' => $orderId));
        $this->orderHelper()->verifyGiftMessage($vrfGiftData);
    }

    /**
     * @TestlinkId
     * @TestlinkId
     * <p>TL-MAGE-898: Gift Wrapping image displaying for Entire Order gift options (image is not specified)</p>
     * <p>TL-MAGE-910: Gift Wrapping image displaying for Individual Items gift options (image is not specified)</p>
     * <p>Test Case TL-MAGE-842: Enabling Gift Wrapping (OnePageCheckout)</p>
     * <p>Verify that when setting "Allow Gift Wrapping on Order Level" is set to "Yes", customer in process of
     * OnePageCheckout have ability to select Gift Wrapping  (with not specified picture which will not be displayed)
     * for entire Order using dropdown "Gift Wrapping Design" on Payment Method step of checkout</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Wrapping on Order Level" is set to "Yes"</p>
     * <p>2. Al least one Gift Wrapping is created and enabled (for example, with Price $10 and image for it is not specified)</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend</p>
     * <p>2. Add any product to a Cart and proceed to checkout</p>
     * <p>3. Select any address in billing information and press button "Continue"</p>
     * <p>4. Select any address in shipping information and press button "Continue"</p>
     * <p>5. Check the "Add gift options" checkbox</p>
     * <p>6. Check "Add gift options for the Entire Order" checkbox</p>
     * <p>7. Select Gift Wrapping Design from "Gift Wrapping Design" dropdown</p>
     * <p>Expected result:</p>
     * <p>"Gift Wrapping Design" will display price without a picture for gift wrapping<p>
     *
     * @param $productData
     * @param $userData
     * @param $giftWrappingData
     * @param $entity
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @depends preconditionsGiftWrapping
     * @dataProvider giftWrappingImageDisplayingGiftOptionsDataProvider
     * @test
     */
    public function giftWrappingImageDisplayingGiftOptions($entity,$productData,$userData,$giftWrappingData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_wrapping_' . $entity . '_enable');
        $entity = substr($entity,4,strlen($entity));
        //Data
        $checkoutData = $this->loadData('gift_data_general', array ('products_to_add/product_1' => $productData));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->fillForm(array('add_gift_options' => 'Yes',
                              'gift_option_for_' .  $entity => 'Yes',
                              $entity . '_gift_wrapping_design' => $giftWrappingData['noImg']['gift_wrapping_design']));
        //Verification
        $this->addParameter('itemName', $productData['general_name']);
        $this->assertTrue($this->controlIsPresent('pageelement', $entity . '_gift_wrapping_price'),
            'There is no price for "Gift Wrapping Design"');
        $this->assertFalse($this->controlIsPresent('pageelement', $entity . '_gift_wrapping_image'),
            'Picture for Gift wrapping is displayed');
    }

    public function giftWrappingImageDisplayingGiftOptionsDataProvider()
    {
        return array(
            array('per_item'),
            array('for_order')
        );
    }

    /**
     * @TestlinkId
     * <p>TL-MAGE-924: Printed Card is allowed </p>
     * <p>Verify that when setting "Allow Printed Card" is set to "Yes",customer in process of OnePageCheckout
     * have ability to add Printed Card to Order after checking the appropriate checkbox</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Printed Card" is set to "Yes"</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend</p>
     * <p>2. Add any product to a Cart and proceed to checkout</p>
     * <p>3. Select shipping/billing addresses and shipping method</p>
     * <p>4. Check the "Add gift options" checkbox, then check the "Add Gift Options for Entire Order" checkbox</p>
     * <p>Expected result:</p>
     * <p>"Add Printed Card" checkbox should appears</p>
     * <p>5. Check the "Add Printed card" checkbox</p>
     * <p>Expected result:</p>
     * <p>Printed card price must appears (for example: Price: $5.00)</p>
     * <p>6.Click on "Continue" button</p>
     * <p>7. On Payment Method step select any payment method (for example, "Check/Money order"), click on "Continue" button</p>
     * <p>8. On Order Review step click on "Place Order" button</p>
     * <p>9. Open newly created Order page in backend</p>
     * <p>Expected result:</p>
     * <p>Gift Options block of Order page should contain information about Printed card, with the same price , as specified after Step 5<p>
     *
     * @param $productData
     * @param $userData
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @test
     */
    public function printedCardIsAllowed($productData,$userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $printedCardOptions = $this->loadData('gift_printed_card_enable');
        $expectedPrintedCardPrice = $printedCardOptions['tab_1']['configuration']['default_price_for_printed_card'];
        $this->systemConfigurationHelper()->configure($printedCardOptions);
        //Data
        $checkoutData = $this->loadData('gift_data_general', array (
                                                             'products_to_add/product_1' => $productData,
                                                             'shipping_data/add_gift_options' => $this->loadData('gift_message_gift_printed_card')));
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->addParameter('order_id','#' . $orderId);
        $this->searchAndOpen(array('filter_order_id' => $orderId));
        $priceXpath = $this->_getControlXpath('pageelement', 'printed_card_price');
        $printedCardPrice = trim($this->getElementByXpath($priceXpath),'$\t\n\r');
        if ($printedCardPrice != $expectedPrintedCardPrice) {
            $this->addVerificationMessage("Printed Card price is different. Actual: $printedCardPrice." .
                                          ' Expected: ' . $expectedPrintedCardPrice);
        }
    }

    /**
     * @TestlinkId
     * <p>TL-MAGE-998: Printed Card ia not allowed</p>
     * <p>Verify that when setting "Allow Printed Card" is set to "No",customer in process of OnePageCheckout not able
     * to add Printed Card to Order</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Printed Card" is set to "No"</p>
     * <p>2. In system configuration setting "Allow Gift Receipt" is set to "Yes".</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend</p>
     * <p>2. Add any product to a Cart and proceed to checkout</p>
     * <p>3. Select shipping/billing addresses and shipping method</p>
     * <p>4. Check the "Add gift options" checkbox</p>
     * <p>Expected result:</p>
     * <p>"Add Printed card" checkbox should not appears<p>
     *
     * @param $productData
     * @param $userData
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @test
     */
    public function printedCardIsNotAllowed($productData,$userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_printed_card_disable');
        $this->systemConfigurationHelper()->configure('gift_receipt_enable');
        //Data
        $checkoutData = $this->loadData('gift_data_general', array ('products_to_add/product_1' => $productData));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->fillForm(array('add_gift_options' => 'Yes'));
        //Verification
        if ($this->controlIsPresent('checkbox','add_printed_card')) {
            $xpath = $this->_getControlXpath('checkbox','add_printed_card');
            $this->assertFalse($this->isVisible($xpath),'"Add Printed card" checkbox is visible');
        }
    }

    /**
     * @TestlinkId
     * <p>TL-MAGE-996: Gift Receipt is allowed</p>
     * <p>Verify that when setting "Allow Gift Receipt" is set to "Yes", customer in process of OnePageCheckout have
     * ability to enable this option to each order.</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Gift Receipt" is set to "Yes".</p>
     * <p>2. At least one product is created.</p>
     * <p>3. At least one Customer is created.</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend</p>
     * <p>2. Add any product to shopping cart.</p>
     * <p>3. Click button Proceed to checkout.</p>
     * <p>4. Select shipping/billing addresses and shipping method. </p>
     * <p>5. Check the "Add gift options" checkbox. </p>
     * <p>6. Check the "Send Gift Receipt" checkbox.</p>
     * <p>7. Click button Continue.</p>
     * <p>8. Select Payment method (Check/Money order).</p>
     * <p>9. Click button Continue.</p>
     * <p>10. On Order Review step click on "Place Order" button.</p>
     * <p>11. Open newly created Order page in backend.</p>
     * <p>Expected result:</p>
     * <p>Gift Options block of Order page should contain checked checkbox for Gift Receipt<p>
     *
     * @param $productData
     * @param $userData
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @test
     */
    public function giftReceiptIsAllowed($productData,$userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_receipt_enable');
        //Data
        $checkoutData = $this->loadData('gift_data_general', array (
                                                             'products_to_add/product_1' => $productData,
                                                             'shipping_data/add_gift_options' => $this->loadData('gift_message_gift_receipt')));
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->addParameter('order_id','#' . $orderId);
        $this->searchAndOpen(array('filter_order_id' => $orderId));
        $this->orderHelper()->verifyGiftMessage(array('entire_order' => array('order_send_gift_receipt' => 'Yes')));
    }


    /**
     * @TestlinkId
     * <p>TL-MAGE-997: Gift Receipt is not allowed</p>
     * <p>Verify that when setting "Allow Gift Receipt" is set to "No", customer in process of OnePageCheckout have not
     * able to enable this option to each order.</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Receipt" is set to "No".</p>
     * <p>2. In system configuration setting "Allow Printed Card" is set to "Yes".</p>
     * <p>3. At least one product is created.</p>
     * <p>4. At least one Customer is created.</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend</p>
     * <p>2. Add any product to shopping cart.</p>
     * <p>3. Click button Proceed to checkout.</p>
     * <p>4. Select shipping/billing addresses and shipping method. </p>
     * <p>5. Check the "Add gift options" checkbox.</p>
     * <p>Expected result:</p>
     * <p>"Send Gift Receipt" checkbox should not be displayed under each "Add gift options" checkbox.<p>
     *
     * @param $productData
     * @param $userData
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @test
     */
    public function giftReceiptIsNotAllowed($productData,$userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_printed_card_enable');
        $this->systemConfigurationHelper()->configure('gift_receipt_disable');
        //Data
        $checkoutData = $this->loadData('gift_data_general', array ('products_to_add/product_1' => $productData));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->fillForm(array('add_gift_options' => 'Yes'));
        //Verification
        if ($this->controlIsPresent('checkbox','send_gift_receipt')) {
            $xpath = $this->_getControlXpath('checkbox','send_gift_receipt');
            $this->assertFalse($this->isVisible($xpath),'"Send Gift Receipt" checkbox is visible');
        }
    }

    /**
     * @TestlinkId
     * @TestlinkId
     * <p>TL-MAGE-912: Recounting Gift Options (Entire Order)</p>
     * <p>TL-MAGE-918: Recounting Gift Options (Individual Item)</p>
     * <p>Need to verify, that customer in process of OnePageCheckout can change configuration of Gift Options more than
     * one time, and Gift Options prices/Grand Total on Order Review step will be recounted accordingly to these changes.</p>
     * <p>Preconditions:</p>
     * <p>1. At least two Gift Wrapping is created with different prices.</p>
     * <p>2. At least one product is created.</p>
     * <p>3. At least one Customer is created.</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend</p>
     * <p>2. Add any product to shopping cart.</p>
     * <p>3. Click button Proceed to checkout.</p>
     * <p>4. Select shipping/billing addresses and shipping method. </p>
     * <p>5. Mark  the "Add gift options" checkbox.</p>
     * <p>6. Mark Add gift options for the Entire Order checkbox.</p>
     * <p>7. Select any Gift Wrapping on the Gift Options for the Entire Order block.</p>
     * <p>8. Click button Continue.</p>
     * <p>9. Select Payment method (Check/Money order).</p>
     * <p>10. Click button Continue.</p>
     * <p>Expected result:</p>
     * <p>Price for Gift Wrapping is added to Grand Total, and appears in a Order Review step, total amounts is correctly recounted<p>
     * <p>11. Turn back to Shipping method step and changes selection of Gift Wrapping Design to another
     * Gift Wrapping (with different from first price).</p>
     * <p>12. Click button Continue.</p>
     * <p>13. Select Payment method (Check/Money order).</p>
     * <p>14. Click button Continue.</p>
     * <p>Expected result:</p>
     * <p>Price for old Gift Wrapping replaced to new Gift Wrapping price, added to Grand Total, and appears in a Order Review step, total amounts is correctly recounted.<p>
     * <p>15. Turn back to Shipping method step and changes selection of Gift Wrapping Design to none.</p>
     * <p>16. Click button Continue.</p>
     * <p>17. Select Payment method (Check/Money order).</p>
     * <p>18. Click button Continue.</p>
     * <p>Expected result:</p>
     * <p>Grand Total is recounted, Gift Wrapping Price disappears from a Order Review step, Total amounts is correctly recounted.<p>
     *
     * @param $entity
     * @param $productData
     * @param $userData
     * @param $giftWrappingData
     * @dataProvider recountingGiftOptionsDataProvider
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @depends preconditionsGiftWrapping
     * @test
     */
    public function recountingGiftOptions($entity, $productData,$userData,$giftWrappingData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_wrapping_for_order_enable');
        //Data
        $productName = ($entity == 'items') ? $productData['general_name'] . ' Gift Wrapping Design : ' .
                                              $giftWrappingData['noImg']['gift_wrapping_design'] : $productData['general_name'];
        if ($entity=='items') {
            $giftWrapping['individual_items'][$productData['general_name']]['item_gift_wrapping_design'] =
                $giftWrappingData['noImg']['gift_wrapping_design'];
        } else {
            $giftWrapping['entire_order']['order_gift_wrapping_design'] =
                $giftWrappingData['noImg']['gift_wrapping_design'];
        }
        $checkoutData = $this->loadData('recount_gift_wrapping_no_img_one_page', array (
                                                                                 'products_to_add/product_1' => $productData,
                                                                                 'product_name' => $productName,
                                                                                 'gift_wrapping_for_' . $entity => '$12.99'));
        $checkoutData['shipping_data']['add_gift_options'] = $giftWrapping;
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->checkoutOnePageHelper()->frontOrderReview($checkoutData);
        $this->clickControl('link','shipping_method_change_link',false);
        //Data
        $productName = ($entity == 'items') ? $productData['general_name'] . ' Gift Wrapping Design : ' .
                                              $giftWrappingData['img']['gift_wrapping_design'] : $productData['general_name'];
        if ($entity=='items') {
            $giftWrapping['individual_items'][$productData['general_name']]['item_gift_wrapping_design'] =
                $giftWrappingData['img']['gift_wrapping_design'];
        } else {
            $giftWrapping['entire_order']['order_gift_wrapping_design'] =
                $giftWrappingData['img']['gift_wrapping_design'];
        }
        $checkoutData = $this->loadData('recount_gift_wrapping_with_img_one_page', array (
                                                                                   'products_to_add/product_1' => $productData,
                                                                                   'product_name' => $productName,
                                                                                   'gift_wrapping_for_' . $entity => '$18.33'));
        $checkoutData['shipping_data']['add_gift_options'] = $giftWrapping;
        //Steps
        $this->checkoutOnePageHelper()->frontSelectShippingMethod($checkoutData['shipping_data']);
        $this->checkoutOnePageHelper()->frontSelectPaymentMethod($checkoutData['payment_data']);
        $this->checkoutOnePageHelper()->frontOrderReview($checkoutData);
        $this->clickControl('link','shipping_method_change_link',false);
        $this->validatePage();
        //Data
        if ($entity=='items') {
            $giftWrapping['individual_items'][$productData['general_name']]['item_gift_wrapping_design'] =
                'Please select';
        } else {
            $giftWrapping['entire_order']['order_gift_wrapping_design'] = 'Please select';
        }
        $checkoutData = $this->loadData('recount_no_gift_wrapping_one_page', array (
                                                                             'products_to_add/product_1' => $productData,
                                                                             'product_name' => $productData['general_name']));
        $checkoutData['shipping_data']['add_gift_options'] = $giftWrapping;
        //Steps
        $this->checkoutOnePageHelper()->frontSelectShippingMethod($checkoutData['shipping_data']);
        $this->checkoutOnePageHelper()->frontSelectPaymentMethod($checkoutData['payment_data']);
        $this->checkoutOnePageHelper()->frontOrderReview($checkoutData);
    }


    public function recountingGiftOptionsDataProvider()
    {
        return array(
            array('items'),
            array('order')
        );
    }

    /**
     * @TestlinkId
     * <p>TL-MAGE-920: Recounting Gift Options (Printed Card to Order)</p>
     * <p>Need to verify, that customer in process of OnePageCheckout can change configuration of Gift Options
     * (Printed Card) more than one time, and Printed Card prices/Grand Total on Order Review step will be recounted
     * accordingly to these changes.</p>
     * <p>Preconditions:</p>
     * <p>1. Allow Printed Card > Yes (System > Configuration > Sales > Gift Options).</p>
     * <p>2. At least one product is created.</p>
     * <p>3. At least one Customer is created.</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend</p>
     * <p>2. Add any product to shopping cart.</p>
     * <p>3. Click button Proceed to checkout.</p>
     * <p>4. Select shipping/billing addresses and shipping method. </p>
     * <p>5. Mark  the "Add gift options" checkbox.</p>
     * <p>6. Mark Add Printed Card checkbox. </p>
     * <p>7. Click button Continue.</p>
     * <p>8. Select Payment method (Check/Money order).</p>
     * <p>9. Click button Continue.</p>
     * <p>Expected result:</p>
     * <p>Grand Total is recounted, Printed Card price disappears from a Order Review step,
     * Total amounts is correctly recounted.<p>
     * <p>10. Turn back to Shipping method step and deselects Add Printed Card checkbox.</p>
     * <p>11. Click button Continue.</p>
     * <p>12. Select Payment method (Check/Money order).</p>
     * <p>13. Click button Continue.</p>
     * <p>Expected result:</p>
     * <p>Grand Total is recounted, Printed Card price disappears from a Order Review step,
     * Total amounts is correctly recounted.<p>
     *
     * @param $productData
     * @param $userData
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @test
     */
    public function recountingGiftOptionsPrintedCard($productData,$userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $printedCardData = $this->loadData('gift_printed_card_enable');
        $this->systemConfigurationHelper()->configure($printedCardData);
        //Data
        $checkoutData = $this->loadData('recount_gift_wrapping_printed_card_yes_one_page',
            array ('products_to_add/product_1' => $productData,
                   'product_name' => $productData['general_name']));
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->checkoutOnePageHelper()->frontOrderReview($checkoutData);
        $this->clickControl('link','shipping_method_change_link',false);
        //Data
        $checkoutData = $this->loadData('recount_gift_wrapping_printed_card_no_one_page',
            array ('products_to_add/product_1' => $productData,
                   'product_name' => $productData['general_name']));
        //Steps
        $this->checkoutOnePageHelper()->frontSelectShippingMethod($checkoutData['shipping_data']);
        $this->checkoutOnePageHelper()->frontSelectPaymentMethod($checkoutData['payment_data']);
        $this->checkoutOnePageHelper()->frontOrderReview($checkoutData);
    }

    /**
     * @TestlinkId
     * @TestlinkId
     * <p>TL-MAGE-1037: No Gift Wrappings is created</p>
     * <p>TL-MAGE-847: Disabling Gift Wrapping (OnePageCheckout)</p>
     * <p>Verify that when setting "Allow Gift Wrapping on Order Level" is set to "Yes" and setting
     * "Allow gift wrapping for Order Item" is set to "Yes", dropdown "Gift Wrapping Design" on Payment Method step of
     * checkout should be absent for Entire Order and Individual Item</p>
     * <p>Preconditions:</p>
     * <p>1. In system configuration setting "Allow Gift Wrapping on Order Level" is set to "Yes";</p>
     * <p>2. In system configuration setting "Allow Gift  Wrapping on Order Items" is set to "Yes"</p>
     * <p>3. Gift Wrapping is not created;</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend</p>
     * <p>2. Add any product to a Cart and proceed to checkout</p>
     * <p>3. Select any address in billing information and press button "Continue"</p>
     * <p>4. Select any address in shipping information and press button "Continue"</p>
     * <p>5. Check the "Add gift options" checkbox</p>
     * <p>6. Check "Add gift options for the Entire Order" checkbox</p>
     * <p>Expected result:</p>
     * <p>"Gift Wrapping Design" dropdown should not be visible<p>
     * <p>7. Check "Add gift Options for Order Item" checkbox and observe drop-down list;</p>
     * <p>Expected result:</p>
     * <p>"Gift Wrapping Design" dropdown should not be visible<p>
     *
     * @param $productData
     * @param $userData
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @test
     */
    public function noGiftWrappingsIsCreated($productData,$userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_wrapping_for_order_enable');
        $this->systemConfigurationHelper()->configure('gift_wrapping_per_item_enable');
        //Disabling gift wrapping for TL-MAGE-847
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->disableAllGiftWrapping();
        //Data
        $checkoutData = $this->loadData('gift_data_general', array('products_to_add/product_1' => $productData));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->fillForm(array('add_gift_options' => 'Yes', 'gift_option_for_order' => 'Yes'));
        //Verification
        $this->assertFalse($this->controlIsPresent('dropdown', 'order_gift_wrapping_design'),
            '"Gift Wrapping Design" is in place');
        //Steps
        $this->fillForm(array('gift_option_for_item' => 'Yes'));
        $this->addParameter('itemName', $productData['general_name']);
        //Verification
        $this->assertFalse($this->controlIsPresent('dropdown', 'item_gift_wrapping_design'),
            '"Gift Wrapping Design" is in place');
    }

    /**
     * @TestlinkId
     * <p>Test Case TL-MAGE-857: Possibility to adding Gift attributes to Order during the process of OnePageCheckout - Website</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. Website is created;</p>
     * <p>4. Gift wrapping is created for new website;</p>
     * <p>5. Gift wrapping and gift messages are allowed for entire order and individual items in system configuration in website scope;</p>
     * <p>6. Navigate to newly created website URL.</p>
     * <p>Steps:</p>
     * <p>1. Log into Frontend;</p>
     * <p>2. Add one product to shopping cart;</p>
     * <p>3. Click on "Checkout" button;</p>
     * <p>4. Select any address in billing information and press button "Continue"</p>
     * <p>5. Select any address in shipping information and press button "Continue"</p>
     * <p>6. Check the "Add gift options" checkbox</p>
     * <p>7. Check "Add gift options for the Entire Order" checkbox</p>
     * <p>8. Proceed to billing information page;</p>
     * <p>9. Select payment method "Check/Money Order";</p>
     * <p>10. Proceed to review order information;</p>
     * <p>11. Check presence of gift wrapping for item and entire order in totals;</p>
     * <p>12. Submit order;</p>
     * <p>Expected Results:</p>
     * <p>1. Gift wrapping is mentioned in totals and its price is correct;</p>
     * <p>2. Order is created;</p>
     *
     * @depends preconditionsCreateCustomerForWebsite
     * @depends preconditionsCreateProductForWebsite
     * @depends createWebsite
     * @param array $customerData
     * @param array $productData
     * @param array $website
     *
     * @test
     */
    public function checkoutWithGiftWrappingAndMessageWebsiteScope($customerData, $productData, $website)
    {
        //Preconditions
        $giftWrappingEnableWebsite = $this->loadData('gift_wrapping_all_enable_on_website',
            array('configuration_scope' => $website['general_information']['staging_website_name']));
        $giftMessagesEnableWebsite = $this->loadData('gift_message_all_enable_on_website',
            array('configuration_scope' => $website['general_information']['staging_website_name']));
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_message_all_disable');
        $this->systemConfigurationHelper()->configure('gift_wrapping_all_disable');
        $this->systemConfigurationHelper()->configure($giftWrappingEnableWebsite);
        $this->systemConfigurationHelper()->configure($giftMessagesEnableWebsite);

        $giftWrappingData = $this->loadData('gift_wrapping_without_image',
            array('gift_wrapping_websites' => $website['general_information']['staging_website_name']));
        $individualItemsMessage = $this->loadData('gift_message_for_individual_items_one_page_website');
        $indItems = array($productData['general_name'] =>
                          array('item_gift_wrapping_design'  => $giftWrappingData['gift_wrapping_design'],
                                'gift_message'               => $individualItemsMessage));

        $checkoutData = $this->loadData('gift_data_general_for_website_scope', array (
                                                                               'products_to_add/product_1'     => $productData,
                                                                               'product_name'                  => $productData['general_name'],
                                                                               'individual_items'              => $indItems));
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $newFrontendUrl = $this->stagingWebsiteHelper()->buildFrontendUrl(
            $website['general_information']['staging_website_code']);
        $this->_configHelper->setAreaBaseUrl('frontend', $newFrontendUrl);
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * @TestlinkId
     * <p>Test Case TL-MAGE-868: Possibility to adding Gift attributes to Order during the process of OnePageCheckout</p>
     * <p>Check If possible to add Gift Attributes to Order during the process of
     * OnePageCheckout (at Default Website) when all  "gift settings" in default scope is set to 'yes" and in the website
     * scope all of this settings set to "no"</p>
     * <p>Preconditions:</p>
     * <p>1. System -> Sales -> Gift Options (Default scope) -> Switch to "yes" all options;</p>
     * <p>2. System -> Sales -> Gift Options (Website scope) -> Switch to "no" all options;</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend of Default website</p>
     * <p>2. Browse site and buy at least 2 products</p>
     * <p>3. Push "Proceed to checkout" in the shoping cart</p>
     * <p>4. Select any address in billing information and press button "Continue"</p>
     * <p>5. Select any address in shipping information and press button "Continue"</p>
     * <p>6. Select any shipping method in shipping method tab</p>
     * <p>7. Check "Add gift options" checkbox</p>
     * <p>8. Check "Add gift options for the Entire Order" checkbox</p>
     * <p>9. Choose gift wrappin design ang fill gift message fields which appers when we press "gift message" link</p>
     * <p>10. Check "Add gift options for Individual Items" checkbox</p>
     * <p>11. Choose gift wrappin design ang fill gift message fields which appers when we press "gift message" link</p>
     * <p>12. Check "Send Gift Receipt" checkbox</p>
     * <p>13. Check "Add Printed Card" checkbox </p>
     * <p>14. Press button "Continue"</p>
     * <p>15. Select any payment method in payment information and press button "Continue"</p>
     * <p>16. Push button "Place order" in "order information" tab</p>
     * <p>17. Log into backend and go to Sales->Order</p>
     * <p>18. Choose recently created order and check if all switched in this test case gift options is saved</p>
     * <p>Expected Results:</p>
     * <p>All switched in this test case gift options is saved;</p>
     *
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProduct
     * @depends createWebsite
     * @param array $customerData
     * @param array $productData
     * @param array $website
     *
     * @test
     */
    public function possibilityToAddGiftAttributesToOrder($customerData, $productData, $website)
    {
        //Preconditions
        $giftWrappingEnableWebsite = $this->loadData('gift_wrapping_all_disable_on_website',
            array('configuration_scope' => $website['general_information']['staging_website_name']));
        $giftMessagesEnableWebsite = $this->loadData('gift_message_all_disable_on_website',
            array('configuration_scope' => $website['general_information']['staging_website_name']));
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_message_all_enable');
        $this->systemConfigurationHelper()->configure('gift_wrapping_all_enable');
        $this->systemConfigurationHelper()->configure($giftWrappingEnableWebsite);
        $this->systemConfigurationHelper()->configure($giftMessagesEnableWebsite);
        //Data
        $giftWrappingData = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $individualItemsMessage = $this->loadData('gift_message_for_individual_items_one_page');
        $indItems = array($productData['general_name'] => array(
            'gift_message'                   => $individualItemsMessage,
            'item_gift_wrapping_design'      => $giftWrappingData['gift_wrapping_design']));
        $entireOrder = $this->loadData('gift_message_with_gift_wrapping_one_page');
        $checkoutData = $this->loadData('gift_data_general', array (
                                                             'products_to_add/product_1'      => $productData,
                                                             'entire_order'                   => $entireOrder['entire_order'],
                                                             'order_gift_wrapping_design'     => $giftWrappingData['gift_wrapping_design'],
                                                             'individual_items'                    => $indItems    ));
        $vrfGiftData = $this->loadData('verify_gift_data',array(
                                                          'sku_product'   => $productData['general_sku'],
                                                          'product_gift_message_from'      => $individualItemsMessage['item_gift_message_from'],
                                                          'product_gift_message_to'        => $individualItemsMessage['item_gift_message_to'],
                                                          'product_gift_message'           => $individualItemsMessage['item_gift_message'],
                                                          'product_gift_wrapping_price'    => $giftWrappingData['gift_wrapping_price'],
                                                          'order_gift_message_from'        => $entireOrder['entire_order']['gift_message']['order_gift_message_from'],
                                                          'order_gift_message_to'          => $entireOrder['entire_order']['gift_message']['order_gift_message_to'],
                                                          'order_gift_message'             => $entireOrder['entire_order']['gift_message']['order_gift_message'],
                                                          'order_gift_wrapping_price'      => $giftWrappingData['gift_wrapping_price']
                                                          ));
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->customerHelper()->frontLoginCustomer($customerData);
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
     * @TestlinkId
     * <p>Test Case TL-MAGE-869: Possibility to adding Gift attributes to Order during the process of OnePageCheckout - Global</p>
     * <p>Check If possible to add Gift Attributes to Order during the process of
     * OnePageCheckout (at Default Website) when all  "gift settings" in default scope is set to 'yes" and in the website
     * scope all of this settings set to "no"</p>
     * <p>Preconditions:</p>
     * <p>1. System -> Sales -> Gift Options (Default scope) -> Switch to "yes" all options;</p>
     * <p>2. System -> Sales -> Gift Options (Website scope) -> Switch to "no" all options;</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend to newly created website</p>
     * <p>2. Browse site and buy at least 2 products</p>
     * <p>3. Push "Proceed to checkout" in the shopping cart</p>
     * <p>4. Select any address in billing information and press button "Continue"</p>
     * <p>5. Select any address in shipping information and press button "Continue"</p>
     * <p>6. Select any shipping method in shipping method tab</p>
     * <p>7. Check "Add gift options" checkbox</p>
     * <p>Expected Results:</p>
     * <p>All gift options should not be visible;</p>
     *
     * @depends preconditionsCreateCustomerForWebsite
     * @depends preconditionsCreateProductForWebsite
     * @depends createWebsite
     * @param array $customerData
     * @param array $productData
     * @param array $website
     *
     * @test
     */
    public function restrictionToAddGiftAttributesToOrder($customerData, $productData, $website)
    {
        //Preconditions
        $giftWrappingDisableWebsite = $this->loadData('gift_wrapping_all_disable_on_website',
            array('configuration_scope' => $website['general_information']['staging_website_name']));
        $giftMessagesDisableWebsite = $this->loadData('gift_message_all_disable_on_website',
            array('configuration_scope' => $website['general_information']['staging_website_name']));
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_message_all_enable');
        $this->systemConfigurationHelper()->configure('gift_wrapping_all_enable');
        $this->systemConfigurationHelper()->configure($giftWrappingDisableWebsite);
        $this->systemConfigurationHelper()->configure($giftMessagesDisableWebsite);
        //Data
        $giftWrappingData = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $checkoutData = $this->loadData('gift_data_general', array (
                                                             'products_to_add/product_1'      => $productData));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $newFrontendUrl = $this->stagingWebsiteHelper()->buildFrontendUrl(
            $website['general_information']['staging_website_code']);
        $this->_configHelper->setAreaBaseUrl('frontend', $newFrontendUrl);
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        //Verification
        if ($this->controlIsPresent('checkbox', 'add_gift_options')) {
            $xpath = $this->_getControlXpath('checkbox', 'add_gift_options');
            $this->assertFalse($this->isVisible($xpath), '"Add gift options" checkbox is visible');
        }
    }
}
