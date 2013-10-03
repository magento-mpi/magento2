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
namespace Magento\Oauth\Controller;

class Token extends \Magento\Core\Controller\Front\Action
{
    /** @var  \Magento\Oauth\Service\OauthV1Interface */
    protected $_oauthService;

    /** @var  \Magento\Oauth\Helper\Data */
    protected $_helper;

    /**
     * @param \Magento\Oauth\Service\OauthV1Interface $oauthService
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Oauth\Helper\Data $helper
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Oauth\Service\OauthV1Interface $oauthService,
        \Magento\Oauth\Helper\Data $helper
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
            $request = $this->_helper->prepareServiceRequest($this->getRequest());

            //Request request token
            $response = $this->_oauthService->getRequestToken($request);

        } catch (\Exception $exception) {
            $response = $this->_helper->prepareErrorResponse(
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
            $request = $this->_helper->prepareServiceRequest($this->getRequest());

            //Request access token in exchange of a pre-authorized token
            $response = $this->_oauthService->getAccessToken($request);

        } catch (\Exception $exception) {
            $response = $this->_helper->prepareErrorResponse(
                $exception,
                $this->getResponse()
            );
        }
        $this->getResponse()->setBody(http_build_query($response));
    }

}
