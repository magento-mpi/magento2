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
 * oAuth initiate controller
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_InitiateController extends Mage_Core_Controller_Front_Action
{
    /**
     * Extract parameters from 'Authorization' header and decode them
     *
     * @param string $headerValue Raw header value
     * @return array
     */
    protected function _extractAuthHeader($headerValue)
    {
        $headerValue = substr($headerValue, 6); // ignore 'OAuth ' at the beginning of string

        $params = array();

        foreach (explode(',', $headerValue) as $paramStr) {
            $nameAndValue = explode('=', $paramStr);

            if (count($nameAndValue) != 2) {
                Mage::throwException('Invalid Authorization header');
            }
            $params[$nameAndValue[0]] = rawurldecode(trim($nameAndValue[1], '"'));
        }
        return $params;
    }

    /**
     * Get prepared value for 'Authorization' header as a string
     *
     * @return string
     */
    protected function _getAuthHeaderValue()
    {
        $authParams = array(
            'realm'                  => $this->_getRealm(),
            'oauth_consumer_key'     => $this->_getConsumerKey(),
            'oauth_signature_method' => $this->_getSignatureMethod(),
            'oauth_timestamp'        => time(),
            'oauth_nonce'            => $this->_getNonce(),
            'oauth_callback'         => $this->_getCallback(),
            'oauth_signature'        => $this->_getSignature()
        );
        $paramsEncoded = array();

        foreach ($authParams as $paramName => $paramValue) {
            $paramsEncoded[] = $paramName . '="' . rawurlencode($paramValue) . '"';
        }
        return 'OAuth ' . implode(',', $paramsEncoded);
    }

    /**
     * Get callback URL to redirect customer to
     *
     * @return string
     */
    protected function _getCallback()
    {
        //TODO: fetch data from Consumer object
        return 'http://apia.com/initiate/ready/';
    }

    /**
     * Get consumer key for initiative request
     *
     * @return string
     */
    protected function _getConsumerKey()
    {
        //TODO: fetch data from Consumer object
        return 'dpf43f3p2l4k3l03';
    }

    /**
     * Retrieve token parameters for initite request
     *
     * @param bool $asQueryString OPTIONAL Return data type (if TRUE - HTTP query string, array - by default)
     * @return array|string
     */
    protected function _getInitiateTokenParams($asQueryString = false)
    {
        //TODO: generate parameters
        $tokenParams = array(
            'oauth_token'              => 'hh5s93j4hdidpola',
            'oauth_token_secret'       => 'hdhd0244k9j7ao03',
            'oauth_callback_confirmed' => 'true'
        );

        return $asQueryString ? http_build_query($tokenParams) : $tokenParams;
    }

    /**
     * Get oAuth nonce for initiative request
     *
     * @return string
     */
    protected function _getNonce()
    {
        //TODO: fetch data from Consumer object
        return 'wIjqoS';
    }

    /**
     * Get oAuth realm to request initiative token for
     *
     * @return string
     */
    protected function _getRealm()
    {
        //TODO: fetch data from Consumer object
        return 'TestRealm';
    }

    /**
     * Get signature for initiative request
     *
     * @return string
     */
    protected function _getSignature()
    {
        //TODO: fetch data from Consumer object
        return '74KNZJeDHnMBp0EMJ9ZHt/XKycU=';
    }

    /**
     * Get consumer key for initiative request
     *
     * @return string
     */
    protected function _getSignatureMethod()
    {
        //TODO: fetch data from Consumer object
        return 'HMAC-SHA1';
    }

    /**
     * Validate 'Authorization' header has been sent in initiative request
     *
     * @param string $headerValue Raw header value
     * @return bool
     */
    protected function _isAuthHeaderValid($headerValue)
    {
        $reqFields = array(
            'realm', 'oauth_consumer_key', 'oauth_signature_method', 'oauth_timestamp',
            'oauth_nonce', 'oauth_callback', 'oauth_signature'
        );

        try {
            $extracted = $this->_extractAuthHeader($headerValue);
        } catch (Exception $e) {
            return false;
        }
        foreach ($reqFields as $reqField) {
            if (!isset($extracted[$reqField])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Test action to generate initiative request to oAuth server
     */
    public function clientInitAction()
    {
        /** @var $helper Mage_OAuth_Helper_Data */
        $helper = Mage::app()->getHelper('oauth');
        $client = curl_init();

        curl_setopt($client, CURLOPT_URL, $helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_INITIATE));
        curl_setopt($client, CURLOPT_HEADER, false);
        curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($client, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($client, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($client, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($client, CURLOPT_HTTPHEADER, array('Authorization: ' . $this->_getAuthHeaderValue()));

        $result = curl_exec($client);

        if (false === $result) {
            Mage::throwException('cURL request failed: [' . curl_errno($client) . '] ' . curl_error($client));
        }
        curl_close($client);

        $oauthVars = array();

        parse_str($result, $oauthVars);

        if (empty($oauthVars['oauth_token']) || empty($oauthVars['oauth_token_secret'])
            || empty($oauthVars['oauth_callback_confirmed'])) {
            Mage::throwException('Required parameter does not exist in response');
        }
        $authUrl = $helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_AUTHORIZE);
        // redirect to auth form
        $this->_redirectUrl($authUrl . '?oauth_token=' . $oauthVars['oauth_token']);
    }

    /**
     * Index action.  Receive initiate request and response OAuth token
     */
    public function indexAction()
    {
        if (!$this->getRequest()->isPost()
            || !$this->_isAuthHeaderValid($this->getRequest()->getHeader('Authorization'))) {
            Mage::throwException('Invalid request');
        }
        $authParams = $this->_extractAuthHeader($this->getRequest()->getHeader('Authorization'));

        $this->getResponse()->setHeader('Content-Type', 'application/x-www-form-urlencoded', true);
        $this->getResponse()->setBody($this->_getInitiateTokenParams(true));
    }
}
