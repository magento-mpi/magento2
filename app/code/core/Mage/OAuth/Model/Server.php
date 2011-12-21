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
 * oAuth Server
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Model_Server extends Mage_Catalog_Model_Abstract
{
    /**#@+
     * OAuth result statuses
     */
    const OK                                = 0;
    const VERSION_REJECTED                  = 1;
    const PARAMETER_ABSENT                  = 2;
    const PARAMETER_REJECTED                = 3;
    const TIMESTAMP_REFUSED                 = 4;
    const NONCE_USED                        = 5;
    const SIGNATURE_METHOD_REJECTED         = 6;
    const SIGNATURE_INVALID                 = 7;
    const CONSUMER_KEY_UNKNOWN              = 8;
    const CONSUMER_KEY_REJECTED             = 9;
    const CONSUMER_KEY_REFUSED              = 10;
    const TOKEN_USED                        = 11;
    const TOKEN_EXPIRED                     = 12;
    const TOKEN_REVOKED                     = 13;
    const TOKEN_REJECTED                    = 14;
    const VERIFIER_INVALID                  = 15;
    const ADDITIONAL_AUTHORIZATION_REQUIRED = 16;
    const PERMISSION_UNKNOWN                = 17;
    const PERMISSION_DENIED                 = 18;
    const USER_REFUSED                      = 19;
    /**#@- */


    /**
     * Value of callback URL when it is established
     *
     * @link http://tools.ietf.org/html/rfc5849#section-2.1     Requirement in RFC-5849
     */
    const CALLBACK_ESTABLISHED              = 'oob';

    /**
     * Consumer object
     *
     * @var Mage_OAuth_Model_Consumer
     */
    protected $_consumer;

    /**
     * Error code to error messsages pairs
     *
     * @var array
     */
    protected $_errors = array(
        self::VERSION_REJECTED                  => 'version_rejected',
        self::PARAMETER_ABSENT                  => 'parameter_absent',
        self::PARAMETER_REJECTED                => 'parameter_rejected',
        self::TIMESTAMP_REFUSED                 => 'timestamp_refused',
        self::NONCE_USED                        => 'nonce_used',
        self::SIGNATURE_METHOD_REJECTED         => 'signature_method_rejected',
        self::SIGNATURE_INVALID                 => 'signature_invalid',
        self::CONSUMER_KEY_UNKNOWN              => 'consumer_key_unknown',
        self::CONSUMER_KEY_REJECTED             => 'consumer_key_rejected',
        self::CONSUMER_KEY_REFUSED              => 'consumer_key_refused',
        self::TOKEN_USED                        => 'token_used',
        self::TOKEN_EXPIRED                     => 'token_expired',
        self::TOKEN_REVOKED                     => 'token_revoked',
        self::TOKEN_REJECTED                    => 'token_rejected',
        self::VERIFIER_INVALID                  => 'verifier_invalid',
        self::ADDITIONAL_AUTHORIZATION_REQUIRED => 'additional_authorization_required',
        self::PERMISSION_UNKNOWN                => 'permission_unknown',
        self::PERMISSION_DENIED                 => 'permission_denied',
        self::USER_REFUSED                      => 'user_refused'
    );

    /**
     * oAuth helper object
     *
     * @var Mage_OAuth_Helper_Data
     */
    protected $_helper;

    /**
     * Request parameters
     *
     * @var array
     */
    protected $_params = null;

    /**
     * Request object
     *
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request = null;

    /**
     * Response object
     *
     * @var Mage_Core_Controller_Response_Http
     */
    protected $_response = null;

    /**
     * Token object
     *
     * @var Mage_OAuth_Model_Token
     */
    protected $_token;

    /**
     * Internal constructor not depended on params
     */
    protected function _construct()
    {
        $this->_helper = Mage::helper('oauth');
    }

    /**
     * Create temporary token object and save it
     *
     * @return Mage_OAuth_Model_Server
     */
    protected function _createTmpToken()
    {
        if (!$this->_consumer) {
            Mage::throwException('Initialize consumer first');
        }
        $this->_token = Mage::getModel('oauth/token');

        if (!empty($this->_params['oauth_callback'])
            && self::CALLBACK_ESTABLISHED != $this->_params['oauth_callback']
        ) {
            $callbackUrl = $this->_params['oauth_callback'];
        } else {
            $callbackUrl = $this->_consumer->getCallBackUrl();
        }
        if (!$callbackUrl) {
            //TODO: is additional check for callback URL validity required?
        }

        $this->_token->setConsumerId($this->_consumer->getId());
        $this->_token->setTmpCallbackUrl($callbackUrl);
        $this->_token->setTmpToken($this->_helper->generateToken(32));
        $this->_token->setTmpTokenSecret($this->_helper->generateToken(32));

        $this->_token->save();

        return $this;
    }

    /**
     * Extract parameters from sources (GET, FormBody, Authorization header), decode them and validate
     *
     * @return Mage_OAuth_Model_Server
     */
    protected function _extractParameters()
    {
        $request = $this->_getRequest();
        $this->_params = $request->getQuery();

        if ($request->getHeader(Zend_Http_Client::CONTENT_TYPE) == Zend_Http_Client::ENC_URLENCODED) {
            $bodyParams = array();

            parse_str($request->getRawBody(), $bodyParams);

            if (count($bodyParams)) {
                $this->_params = array_merge($this->_params, $bodyParams);
            }
        }
        $headerValue = $request->getHeader('Authorization');

        if ($headerValue) {
            $headerValue = substr($headerValue, 6); // ignore 'OAuth ' at the beginning

            foreach (explode(',', $headerValue) as $paramStr) {
                $nameAndValue = explode('=', $paramStr, 2);

                if (count($nameAndValue) < 2) {
                    continue;
                }
                if (preg_match('/oauth_[a-z_-]+/', $nameAndValue[0])) {
                    $this->_params[rawurldecode($nameAndValue[0])] = rawurldecode(trim($nameAndValue[1], '"'));
                }
            }
        }

        // parameters validation
        $reqFields = array(
            'oauth_consumer_key', 'oauth_signature_method', 'oauth_timestamp', 'oauth_nonce', 'oauth_signature'
        );

        foreach ($reqFields as $reqField) {
            if (!isset($this->_params[$reqField])) {
                $this->_reportProblem(Mage::exception('Mage_OAuth', $reqField, self::PARAMETER_ABSENT));
            }
        }
        // validate signature method
        $this->_validateSignatureMethod($this->_params['oauth_signature_method']);

        // validate nonce data if specific signature method selected
        if ('HMAC-SHA1' == $this->_params['oauth_signature_method']
            || 'RSA-SHA1' == $this->_params['oauth_signature_method']) {
            $this->_validateNonce(
                $this->_params['oauth_consumer_key'], $this->_params['oauth_nonce'], $this->_params['oauth_timestamp']
            );
        }
        return $this;
    }

    /**
     * Retrieve token parameters for permanent access request
     *
     * @return string
     */
    protected function _getAccessToken()
    {
        $tokenParams = array(
            'oauth_token' => $this->_token->getToken(), 'oauth_token_secret' => $this->_token->getTokenSecret()
        );

        return http_build_query($tokenParams);
    }

    /**
     * Retrieve token parameters for initiate request
     *
     * @return string
     */
    protected function _getInitiateToken()
    {
        $tokenParams = array(
            'oauth_token'              => $this->_token->getTmpToken(),
            'oauth_token_secret'       => $this->_token->getTmpTokenSecret(),
            'oauth_callback_confirmed' => 'true'
        );

        return http_build_query($tokenParams);
    }

    /**
     * Retrieve request object
     *
     * @return Mage_Core_Controller_Request_Http
     */
    protected function _getRequest()
    {
        if (null === $this->_request) {
            $this->_request = Mage::app()->getRequest();
        }
        return $this->_request;
    }

    /**
     * Retrieve response object
     *
     * @return Mage_Core_Controller_Response_Http
     */
    protected function _getResponse()
    {
        if (null === $this->_response) {
            $this->_response = Mage::app()->getResponse();
        }
        return $this->_response;
    }

    /**
     * Initialize and try to load consumer object
     *
     * @return Mage_OAuth_Model_Server
     */
    protected function _initConsumer()
    {
        $this->_consumer = Mage::getModel('oauth/consumer');

        if (empty($this->_params['oauth_consumer_key'])) {
            $this->_reportProblem(Mage::exception('Mage_OAuth', self::CONSUMER_KEY_UNKNOWN));
        } else {
            $this->_consumer->load($this->_params['oauth_consumer_key'], 'key');
        }
        if (!$this->_consumer->getId()) {
            $this->_reportProblem(Mage::exception('Mage_OAuth', '', self::CONSUMER_KEY_REJECTED));
        }
        return $this;
    }

    /**
     * Load token object, validate it, set access data and save
     *
     * @return Mage_OAuth_Model_Server
     */
    protected function _initToken()
    {
        if (!$this->_consumer) {
            Mage::throwException('Initialize consumer first');
        }
        $this->_validateTokenParam();
        $this->_validateVerifierParam();

        $this->_token = Mage::getModel('oauth/token');

        if (!$this->_token->load($this->_params['oauth_token'], 'tmp_token')->getId()) {
            $this->_reportProblem(Mage::exception('Mage_OAuth', $this->_params['oauth_token'], self::TOKEN_REJECTED));
        }
        if ($this->_token->getTmpVerifier() != $this->_params['oauth_verifier']) {
            $this->_reportProblem(Mage::exception('Mage_OAuth', '', self::TOKEN_REJECTED));
        }
        if ($this->_token->getConsumerId() != $this->_consumer->getId()) {
            $this->_reportProblem(Mage::exception('Mage_OAuth', '', self::TOKEN_REJECTED));
        }
        if ($this->_token->getToken()) {
            $this->_reportProblem(Mage::exception('Mage_OAuth', '', self::TOKEN_USED));
        }
        $this->_token->setToken($this->_helper->generateToken(32));
        $this->_token->setTokenSecret($this->_helper->generateToken(32));

        $this->_token->save();

        return $this;
    }

    /**
     * Report problem during request
     *
     * @param Mage_Oauth_Exception $e
     * @return string
     * @todo Move this method to try...catch without "exit"
     */
    protected function _reportProblem(Mage_Oauth_Exception $e)
    {
        $exceptionCode = $e->getCode();

        if (self::PARAMETER_ABSENT == $exceptionCode) {
            $msgAdd = '&oauth_parameters_absent=' . $e->getMessage();
        } elseif (self::SIGNATURE_INVALID == $exceptionCode) {
            $msgAdd =  '&debug_sbs=' . $e->getMessage();
        } else {
            $msgAdd = '';
        }
        if (isset($this->_errors[$exceptionCode])) {
            $msg = $this->_errors[$exceptionCode];
        } else {
            $msg = 'unknown_problem';
            $msgAdd = '&code=' . $exceptionCode;
        }
        if ($e->getMessage()) {
            $msgAdd .= '&message=' . $e->getMessage();
        }
        $this->_getResponse()->setBody('oauth_problem=' . $msg . $msgAdd);

        //TODO Move HTTP code to constant
        $this->_getResponse()->setHttpResponseCode(400);
        $this->_getResponse()->sendResponse();
        exit;
    }

    /**
     * Check nonce data validity
     *
     * @param string $consumerKey Consumer key
     * @param string $nonce Nonce string
     * @param int $timestamp UNIX timestamp of request
     * @return void
     */
    protected function _validateNonce($consumerKey, $nonce, $timestamp)
    {
        //TODO: try to get row from nonce table and return false if row exists
        if (false) {
            $this->_reportProblem(Mage::exception('Mage_OAuth', '', self::NONCE_USED));
        }
    }

    /**
     * Validate signature
     *
     * @param string $url Request URL to be a part of data to sign
     * @param string $tokenSecret OPTIONAL Token secret to be a part of data to sign
     * @return void
     */
    protected function _validateSignature($url, $tokenSecret = null)
    {
        // validate method calls order
        if (null === $this->_params || !$this->_consumer) {
            Mage::throwException('Extract parameters and initialize consumer first');
        }
        $util = new Zend_Oauth_Http_Utility();
        $params = $this->_params;

        $requestedSign = $params['oauth_signature'];
        unset($params['oauth_signature']);

        $calculatedSign = $util->sign(
            $params,
            $this->_params['oauth_signature_method'],
            $this->_consumer->getSecret(),
            $tokenSecret,
            Zend_Oauth::POST,
            $url
        );

        if ($calculatedSign != $requestedSign) {
            $this->_reportProblem(Mage::exception('Mage_OAuth', $calculatedSign, self::SIGNATURE_INVALID));
        }
    }

    /**
     * Check signature method validity
     *
     * @param string $sigMethod Signature method
     * @return void
     */
    protected function _validateSignatureMethod($sigMethod)
    {
        //TODO Move signatures to constants
        if (!in_array($sigMethod, array('HMAC-SHA1', 'RSA-SHA1', 'PLAINTEXT'))) {
            $this->_reportProblem(Mage::exception('Mage_OAuth', '', self::SIGNATURE_METHOD_REJECTED));
        }
    }

    /**
     * Check for 'oauth_token' parameter
     *
     * @return void
     */
    protected function _validateTokenParam()
    {
        if (empty($this->_params['oauth_token'])) {
            $this->_reportProblem(Mage::exception('Mage_OAuth', 'oauth_token', self::PARAMETER_ABSENT));
        }
    }

    /**
     * Check for 'oauth_verifier' parameter
     *
     * @return void
     */
    protected function _validateVerifierParam()
    {
        if (empty($this->_params['oauth_verifier'])) {
            $this->_reportProblem(Mage::exception('Mage_OAuth', 'oauth_verifier', self::PARAMETER_ABSENT));
        }
    }

    /**
     * Process request for permanent access token
     */
    public function accessToken()
    {
        $this->_extractParameters();
        $this->_initConsumer();
        $this->_initToken();
        $this->_validateSignature(
            $this->_helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_TOKEN),
            $this->_token->getTmpTokenSecret()
        );

        $this->_getResponse()->setBody($this->_getAccessToken());
    }

    /**
     * Process request for temporary (initiative) token
     */
    public function initiateToken()
    {
        $this->_extractParameters();
        $this->_initConsumer();
        $this->_validateSignature($this->_helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_INITIATE));
        $this->_createTmpToken();

        $this->_getResponse()->setBody($this->_getInitiateToken());
    }

    /**
     * Set request object
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return Mage_OAuth_Model_Server
     */
    public function setRequest(Mage_Core_Controller_Request_Http $request)
    {
        $this->_request = $request;

        return $this;
    }

    /**
     * Set response object
     *
     * @param Mage_Core_Controller_Response_Http $response
     * @return Mage_OAuth_Model_Server
     */
    public function setResponse(Mage_Core_Controller_Response_Http $response)
    {
        $this->_response = $response;

        $this->_response->setHeader(Zend_Http_Client::CONTENT_TYPE, Zend_Http_Client::ENC_URLENCODED, true);

        return $this;
    }
}
