<?php
/**
 * Checkout tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Checkout_CartTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Eav_Model_Entity_Store */
    protected static $_entityStore;

    /** @var array */
    protected static $_origData = array();

    /**
     * Product for test
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Quote object
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote;

    /**
     * Sales Rule object
     *
     * @var Mage_SalesRule_Model_Rule
     */
    protected $_salesRule;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    protected function setUp()
    {
        // create product for test
        $this->_product = Mage::getModel('Mage_Catalog_Model_Product');

        $this->_product->setData(
            array(
                'sku' => 'simple' . uniqid(),
                'attribute_set_id' => 4,
                'type_id' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                'name' => 'Simple Product',
                'website_ids' => array(Mage::app()->getStore()->getWebsiteId()),
                'description' => '...',
                'short_description' => '...',
                'price' => 0.99,
                'tax_class_id' => 2,
                'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                'status' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED
            )
        );
        $this->_product->save();
        $this->_product->load($this->_product->getId());
        $this->_product->setStockData(
            array(
                'manage_stock' => 1,
                'qty' => 10,
                'backorders' => 1,
                'is_in_stock' => '1',
            )
        );
        $this->_product->save();

        // Disable exceptions to avoid errors in cookie processing
        Mage::$headersSentThrowsException = false;

        // create quote for test
        $this->_quote = Mage::getModel('Mage_Sales_Model_Quote');

        $this->_quote->setData(
            array(
                'store_id' => 1,
                'is_active' => 0,
                'is_multi_shipping' => 0
            )
        );
        $this->_quote->save();

        parent::setUp();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        Mage::register('isSecureArea', true);

        $this->_product->delete();
        $this->_quote->delete();
        if ($this->_salesRule instanceof Mage_SalesRule_Model_Rule) {
            $this->_salesRule->delete();
        }

        Mage::unregister('isSecureArea');

        $this->_restoreIncrementIdPrefix();
        PHPUnit_Framework_TestCase::deleteFixture('customer', true);
        parent::tearDown();
    }

    /**
     * Test for product add to shopping cart
     */
    public function testProductAddToCart()
    {
        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartProductAdd',
            array(
                'quoteId' => $this->_quote->getId(),
                'productsData' => array(
                    array('sku' => $this->_product->getSku(), 'qty' => 1)
                )
            )
        );

        $this->assertTrue($soapResult, 'Error during product add to cart via API call');
    }

    /**
     * Test for product with custom options add to shopping cart
     */
    public function testProductWithCustomOptionsAddToCart()
    {
        // Create custom option for product
        $customOptionId = null;
        $customOptionTitle = 'My Text ' . uniqid();
        $customOptionValue = 'Hello WORLD';

        $this->_product->setCanSaveCustomOptions(true);
        $this->_product->setProductOptions(
            array(
                array('type' => 'field', 'title' => $customOptionTitle, 'is_require' => 0, 'max_characters' => 0)
            )
        );
        $this->_product->save();

        // Find ID of created custom option for future use
        /** @var $productOption Mage_Catalog_Model_Product_Option */
        $productOption = Mage::getModel('Mage_Catalog_Model_Product_Option');

        foreach ($productOption->getProductOptionCollection($this->_product) as $option) {
            if ($option['default_title'] == $customOptionTitle) {
                $customOptionId = $option['option_id'];
                break;
            }
        }
        if (null === $customOptionId) {
            $this->fail('Can not find custom option ID that been created');
        }

        // Add product with custom option to cart via API
        if (TESTS_WEBSERVICE_TYPE == PHPUnit_Framework_TestCase::TYPE_SOAP
            || TESTS_WEBSERVICE_TYPE == PHPUnit_Framework_TestCase::TYPE_SOAP_WSI
        ) { // use associative array for SOAP v2
            $customOptionsData = array(array('key' => $customOptionId, 'value' => $customOptionValue));
        } else { // use numeric array otherwise
            $customOptionsData = array($customOptionId => $customOptionValue);
        }
        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartProductAdd',
            array(
                'quoteId' => $this->_quote->getId(),
                'productsData' => array(
                    array('sku' => $this->_product->getSku(), 'qty' => 1, 'options' => $customOptionsData)
                )
            )
        );

        $this->assertTrue($soapResult, 'Error during product with custom options add to cart via API call');

        /** @var $quoteItemOption Mage_Sales_Model_Resource_Quote_Item_Option_Collection */
        $quoteItemOption = Mage::getResourceModel('Mage_Sales_Model_Resource_Quote_Item_Option_Collection');
        $itemOptionValue = null;

        foreach ($quoteItemOption->getOptionsByProduct($this->_product) as $row) {
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
     * Test for product list from shopping cart API method
     */
    public function testCartProductList()
    {
        // have to re-load product for stock item set
        $this->_product->load($this->_product->getId());

        // add product as a quote item
        $this->_quote->addProduct($this->_product);
        $this->_quote->collectTotals()->save();

        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartProductList',
            array('quoteId' => $this->_quote->getId())
        );

        $this->assertInternalType('array', $soapResult, 'Product List call result is not an array');

        if (0 === key($soapResult)) {
            $this->assertCount(1, $soapResult, 'Product List call result contain not exactly one product');

            $soapResult = $soapResult[0]; //workaround for different result structure
        }
        $this->assertArrayHasKey('sku', $soapResult, 'Product List call result does not contain a product sku');
        $this->assertEquals($this->_product->getSku(), $soapResult['sku'], 'Product Sku does not match fixture');
    }

    /**
     * Test coupon code applying
     */
    public function testCartCouponAdd()
    {
        // create sales rule coupon
        $this->_salesRule = Mage::getModel('Mage_SalesRule_Model_Rule');
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
        );
        $this->_salesRule->loadPost($data)->setUseAutoGeneration(false)->save();

        // have to re-load product for stock item set
        $this->_product->load($this->_product->getId());

        // add product as a quote item
        $this->_quote->addProduct($this->_product);
        $this->_quote->collectTotals()->save();

        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartCouponAdd',
            array(
                'quoteId' => $this->_quote->getId(),
                'couponCode' => $this->_salesRule->getCouponCode()
            )
        );
        $this->assertTrue($soapResult, 'Coupon code was not applied');
        $this->_quote->load($this->_quote->getId());
        $discountedPrice = sprintf('%01.2f', $this->_product->getPrice() * (1 - $discount / 100));
        $this->assertEquals(
            $this->_quote->getSubtotalWithDiscount(),
            $discountedPrice,
            'Quote subtotal price does not match discounted item price'
        );
    }

    /**
     * Test for product list from shopping cart API method
     *
     * @magentoDataFixture Api/Mage/Checkout/_fixture/order.php
     */
    public function testCreateOrder()
    {
        // Set order increment id prefix
        $prefix = '01';
        $this->_setIncrementIdPrefix('order', $prefix);

        $quote = self::getFixture('quote');

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
     * @magentoDataFixture Api/Mage/Checkout/_fixture/order_with_payment_method.php
     */
    public function testCreateOrderWithPayment()
    {
        $quote = self::getFixture('quote');
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
                'paymentData' => $paymentMethod
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
     * @magentoDataFixture Api/Mage/Checkout/_fixture/order_with_payment_method.php
     */
    public function testCreateOrderWithNotAvailablePayment()
    {
        $quote = self::getFixture('quote');
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
                    'paymentData' => $paymentMethod
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
     * @magentoDataFixture Api/Mage/Checkout/_fixture/order_with_payment_method.php
     */
    public function testCreateOrderWithEmptyPaymentData()
    {
        $quote = self::getFixture('quote');
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
     * @magentoDataFixture Api/Mage/Checkout/_fixture/order_with_payment_method.php
     */
    public function testCreateOrderWithInvalidPaymentData()
    {
        $quote = self::getFixture('quote');
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
                    'paymentData' => $paymentMethod
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

    /**
     * Restore increment ID prefix in entity model.
     */
    protected function _restoreIncrementIdPrefix()
    {
        $entityStoreModel = self::$_entityStore;
        if ($entityStoreModel instanceof Mage_Eav_Model_Entity_Store) {
            $entityStoreModel->loadByEntityStore($entityStoreModel->getEntityTypeId(), $entityStoreModel->getStoreId());
            $entityStoreModel->setIncrementPrefix(self::$_origData['increment_prefix'])
                ->setIncrementLastId(++self::$_origData['increment_last_id'])
                ->save();
        }
    }

    /**
     * Set increment id prefix in entity model.
     *
     * @param string $entityType
     * @param string $prefix
     */
    protected function _setIncrementIdPrefix($entityType, $prefix)
    {
        $website = Mage::app()->getWebsite();
        $storeId = $website->getDefaultStore()->getId();
        $entityTypeModel = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode($entityType);
        /** @var Mage_Eav_Model_Entity_Store $entityStore */
        $entityStore = Mage::getModel('Mage_Eav_Model_Entity_Store')->loadByEntityStore(
            $entityTypeModel->getId(),
            $storeId
        );
        $origPrefix = $entityStore->getIncrementPrefix() == null ? $storeId : $entityStore->getIncrementPrefix();
        self::$_origData['increment_prefix'] = $origPrefix;
        self::$_origData['increment_last_id'] = $entityStore->getIncrementLastId();
        $entityStore->setEntityTypeId($entityTypeModel->getId());
        $entityStore->setStoreId($storeId);
        $entityStore->setIncrementPrefix($prefix);
        $entityStore->save();
        self::$_entityStore = $entityStore;
    }
}
