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
    const ERR_OK                        = 0;
    const ERR_VERSION_REJECTED          = 1;
    const ERR_PARAMETER_ABSENT          = 2;
    const ERR_PARAMETER_REJECTED        = 3;
    const ERR_TIMESTAMP_REFUSED         = 4;
    const ERR_NONCE_USED                = 5;
    const ERR_SIGNATURE_METHOD_REJECTED = 6;
    const ERR_SIGNATURE_INVALID         = 7;
    const ERR_CONSUMER_KEY_REJECTED     = 8;
    const ERR_TOKEN_USED                = 9;
    const ERR_TOKEN_EXPIRED             = 10;
    const ERR_TOKEN_REVOKED             = 11;
    const ERR_TOKEN_REJECTED            = 12;
    const ERR_VERIFIER_INVALID          = 13;
    const ERR_PERMISSION_UNKNOWN        = 14;
    const ERR_PERMISSION_DENIED         = 15;
    /**#@-*/

    /**#@+
     * Signature Methods
     */
    const SIGNATURE_SHA1  = 'HMAC-SHA1';
    const SIGNATURE_SHA256 = 'HMAC-SHA256';

    /**#@-*/

    /**#@+
     * HTTP Response Codes
     */
    const HTTP_OK             = 200;
    const HTTP_BAD_REQUEST    = 400;
    const HTTP_UNAUTHORIZED   = 401;
    const HTTP_INTERNAL_ERROR = 500;
    /**#@-*/

    /**
     * Create a new consumer account when an Add-On is installed.
     *
     * @param array $addOnData - Information provided by an AddOn when the AddOn installed.
     * @return array - Consumer account information, including Oauth consumer key and secret.
     * @throws Mage_Oauth_Exception
     */
    public function createConsumer($addOnData);

	/**
	 * Issue a pre-authorization request token to the caller
	 *
	 * @param array input parameters includes consumer key, nonce, signature, signature method, timestamp, oauth version, auth code
     * @return array output containing the request token
     * @throws Mage_Oauth_Exception
	 */
	public function authorize($request);
}
