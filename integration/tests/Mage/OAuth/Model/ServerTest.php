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
 * @package     Mage_OAuth
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test OAuth Server
 *
 */
class Mage_OAuth_Model_ServerTest extends Magento_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    protected function setUp()
    {
        /** @var $consumer Mage_OAuth_Model_Consumer */
        $consumer = Mage::getModel('oauth/consumer');

        $consumer->setName('Unit Test Consumer')
            ->setCallbackUrl('http://' . TESTS_HTTP_HOST . '/oauth/client/callback')
            ->setKey('12345678901234567890123456789012')
            ->setSecret('12345678901234567890123456789012');

        $consumer->save();

        $this->setFixture('consumer', $consumer);

        /** @var $helper Mage_OAuth_Helper_Data */
        /*$helper = Mage::helper('oauth');

        $config = array(
            'requestTokenUrl' => $helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_INITIATE),
            'accessTokenUrl'  => $helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_TOKEN),
            'authorizeUrl'    => $helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_AUTHORIZE_CUSTOMER),
            'requestMethod'   => Zend_Oauth::POST,
            'consumerKey'     => $consumer->getKey(),
            'consumerSecret'  => $consumer->getSecret(),
            'callbackUrl'     => $consumer->getCallbackUrl(),
            'signatureMethod' => 'HMAC-SHA1'
        );

        $this->setFixture('client', new Zend_Oauth_Consumer($config));*/

        parent::setUp();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->deleteFixture('consumer', true);

        parent::tearDown();
    }

    /**
     * Test for product add to shopping cart
     *
     * @return void
     */
    public function testGetRequestToken()
    {
    }
}
