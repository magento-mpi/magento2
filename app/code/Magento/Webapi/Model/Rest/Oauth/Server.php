<?php
/**
 * Two-legged OAuth server.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Rest_Oauth_Server extends Magento_Oauth_Model_Server
{
    /**
     * Construct server.
     *
<<<<<<< HEAD:app/code/Mage/Webapi/Model/Rest/Oauth/Server.php
     * @param Mage_Webapi_Controller_Rest_Request $request
     * @param Mage_Oauth_Model_Token_Factory $tokenFactory
     * @param Mage_Webapi_Model_Acl_User_Factory $consumerFactory
     * @param Mage_Oauth_Model_Nonce_Factory $nonceFactory
     */
    public function __construct(
        Mage_Webapi_Controller_Rest_Request $request,
        Mage_Oauth_Model_Token_Factory $tokenFactory,
        Mage_Webapi_Model_Acl_User_Factory $consumerFactory,
        Mage_Oauth_Model_Nonce_Factory $nonceFactory
=======
     * @param Magento_Webapi_Controller_Request_Rest $request
     * @param Magento_Oauth_Model_Token_Factory $tokenFactory
     * @param Magento_Webapi_Model_Acl_User_Factory $consumerFactory
     * @param Magento_Oauth_Model_Nonce_Factory $nonceFactory
     */
    public function __construct(
        Magento_Webapi_Controller_Request_Rest $request,
        Magento_Oauth_Model_Token_Factory $tokenFactory,
        Magento_Webapi_Model_Acl_User_Factory $consumerFactory,
        Magento_Oauth_Model_Nonce_Factory $nonceFactory
>>>>>>> upstream/develop:app/code/Magento/Webapi/Model/Rest/Oauth/Server.php
    ) {
        parent::__construct($request, $tokenFactory, $consumerFactory, $nonceFactory);
    }

    /**
     * Authenticate two-legged REST request.
     *
     * @return Magento_Webapi_Model_Acl_User
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
