<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * oAuth token controller
 */
class Magento_Oauth_Controller_Token extends Magento_Core_Controller_Front_Action
{
    /**#@+
     * HTTP Response Codes
     */
    const HTTP_OK = 200;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_INTERNAL_ERROR = 500;
    /**#@-*/

    /** @var  Magento_Oauth_Service_OauthV1Interface */
    protected $_oauthService;

    /** @var  Magento_Oauth_Helper_Data */
    protected $_helper;

    /**
     * @param Magento_Oauth_Service_OauthV1Interface $oauthService
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Oauth_Helper_Data $helper
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Oauth_Service_OauthV1Interface $oauthService,
        Magento_Oauth_Helper_Data $helper
    ) {
        parent::__construct($context);
        $this->_oauthService = $oauthService;
        $this->_helper = $helper;
    }

    /**
     *  Initiate RequestToken request operation
     */
    public function requestAction()
    {
        try {
            $request = $this->_helper->_prepareServiceRequest($this->getRequest());

            //Request request token
            $response = $this->_oauthService->getRequestToken($request);

        } catch (Exception $exception) {
            $response = $this->_helper->_prepareErrorResponse(
                $exception,
                $this->getResponse()
            );
        }
        $this->getResponse()->setBody(http_build_query($response));
    }

    /**
     * Initiate AccessToken request operation
     */
    public function accessAction()
    {
        try {
            $request = $this->_helper->_prepareServiceRequest($this->getRequest());

            //Request access token in exchange of a pre-authorized token
            $response = $this->_oauthService->getAccessToken($request);

        } catch (Exception $exception) {
            $response = $this->_helper->_prepareErrorResponse(
                $exception,
                $this->getResponse()
            );
        }
        $this->getResponse()->setBody(http_build_query($response));
    }

}