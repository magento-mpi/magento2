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
     * @static
     * @var Mage_Catalog_Model_Product
     */
    protected static $_product;

    /**
     * Customer model data fixture
     *
     * @static
     * @var Mage_Customer_Model_Customer
     */
    protected static $_customer;

    /**
     * Consumer model data fixture
     *
     * @static
     * @var Mage_OAuth_Model_Consumer
     */
    protected static $_consumer;

    /**
     * Token model data fixture
     *
     * @static
     * @var Mage_OAuth_Model_Token
     */
    protected static $_token;

    /**
     * Set product data fixture
     *
     * @static
     * @return void
     */
    public static function productDataFixture()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product');
        $product->setData(array(
            'sku'               => 'test_product' . (int)microtime(true) . mt_rand(),
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
     * @static
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
            'email'        => 'my@example.com'
        ))->save();

        self::$_customer = $customer;
    }

    /**
     * Set consumer data fixture
     *
     * @static
     * @return void
     */
    public static function consumerDataFixture()
    {
        /** @var $consumer Mage_OAuth_Model_Consumer */
        $consumer = Mage::getModel('oauth/consumer');
        $consumer->setData(array(
            'name'   => date('Ymd-His') . ' Consumer Name Server Test',
            'key'    => md5(mt_rand()),
            'secret' => md5(mt_rand())
        ))->save();

        self::$_consumer = $consumer;
    }

    /**
     * Set token data fixture
     *
     * @static
     * @return void
     */
    public static function tokenDataFixture()
    {
        /** @var $helper Mage_OAuth_Helper_Data */
        $helper = Mage::helper('oauth');

        /** @var $consumer Mage_OAuth_Model_Token */
        $token = Mage::getModel('oauth/token');
        $token->setData(array(
            'consumer_id'       => self::$_consumer->getId(),
            'admin_id'          => null,
            'customer_id'       => self::$_customer->getId(),
            'type'              => Mage_OAuth_Model_Token::TYPE_ACCESS,
            'token'             => $helper->generateToken(),
            'secret'            => $helper->generateTokenSecret(),
            'verifier'          => $helper->generateVerifier(),
            'callback_url'      => Mage_OAuth_Model_Server::CALLBACK_ESTABLISHED,
            'authorized'        => 1,
            'revoked'           => 0,
        ))->save();

        self::$_token = $token;
    }

//    /**
//     * Get varien http client
//     *
//     * @return Varien_Http_Client
//     */
//    protected function _getClient()
//    {
//        /** @var $product Mage_Catalog_Model_Product */
//        $product = $this->getFixture('product');
//
//        /** @var $client Varien_Http_Client */
//        $client = new Varien_Http_Client('http://' . TESTS_HTTP_HOST . '/api/' . Mage_Api2_Model_Server::API_TYPE_REST
//            . '/products/' . $product->getId());
//
//        $client->setHeaders(array(
//            'Accept' => 'text/html',
//            'Version' => 1,
//            'Content-Type' => 'application/xml'
//        ));
//
//        return $client;
//    }

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
//        print_r(self::$_customer->getData());
    }

//    /**
//     * Test get product in HTML format
//     *
//     * @return void
//     */
//    public function testGetProductInHtmlFormat()
//    {
//        $this->_helperMock->expects($this->once())
//            ->method('getUserTypes')
//            ->will($this->returnValue(array('customer' => 'catalog/product')));
//
//        /** @var $response Zend_Http_Response */
//        $response = $this->_getClient()->setHeaders('Accept', 'text/html')->request();
//        echo $response->getBody();die;
//        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $response->getStatus());
//
//        $contentType = explode('; ', $response->getHeader('Content-type'));
//        $this->assertEquals('text/html', $contentType[0]);
//    }

//    /**
//     * Test get product in JSON format
//     *
//     * @return void
//     */
//    public function testGetProductInJsonFormat()
//    {
//        /** @var $response Zend_Http_Response */
//        $response = $this->_getClient()->setHeaders('Accept', 'application/json')->request();
//        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $response->getStatus());
//
//        $body = Zend_Json::decode($response->getBody());
//        $this->assertInternalType('array', $body);
//        $this->assertGreaterThan(0, count($body));
//
//        /** @var $product Mage_Catalog_Model_Product */
//        $product = $this->getFixture('product');
//
//        $this->assertEquals($product->getId(), $body['entity_id']);
//        $this->assertEquals($product->getSku(), $body['sku']);
//
//        $contentType = explode('; ', $response->getHeader('Content-type'));
//        $this->assertEquals('application/json', $contentType[0]);
//    }

//    /**
//     * Test get product in XML format
//     *
//     * @return void
//     */
//    public function testGetProductInXmlFormat()
//    {
//        /** @var $response Zend_Http_Response */
//        $response = $this->_getClient()->setHeaders('Accept', 'application/xml')->request();
//        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $response->getStatus());
//
//        /** @var $body Varien_Simplexml_Element */
//        $body = new Varien_Simplexml_Element($response->getBody());
//        $members = $body->xpath('params/param/value/struct/member');
//        $this->assertEquals(2, count($members));
//
//        /** @var $product Mage_Catalog_Model_Product */
//        $product = $this->getFixture('product');
//
//        foreach ($members as $member) {
//            /** @var $member Varien_Simplexml_Element */
//            if ('entity_id' == $member->name) {
//                $this->assertEquals($member->value->string->asArray(), $product->getId());
//            } elseif ('sku' == $member->name) {
//                $this->assertEquals($member->value->string->asArray(), $product->getSku());
//            } else {
//                $this->fail('Bad param in response.');
//            }
//        }
//
//        $contentType = explode('; ', $response->getHeader('Content-type'));
//        $this->assertEquals('application/xml', $contentType[0]);
//    }

//    /**
//     * Test dispatch wrong resource
//     *
//     * @return void
//     */
//    public function testGetWrongResource()
//    {
//        /** @var $response Zend_Http_Response */
//        $response = $this->_getClient()->setUri('http://' . TESTS_HTTP_HOST . '/api/'
//            . Mage_Api2_Model_Server::API_TYPE_REST . '/qwerty')->request();
//
//        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $response->getStatus());
//    }
}
