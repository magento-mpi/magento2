<?php
/**
 * Two legged oAuth server
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Rest_Oauth_Server extends Mage_Oauth_Model_Server
{
    /**
     * Construct server.
     *
     * @param Mage_Webapi_Controller_Request_Rest $request
     * @param Mage_Oauth_Model_Token_Factory $tokenFactory
     * @param Mage_Webapi_Model_Acl_UserFactory $consumerFactory
     * @param Mage_Oauth_Model_Nonce_Factory $nonceFactory
     */
    public function __construct(
        Mage_Webapi_Controller_Request_Rest $request,
        Mage_Oauth_Model_Token_Factory $tokenFactory,
        Mage_Webapi_Model_Acl_UserFactory $consumerFactory,
        Mage_Oauth_Model_Nonce_Factory $nonceFactory
    ) {
        parent::__construct($request, $tokenFactory, $consumerFactory, $nonceFactory);
    }

    /**
     * Authenticate two-legged REST request.
     *
     * @return Mage_Webapi_Model_Acl_User
     */
    public function authenticateTwoLegged()
    {
        // get parameters from request
        $this->_fetchParams();

        // make generic validation of request parameters
        $this->_validateProtocolParams();

        // initialize consumer
        $this->_initConsumer();

        // validate signature
        $this->_validateSignature();

        return $this->_consumer;
    }
}
