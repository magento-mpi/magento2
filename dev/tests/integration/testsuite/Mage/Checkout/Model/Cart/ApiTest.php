<?php
/**
 * Checkout API tests.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDbIsolation enabled
 */
class Mage_Checkout_Model_Cart_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for product add to shopping cart.
     *
     * @magentoDataFixture Mage/Catalog/_files/product_simple_duplicated.php
     * @magentoDataFixture Mage/Checkout/_files/quote.php
     */
    public function testProductAddToCart()
    {
        /** @var Mage_Sales_Model_Resource_Quote_Collection $quoteCollection */
        $quoteCollection = Mage::getModel('Mage_Sales_Model_Resource_Quote_Collection');
        $quote = $quoteCollection->getFirstItem();
        $productSku = 'simple-1';

        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartProductAdd',
            array(
                'quoteId' => $quote->getId(),
                'productsData' => array(
                    (object)array('sku' => $productSku, 'qty' => 1)
                )
            )
        );

        $this->assertTrue($soapResult, 'Error during product add to cart via API call');
    }

    /**
     * Test for product with custom options add to shopping cart.
     *
     * @magentoDataFixture Mage/Catalog/_files/product_with_options.php
     * @magentoDataFixture Mage/Checkout/_files/quote.php
     */
    public function testProductWithCustomOptionsAddToCart()
    {
        // Create custom option for product
        $customOptionId = null;
        $customOptionTitle = 'test_option_code_1';
        $customOptionValue = 'option_value';
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load(1);
        /** @var Mage_Sales_Model_Resource_Quote_Collection $quoteCollection */
        $quoteCollection = Mage::getModel('Mage_Sales_Model_Resource_Quote_Collection');
        $quote = $quoteCollection->getFirstItem();

        // Find ID of created custom option for future use
        /** @var $productOption Mage_Catalog_Model_Product_Option */
        $productOption = Mage::getModel('Mage_Catalog_Model_Product_Option');

        foreach ($productOption->getProductOptionCollection($product) as $option) {
            if ($option['default_title'] == $customOptionTitle) {
                $customOptionId = $option['option_id'];
                break;
            }
        }
        if (null === $customOptionId) {
            $this->fail('Can not find custom option ID that been created');
        }

        $customOptionsData = array($customOptionId => $customOptionValue);
        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartProductAdd',
            array(
                'quoteId' => $quote->getId(),
                'productsData' => array(
                    (object)array('sku' => $product->getSku(), 'qty' => 1, 'options' => $customOptionsData)
                )
            )
        );

        $this->assertTrue($soapResult, 'Error during product with custom options add to cart via API call');

        /** @var $quoteItemOption Mage_Sales_Model_Resource_Quote_Item_Option_Collection */
        $quoteItemOption = Mage::getResourceModel('Mage_Sales_Model_Resource_Quote_Item_Option_Collection');
        $itemOptionValue = null;

        foreach ($quoteItemOption->getOptionsByProduct($product) as $row) {
            if ('option_' . $customOptionId == $row['code']) {
                $itemOptionValue = $row['value'];
                break;
            }
        }
        if (null === $itemOptionValue) {
            $this->fail('Custom option value not found in DB after API call');
        }
        $this->assertEquals(
            $customOptionValue,
            $itemOptionValue,
            'Custom option value in DB does not match value passed by API'
        );
    }

    /**
     * Test for product list from shopping cart API method.
     *
     * @magentoDataFixture Mage/Checkout/_files/quote_with_simple_product.php
     */
    public function testCartProductList()
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load(1);
        /** @var Mage_Sales_Model_Resource_Quote_Collection $quoteCollection */
        $quoteCollection = Mage::getModel('Mage_Sales_Model_Resource_Quote_Collection');
        $quote = $quoteCollection->getFirstItem();

        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartProductList',
            array('quoteId' => $quote->getId())
        );

        $this->assertInternalType('array', $soapResult, 'Product List call result is not an array');

        if (0 === key($soapResult)) {
            $this->assertCount(1, $soapResult, 'Product List call result contain not exactly one product');

            $soapResult = $soapResult[0]; //workaround for different result structure
        }
        $this->assertArrayHasKey('sku', $soapResult, 'Product List call result does not contain a product sku');
        $this->assertEquals($product->getSku(), $soapResult['sku'], 'Product Sku does not match fixture');
    }

    /**
     * Test coupon code applying.
     *
     * @magentoDataFixture Mage/Checkout/_files/quote_with_simple_product.php
     */
    public function testCartCouponAdd()
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load(1);
        /** @var Mage_Sales_Model_Resource_Quote_Collection $quoteCollection */
        $quoteCollection = Mage::getModel('Mage_Sales_Model_Resource_Quote_Collection');
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $quoteCollection->getFirstItem();

        // create sales rule coupon
        /** @var Mage_SalesRule_Model_Rule $salesRule */
        $salesRule = Mage::getModel('Mage_SalesRule_Model_Rule');
        $discount = 10;
        $data = array(
            'name' => 'Test Coupon',
            'is_active' => true,
            'website_ids' => array(Mage::app()->getStore()->getWebsiteId()),
            'customer_group_ids' => array(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID),
            'coupon_type' => Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC,
            'coupon_code' => uniqid(),
            'simple_action' => Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION,
            'discount_amount' => $discount,
            'discount_step' => 1,
        );
        $salesRule->loadPost($data)->setUseAutoGeneration(false)->save();

        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartCouponAdd',
            array(
                'quoteId' => $quote->getId(),
                'couponCode' => $salesRule->getCouponCode()
            )
        );
        $this->assertTrue($soapResult, 'Coupon code was not applied');
        $quote->load($quote->getId());
        $discountedPrice = sprintf('%01.2f', $product->getPrice() * (1 - $discount / 100));
        $this->assertEquals(
            $quote->getSubtotalWithDiscount(),
            $discountedPrice,
            'Quote subtotal price does not match discounted item price'
        );
    }

    /**
     * Test for product list from shopping cart API method
     *
     * @magentoDataFixture Mage/Checkout/_files/quote_with_check_payment.php
     * @magentoAppIsolation enabled
     */
    public function testCreateOrder()
    {
        // Set order increment id prefix
        $prefix = '01';
        Magento_Test_Helper_Api::setIncrementIdPrefix('order', $prefix);

        $quote = Mage::registry('quote');
        $orderIncrementId = Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartOrder',
            array(
                'quoteId' => $quote->getId()
            )
        );

        $this->assertTrue(is_string($orderIncrementId), 'Increment Id is not a string');
        $this->assertStringStartsWith($prefix, $orderIncrementId, 'Increment Id returned by API is not correct');
    }

    /**
     * Test order creation with payment method
     *
     * @magentoConfigFixture current_store payment/ccsave/active 1
     * @magentoDataFixture Mage/Checkout/_files/quote_with_ccsave_payment.php
     * @magentoAppIsolation enabled
     */
    public function testCreateOrderWithPayment()
    {
        $quote = Mage::registry('quote');
        $paymentMethod = array(
            'method' => 'ccsave',
            'cc_owner' => 'user',
            'cc_type' => 'VI',
            'cc_exp_month' => 5,
            'cc_exp_year' => 2016,
            'cc_number' => '4584728193062819',
            'cc_cid' => '000',
        );

        $orderIncrementId = Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartOrderWithPayment',
            array(
                'quoteId' => $quote->getId(),
                'store' => null,
                'agreements' => null,
                'paymentData' => (object)$paymentMethod
            )
        );

        $this->assertTrue(is_string($orderIncrementId), 'Increment Id is not a string');
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('Mage_Sales_Model_Order')->loadByIncrementId($orderIncrementId);
        $this->assertEquals('ccsave', $order->getPayment()->getMethod());
    }

    /**
     * Test order creation with not available payment method
     *
     * @magentoDataFixture Mage/Checkout/_files/quote_with_ccsave_payment.php
     * @magentoAppIsolation enabled
     */
    public function testCreateOrderWithNotAvailablePayment()
    {
        $quote = Mage::registry('quote');
        $paymentMethod = array(
            'method' => 'paypal_direct',
            'cc_owner' => 'user',
            'cc_type' => 'VI',
            'cc_exp_month' => 5,
            'cc_exp_year' => 2016,
            'cc_number' => '4584728193062819',
            'cc_cid' => '000',
        );

        $errorCode = 1075;
        $errorMessage = 'The requested Payment Method is not available.';
        try {
            Magento_Test_Helper_Api::call(
                $this,
                'shoppingCartOrderWithPayment',
                array(
                    'quoteId' => $quote->getId(),
                    'store' => null,
                    'agreements' => null,
                    'paymentData' => (object)$paymentMethod
                )
            );
            $this->fail('Expected error exception was not raised.');
        } catch (SoapFault $e) {
            $this->_assertError($errorCode, $errorMessage, $e->faultcode, $e->faultstring);
        }
    }

    /**
     * Test order creation with payment method data empty
     *
     * @magentoDataFixture Mage/Checkout/_files/quote_with_ccsave_payment.php
     * @magentoAppIsolation enabled
     */
    public function testCreateOrderWithEmptyPaymentData()
    {
        $quote = Mage::registry('quote');
        $errorCode = 1071;
        $errorMessage = 'Payment method data is empty.';
        try {
            Magento_Test_Helper_Api::call(
                $this,
                'shoppingCartOrderWithPayment',
                array(
                    'quoteId' => $quote->getId(),
                    'store' => null,
                    'agreements' => null,
                    'paymentData' => array()
                )
            );
            $this->fail('Expected error exception was not raised.');
        } catch (SoapFault $e) {
            $this->_assertError($errorCode, $errorMessage, $e->faultcode, $e->faultstring);
        }
    }

    /**
     * Test order creation with invalid payment method data
     *
     * @magentoConfigFixture current_store payment/ccsave/active 1
     * @magentoDataFixture Mage/Checkout/_files/quote_with_ccsave_payment.php
     * @magentoAppIsolation enabled
     */
    public function testCreateOrderWithInvalidPaymentData()
    {
        $quote = Mage::registry('quote');
        $paymentMethod = array(
            'method' => 'ccsave',
            'cc_owner' => 'user',
            'cc_type' => 'VI',
            'cc_exp_month' => 5,
            'cc_exp_year' => 2010,
            'cc_number' => '4584728193062819',
            'cc_cid' => '000',
        );
        $errorCode = 1075;
        $errorMessage = 'Incorrect credit card expiration date.';
        try {
            Magento_Test_Helper_Api::call(
                $this,
                'shoppingCartOrderWithPayment',
                array(
                    'quoteId' => $quote->getId(),
                    'store' => null,
                    'agreements' => null,
                    'paymentData' => (object)$paymentMethod
                )
            );
            $this->fail('Expected error exception was not raised.');
        } catch (SoapFault $e) {
            $this->_assertError($errorCode, $errorMessage, $e->faultcode, $e->faultstring);
        }
    }

    /**
     * Assert that error code and message equals expected
     *
     * @param int $expectedCode
     * @param string $expectedMessage
     * @param int $actualCode
     * @param string $actualMessage
     */
    protected function _assertError($expectedCode, $expectedMessage, $actualCode, $actualMessage)
    {
        $this->assertEquals($expectedCode, $actualCode);
        $this->assertEquals($expectedMessage, $actualMessage);
    }
}
