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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test Api2 server model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_ServerTest extends Magento_TestCase
{
    /**
     * Product model data fixture
     *
     * @var Mage_Catalog_Model_Product
     */
    protected static $_product;

    /**
     * Customer model data fixture
     *
     * @var Mage_Customer_Model_Customer
     */
    protected static $_customer;

    /**
     * Consumer model data fixture
     *
     * @var Mage_Oauth_Model_Consumer
     */
    protected static $_consumer;

    /**
     * Token model data fixture
     *
     * @var Mage_Oauth_Model_Token
     */
    protected static $_token;

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->callModelDelete(self::$_product, true);
        $this->callModelDelete(self::$_customer, true);
        $this->callModelDelete(self::$_consumer, true);
        $this->callModelDelete(self::$_token, true);

        parent::tearDown();
    }

    /**
     * Set product data fixture
     *
     * @return void
     */
    public static function productDataFixture()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product');
        $product->setData(array(
            'sku'               => 'test_product' . uniqid(),
            'name'              => 'test Product',
            'attribute_set_id'  => 1,
            'website_ids'       => array(Mage::app()->getStore()->getWebsiteId()),
            'description'       => '...',
            'short_description' => '...',
            'price'             => 0,
            'tax_class_id'      => 2,
            'visibility'        => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            'status'            => Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
        ))->save();

        self::$_product = $product;
    }

    /**
     * Set customer data fixture
     *
     * @return void
     */
    public static function customerDataFixture()
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer');
        $customer->setData(array(
            'firstname'    => 'TestFirst',
            'lastname'     => 'TestLast',
            'password'     => '123123q',
            'confirmation' => '123123q',
            'username'     => 'myusername',
            'email'        => 'my' . uniqid() . '@example.com'
        ))->save();

        self::$_customer = $customer;
    }

    /**
     * Set consumer data fixture
     *
     * @return void
     */
    public static function consumerDataFixture()
    {
        /** @var $consumer Mage_Oauth_Model_Consumer */
        $consumer = Mage::getModel('oauth/consumer');
        /** @var $helper Mage_Oauth_Helper_Data */
        $helper   = Mage::helper('oauth/data');

        $consumer->setData(array(
            'name'   => 'Consumer Name Server Test ' . uniqid(),
            'key'    => $helper->generateConsumerKey(),
            'secret' => $helper->generateConsumerSecret()
        ))->save();

        self::$_consumer = $consumer;
    }

    /**
     * Set token data fixture
     *
     * @return void
     */
    public static function tokenDataFixture()
    {
        /** @var $helper Mage_Oauth_Helper_Data */
        $helper = Mage::helper('oauth');

        /** @var $consumer Mage_Oauth_Model_Token */
        $token = Mage::getModel('oauth/token');
        $token->setData(array(
            'consumer_id'       => self::$_consumer->getId(),
            'admin_id'          => null,
            'customer_id'       => self::$_customer->getId(),
            'type'              => Mage_Oauth_Model_Token::TYPE_ACCESS,
            'token'             => $helper->generateToken(),
            'secret'            => $helper->generateTokenSecret(),
            'verifier'          => $helper->generateVerifier(),
            'callback_url'      => Mage_Oauth_Model_Server::CALLBACK_ESTABLISHED,
            'authorized'        => 1,
            'revoked'           => 0,
        ))->save();

        self::$_token = $token;
    }

    /**
     * Test fixtures creation
     *
     * @magentoDataFixture productDataFixture
     * @magentoDataFixture customerDataFixture
     * @magentoDataFixture consumerDataFixture
     * @magentoDataFixture tokenDataFixture
     * @return void
     */
    public function testCreateFixtures()
    {
    }
}
