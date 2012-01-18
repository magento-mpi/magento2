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
     *
     * @param boolean $asAdmin Administrator or customer
     */
    protected function _initConsumer($asAdmin = true)
    {
        /** @var $consumer Mage_OAuth_Model_Consumer */
        $consumer = Mage::getModel('oauth/consumer');

        if (!$consumer->load(1)->getId()) {
            die('Consumer with ID "1" not found');
        }
        /** @var $helper Mage_OAuth_Helper_Data */
        $helper = Mage::helper('oauth');

        if ($asAdmin) {
            $authorizeUrl = $helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_AUTHORIZE_ADMIN);
        } else {
            $authorizeUrl = $helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_AUTHORIZE_CUSTOMER);
        }
        $config = array(
            'requestTokenUrl' => $helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_INITIATE),
            'accessTokenUrl'  => $helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_TOKEN),
            'authorizeUrl'    => $authorizeUrl,
            'requestMethod'   => Zend_Oauth::POST,
            'consumerKey'     => $consumer->getKey(),
            'consumerSecret'  => $consumer->getSecret(),
            'signatureMethod' => 'HMAC-SHA1'
        );
        if ($callbackUrl = $consumer->getCallbackUrl()) {
            $config['callbackUrl'] = $callbackUrl;
        }
        $this->_consumer = new Zend_Oauth_Consumer($config);
    }

    /**
     * Test for client callback call
     */
    public function callbackAction()
    {
        if ($this->getRequest()->getParam(Mage_OAuth_Helper_Data::QUERY_PARAM_REJECTED, false)) {
            die('Token rejected by user.');
        }
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
        $this->_initConsumer($this->getRequest()->getParam('admin'));

        /** @var $session Mage_Core_Model_Session */
        $session = Mage::getSingleton('core/session');

        $requestToken = $this->_consumer->getRequestToken();

        if ($requestToken->isValid()) {

            if ($this->_request->getParam('token')) {
                echo $requestToken->getToken();
                exit;
            }

            $session->setInitToken($requestToken);

            if ($this->_request->getParam('simple')) {
                $request = null;
                if ($requestToken instanceof Zend_Oauth_Http_UserAuthorization) {
                    $request = $requestToken;
                    $token = null;
                }
                $redirectUrl = $this->_consumer->getRedirectUrl(null, $requestToken, $request);
                $redirectUrl = str_replace('/authorize', '/authorize/simple', $redirectUrl);
                $this->_redirectUrl($redirectUrl);
                return;
            } else {
                $this->_consumer->redirect(null, $requestToken);
            }



        } else {
            // for debug purposes
            echo '<pre><strong>Last request:</strong><br>';
            echo str_replace(',', ',<br>', $this->_consumer->getHttpClient()->getLastRequest());
            echo '<strong>Last response:</strong><br>';
            echo $this->_consumer->getHttpClient()->getLastResponse();
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

        try {
            $server->checkAccessRequest(Mage::getUrl('*/*/*'));

            echo 'Access Granted<br>';
        } catch (Exception $e) {
            echo 'Access Denied [' . $server->reportProblem($e) . '] <br>';
        }
    }

    /**
     * Test action for resource request with access token validation
     */
    public function testSimpleAction()
    {
        /** @var $tokenC Mage_OAuth_Model_Token */
        $tokenC = Mage::getModel('oauth/token')->load(1);
        if ($tokenC->getId()) {
            $tokenC->setAuthorized(0)->setType('request')->save();
        }
        /** @var $tokenA Mage_OAuth_Model_Token */
        $tokenA = Mage::getModel('oauth/token')->load(2);
        if ($tokenA->getId()) {
            $tokenA->setAuthorized(0)->setType('request')->save();
        }

        $baseUrl = Mage::app()->getStore()->getBaseUrl();

        $urlCustomer = $baseUrl . 'oauth/authorize/simple'
                . '?oauth_token=_token_'
                . '&oauth_callback=' . $baseUrl . 'oauth/client/simpleBack';
        $urlAdmin = $baseUrl . 'admin/oAuth_authorize/simple'
                . '?oauth_token=_token_'
                . '&oauth_callback=' . $baseUrl . 'oauth/client/simpleBack';
        ?>
        <html>
        <head>
            <script type="text/javascript">
                var callbackOAuth = {
                    'admin': '<?php echo $urlAdmin ?>',
                    'customer': '<?php echo $urlCustomer ?>'
                };
                function windowOpen(el) {
                    var id = el.id;
                    var url = callbackOAuth[id].replace('_token_', document.getElementById(id + '_token').value)
                    window.open(url,'new','width=650,height=350,toolbar=1');
                    return false;
                }
            </script>
        </head>
        <body>
            <h1>Magento authorize</h1>
            <input id="customer_token" type="text" maxlength="32" style="width: 230px"
                   value="<?php echo $tokenC->getToken() ?>" />
            <br />
            <a href="#" id="customer" onclick="windowOpen(this); return false;">Window Customer</a></div>
            <br />
            <br />
            <input id="admin_token" type="text" maxlength="32" style="width: 230px"
                   value="<?php echo $tokenA->getToken() ?>" />
            <br />
            <a href="#" id="admin" onclick="windowOpen(this);">Window Admin</a></div>
        </body>
        </html>
        <?php
    }

    /**
     * Test action for resource request with access token validation
     */
    public function simpleBackAction()
    {
        ?>
        <html>
        <body>
        <p>Request is done.</p>
        <p>Window will be closed in <span id="second">10</span> second.</p>
        <p><strong>Params:</strong></p>
        <ul>
            <?php foreach ($this->getRequest()->getParams() as $param) : ?>
                <li><?php echo $param; ?></li>
            <?php endforeach; ?>
        </ul>
        <script type="text/javascript">
            var func = function() {
                var el = document.getElementById('second');
                var s = el.innerHTML;
                if (s <= 1) {
                    window.close();
                }
                el.innerHTML = --s;
                setTimeout(func, 1000);
            };
            setTimeout(func, 1000);
        </script>
        </body>
        </html>
        <?php
    }
}
