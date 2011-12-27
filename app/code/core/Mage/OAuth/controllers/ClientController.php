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
 * @category    Mage
 * @package     Mage_OAuth
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * oAuth authorize controller
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_ClientController extends Mage_Core_Controller_Front_Action
{
    /**
     * Consumer object
     *
     * @var Zend_Oauth_Consumer
     */
    protected $_consumer;

    /**
     * Initialize test consumer
     */
    protected function _initConsumer()
    {
        /** @var $consumer Mage_OAuth_Model_Consumer */
        $consumer = Mage::getModel('oauth/consumer');

        if (!$consumer->load(1)->getId()) {
            die('Consumer not found');
        }
        /** @var $helper Mage_OAuth_Helper_Data */
        $helper = Mage::helper('oauth');

        $config = array(
            'requestTokenUrl' => $helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_INITIATE),
            'accessTokenUrl'  => $helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_TOKEN),
            'authorizeUrl'    => $helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_AUTHORIZE),
            'requestMethod'   => Zend_Oauth::POST,
            'consumerKey'     => $consumer->getKey(),
            'consumerSecret'  => $consumer->getSecret(),
            'callbackUrl'     => $consumer->getCallbackUrl(),
            'signatureMethod' => 'HMAC-SHA1'
        );
        $this->_consumer = new Zend_Oauth_Consumer($config);
    }

    /**
     * Test for client callback call
     */
    public function callbackAction()
    {
        $this->_initConsumer();

        echo '<pre>';

        /** @var $session Mage_Core_Model_Session */
        $session = Mage::getSingleton('core/session');

        $initToken = $session->getInitToken();

        if (!$initToken) {
            die('No init token');
        }
        $accessToken = $this->_consumer->getAccessToken($this->getRequest()->getQuery(), $initToken);

        if ($accessToken->isValid()) {
            echo '<strong>Last request:</strong><br>';
            echo str_replace(',', ',<br>', $this->_consumer->getHttpClient()->getLastRequest());
            echo '<strong>Last response:</strong><br>';
            echo $this->_consumer->getHttpClient()->getLastResponse();

            $session->setAccessToken($accessToken);
        } else {
            echo 'Invalid access token received: ' . $this->_consumer->getHttpClient()->getLastResponse();
        }
    }

    /**
     * Test action to generate initiative request to oAuth server
     */
    public function clientInitAction()
    {
        $this->_initConsumer();

        echo '<pre>';

        /** @var $session Mage_Core_Model_Session */
        $session = Mage::getSingleton('core/session');

        $initToken = $session->getInitToken();

        if ($initToken) {
            // Zend_Oauth_Token_Request does not set RevisionA compatibility after wakeup from serialization
            // let's do it by ourselves
            if ($initToken->getParam(Zend_Oauth_Token::TOKEN_PARAM_CALLBACK_CONFIRMED)) {
                Zend_Oauth_Client::$supportsRevisionA = true;
            }
            $this->_consumer->redirect(null, $initToken);
        } else {
            $initToken = $this->_consumer->getRequestToken();
            echo '<strong>Last request:</strong><br>';
            echo str_replace(',', ',<br>', $this->_consumer->getHttpClient()->getLastRequest());
            echo '<strong>Last response:</strong><br>';
            echo $this->_consumer->getHttpClient()->getLastResponse();

            $session->setInitToken($initToken);
        }
    }

    /**
     * Test action to get resource with limited rights
     */
    public function getResourceAction()
    {
        echo '<pre>';
        /** @var $session Mage_Core_Model_Session */
        $session = Mage::getSingleton('core/session');

        $accessToken = $session->getAccessToken();

        if (!$accessToken) {
            die('No access token');
        }
        $this->_initConsumer();

        $callUrl = Mage::getUrl('*/client/photo');

        $client = new Zend_Oauth_Client(
            array(
                'consumerKey'    => $this->_consumer->getConsumerKey(),
                'consumerSecret' => $this->_consumer->getConsumerSecret()
            ),
            $callUrl
        );

        $client->setToken($accessToken);
        $client->request();

        echo '<strong>Call</strong> ' . $callUrl . ' with header<br><strong>Authorization:</strong>'
            . str_replace(',', ',<br>', $client->getHeader('Authorization'));
        echo '<br><br><strong>Response:</strong><br>' . $client->getLastResponse();
    }

    /**
     * Test action for resource request with access token validation
     */
    public function photoAction()
    {
        /** @var $server Mage_OAuth_Model_Server */
        $server = Mage::getModel('oauth/server');

        $server->checkAccessRequest(null, Mage::getUrl('*/*/*'));
    }
}
