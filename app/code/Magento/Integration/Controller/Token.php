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
namespace Magento\Integration\Controller;

class Token extends \Magento\App\Action\Action
{
    /** @var  \Magento\Oauth\OauthInterface */
    protected $_oauthService;

    /** @var  \Magento\Oauth\Helper\Request */
    protected $_helper;

    /***
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Oauth\OauthInterface $oauthService
     * @param \Magento\Oauth\Helper\Request $helper
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Oauth\OauthInterface $oauthService,
        \Magento\Oauth\Helper\Request $helper
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
            $requestUrl = $this->_helper->getRequestUrl($this->getRequest());
            $request = $this->_helper->prepareRequest($this->getRequest(), $requestUrl);

            // Request request token
            $response = $this->_oauthService->getRequestToken(
                $request, $requestUrl, $this->getRequest()->getMethod());
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
            $requestUrl = $this->_helper->getRequestUrl($this->getRequest());
            $request = $this->_helper->prepareRequest($this->getRequest(), $requestUrl);

            // Request access token in exchange of a pre-authorized token
            $response = $this->_oauthService->getAccessToken(
                $request, $requestUrl, $this->getRequest()->getMethod());
        } catch (\Exception $exception) {
            $response = $this->_helper->prepareErrorResponse(
                $exception,
                $this->getResponse()
            );
        }
        $this->getResponse()->setBody(http_build_query($response));
    }
}
