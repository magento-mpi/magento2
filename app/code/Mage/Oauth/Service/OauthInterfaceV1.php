<?php
/**
 * Web API Oauth Service.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Oauth_Service_OauthInterfaceV1
{
    /**#@+
     * OAuth result statuses
     */
    const ERR_OK = 0;
    const ERR_VERSION_REJECTED = 1;
    const ERR_PARAMETER_ABSENT = 2;
    const ERR_PARAMETER_REJECTED = 3;
    const ERR_TIMESTAMP_REFUSED = 4;
    const ERR_NONCE_USED = 5;
    const ERR_SIGNATURE_METHOD_REJECTED = 6;
    const ERR_SIGNATURE_INVALID = 7;
    const ERR_CONSUMER_KEY_REJECTED = 8;
    const ERR_TOKEN_USED = 9;
    const ERR_TOKEN_EXPIRED = 10;
    const ERR_TOKEN_REVOKED = 11;
    const ERR_TOKEN_REJECTED = 12;
    const ERR_VERIFIER_INVALID = 13;
    const ERR_PERMISSION_UNKNOWN = 14;
    const ERR_PERMISSION_DENIED = 15;
    const ERR_METHOD_NOT_ALLOWED = 16;

    /**#@-*/

    /**#@+
     * Signature Methods
     */
    const SIGNATURE_SHA1 = 'HMAC-SHA1';
    const SIGNATURE_SHA256 = 'HMAC-SHA256';

    /**#@+
     * HTTP Response Codes
     */
    const HTTP_OK = 200;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_INTERNAL_ERROR = 500;

    /**#@-*/

    /**
     * Create a new consumer account when an Add-On is installed.
     *
     * @param array $consumerData - Information provided by an Add-On when the Add-On is installed.
     * <pre>
     * array(
     *  'name' => 'Add-On Name',
     *  'key' => 'a6aa81cc3e65e2960a4879392445e718',
     *  'secret' => 'b7bb92dd4f76f3a71b598a4a3556f829',
     *  'http_post_url' => 'http://www.my-add-on.com'
     * )
     * </pre>
     * @return array - The Add-On (consumer) data.
     * @throws Mage_Core_Exception
     * @throws Mage_Oauth_Exception
     */
    public function createConsumer($consumerData);

    /**
     * Execute post to Add-On (consumer) HTTP Post URL. Generate and return oauth_verifier.
     *
     * @param array $consumerData - The Add-On (consumer) data.
     * <pre>
     * array(
     *  'entity_id' => 1
     *  'key' => 'a6aa81cc3e65e2960a4879392445e718',
     *  'secret' => 'b7bb92dd4f76f3a71b598a4a3556f829',
     *  'http_post_url' => 'http://www.my-add-on.com'
     *  )
     * </pre>
     * @return array - The oauth_verifier.
     * @throws Mage_Core_Exception
     * @throws Mage_Oauth_Exception
     */
    public function postToConsumer($consumerData);

    /**
     * Issue a pre-authorization request token to the caller
     *
     * @param array $request - Parameters including consumer key, nonce, signature, signature method, etc.
     * @return array - The request token/secret pair.
     * @throws Mage_Oauth_Exception
     */
    public function getRequestToken($request);

    /**
     * Get access token for a pre-authorized request token
     *
     * @param array $accessTokenReqArray array containing parameters necessary for requesting Access Token
     * <pre>
     * array(
     *  'oauth_version' => '1.0',
     *  'oauth_signature_method' => 'HMAC-SHA1',
     *  'oauth_token' => 'a6aa81cc3e65e2960a487939244sssss',
     *  'oauth_verifier' => 'a6aa81cc3e65e2960a487939244vvvvv',
     *  'oauth_nonce' => 'BXzEolwaQDDNlCv',
     *  'oauth_timestamp' => '1376922156',
     *  'oauth_consumer_key' => 'a6aa81cc3e65e2960a4879392445e718',
     *  'oauth_signature' => 'C1wtpLrci5Ak6kMCl/YN9J8Hmi0=',
     *  'request_url' => 'http://magento.ll/oauth/token?oauth_token=a6aa81cc3e65e2960a487939244sssss
     *                                                          &oauth_verifier=a6aa81cc3e65e2960a487939244vvvvv',
     *  'http_method' => 'POST'
     * )
     * </pre>
     * @return array - The access token/secret pair.
     */
    public function getAccessToken($requestArray);


    /**
     * Validate a requested access token
     *
     * @param array $requestArray containing parameters necessary for validating Access Token
     * <pre> eg array(
     *  'oauth_version' => '1.0',
     *  'oauth_signature_method' => 'HMAC-SHA1',
     *  'oauth_token' => 'a6aa81cc3e65e2960a487939244sssss',
     *  'oauth_nonce' => 'BXzEolwaQDDNlCv',
     *  'oauth_timestamp' => '1376922156',
     *  'oauth_consumer_key' => 'a6aa81cc3e65e2960a4879392445e718',
     *  'oauth_signature' => 'C1wtpLrci5Ak6kMCl/YN9J8Hmi0=',
     *  'request_url' => 'http://magento.ll/oauth/token?oauth_token=a6aa81cc3e65e2960a487939244sssss
     *                                                          &oauth_verifier=a6aa81cc3e65e2960a487939244vvvvv',
     *  'http_method' => 'POST'
     * )
     * </pre>
     * @return boolean true if requested access token is valid
     * @throws Mage_Oauth_Exception
     *
     */
    public function validateAccessToken($requestArray);


    /**
     * @return array()
     */
    public function getErrorMap();

    /**
     * @return array()
     */
    public function getErrorToHttpCodeMap();
}
