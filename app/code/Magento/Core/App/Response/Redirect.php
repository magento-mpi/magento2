<?php
/**
 * Response redirector
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\Response;

class Redirect implements \Magento\App\Response\RedirectInterface
{
    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Encryption\UrlCoder
     */
    protected $_urlCoder;

    /**
     * @var \Magento\Session\SessionManagerInterface
     */
    protected $_session;

    /**
     * @var \Magento\Session\SidResolverInterface
     */
    protected $_sidResolver;

    /**
     * @var bool
     */
    protected $_canUseSessionIdInParam;

    /**
     * @var \Magento\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Encryption\UrlCoder $urlCoder
     * @param \Magento\Session\SessionManagerInterface $session
     * @param \Magento\Session\SidResolverInterface $sidResolver
     * @param \Magento\UrlInterface $urlBuilder
     * @param bool $canUseSessionIdInParam
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Encryption\UrlCoder $urlCoder,
        \Magento\Session\SessionManagerInterface $session,
        \Magento\Session\SidResolverInterface $sidResolver,
        \Magento\UrlInterface $urlBuilder,
        $canUseSessionIdInParam = true
    ) {
        $this->_canUseSessionIdInParam = $canUseSessionIdInParam;
        $this->_request = $request;
        $this->_storeManager = $storeManager;
        $this->_urlCoder = $urlCoder;
        $this->_session = $session;
        $this->_sidResolver = $sidResolver;
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * @return string
     */
    protected function _getUrl()
    {
        $refererUrl = $this->_request->getServer('HTTP_REFERER');
        $url = (string)$this->_request->getParam(self::PARAM_NAME_REFERER_URL);
        if ($url) {
            $refererUrl = $url;
        }
        $url = $this->_request->getParam(\Magento\App\Action\Action::PARAM_NAME_BASE64_URL);
        if ($url) {
            $refererUrl = $this->_urlCoder->decode($url);
        }
        $url = $this->_request->getParam(\Magento\App\Action\Action::PARAM_NAME_URL_ENCODED);
        if ($url) {
            $refererUrl = $this->_urlCoder->decode($url);
        }

        if (!$this->_isUrlInternal($refererUrl)) {
            $refererUrl = $this->_storeManager->getStore()->getBaseUrl();
        }
        return $refererUrl;
    }

    /**
     * Identify referer url via all accepted methods (HTTP_REFERER, regular or base64-encoded request param)
     *
     * @return string
     */
    public function getRefererUrl()
    {
        return $this->_getUrl();
    }

    /**
     * Set referer url for redirect in response
     *
     * @param   string $defaultUrl
     * @return  \Magento\App\ActionInterface
     */
    public function getRedirectUrl($defaultUrl = null)
    {
        $refererUrl = $this->_getUrl();
        if (empty($refererUrl)) {
            $refererUrl = empty($defaultUrl)
                ? $this->_storeManager->getStore()->getBaseUrl()
                : $defaultUrl;
        }
        return $refererUrl;
    }

    /**
     * Redirect to error page
     *
     * @param string $defaultUrl
     * @return  string
     */
    public function error($defaultUrl)
    {
        $errorUrl = $this->_request->getParam(self::PARAM_NAME_ERROR_URL);
        if (empty($errorUrl)) {
            $errorUrl = $defaultUrl;
        }
        if (!$this->_isUrlInternal($errorUrl)) {
            $errorUrl = $this->_storeManager->getStore()->getBaseUrl();
        }
        return $errorUrl;
    }

    /**
     * Redirect to success page
     *
     * @param string $defaultUrl
     * @return string
     */
    public function success($defaultUrl)
    {
        $successUrl = $this->_request->getParam(self::PARAM_NAME_SUCCESS_URL);
        if (empty($successUrl)) {
            $successUrl = $defaultUrl;
        }
        if (!$this->_isUrlInternal($successUrl)) {
            $successUrl = $this->_storeManager->getStore()->getBaseUrl();
        }
        return $successUrl;
    }

    /**
     * Set redirect into response
     *
     * @param \Magento\App\ResponseInterface $response
     * @param string $path
     * @param array $arguments
     */
    public function redirect(\Magento\App\ResponseInterface $response, $path, $arguments = array())
    {
        if ($this->_session->getCookieShouldBeReceived()
            && $this->_urlBuilder->getUseSession()
            && $this->_canUseSessionIdInParam
        ) {
            $arguments += array('_query' => array(
                $this->_sidResolver->getSessionIdQueryParam($this->_session) => $this->_session->getSessionId()
            ));
        }
        $response->setRedirect($this->_urlBuilder->getUrl($path, $arguments));
    }

    /**
     * Check whether URL is internal
     *
     * @param string $url
     * @return bool
     */
    protected function _isUrlInternal($url)
    {
        if (strpos($url, 'http') !== false) {
            $unsecure = (strpos($url, $this->_storeManager->getStore()->getBaseUrl()) === 0);
            $secure = strpos(
                    $url,
                    $this->_storeManager->getStore()->getBaseUrl(\Magento\UrlInterface::URL_TYPE_LINK, true)
                ) === 0;
            return $unsecure || $secure;
        }
        return false;
    }
}
