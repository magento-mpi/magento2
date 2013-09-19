<?php
/**
 * REST web API authentication model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Authentication
{
    /** @var Magento_Oauth_Service_OauthV1Interface */
    protected $_oauthService;

    /** @var  Magento_Oauth_Helper_Data */
    protected $_helper;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Oauth_Service_OauthV1Interface $oauthService
     * @param Magento_Oauth_Helper_Data $helper
     */
    public function __construct(
        Magento_Oauth_Service_OauthV1Interface $oauthService,
        Magento_Oauth_Helper_Data $helper
    ) {
        $this->_oauthService = $oauthService;
        $this->_helper = $helper;
    }

    /**
     * Authenticate user.
     *
     * @param Zend_Controller_Request_Http $httpRequest
     * @throws Magento_Webapi_Exception If authentication failed
     */
    public function authenticate($httpRequest)
    {
        try {
            $this->_oauthService->validateAccessToken($this->_helper->_prepareTokenRequest($httpRequest));
        } catch (Exception $e) {
            throw new Magento_Webapi_Exception(
                $e, //TODO : Fix this to report oAUth problem appropriately
                //$this->_oauthServer->reportProblem($e),
                Magento_Webapi_Exception::HTTP_UNAUTHORIZED
            );
        }
    }
}
