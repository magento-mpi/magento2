<?php
/**
 * Request redirector
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Request;

class Redirect
{
    const PARAM_NAME_REFERER_URL        = 'referer_url';
    const PARAM_NAME_ERROR_URL          = 'error_url';
    const PARAM_NAME_SUCCESS_URL        = 'success_url';

    /** @var \Magento\App\RequestInterface */
    protected $_request;

    /** @var \Magento\Core\Model\StoreManagerInterface */
    protected $_storeManager;

    /** @var \Magento\Encryption\UrlCoder */
    protected $_urlCoder;

    /** @var \Magento\HTTP\Url */
    protected $_url;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Encryption\UrlCoder $urlCoder
     * @param \Magento\HTTP\Url $url
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Encryption\UrlCoder $urlCoder,
        \Magento\HTTP\Url $url
    ) {
        $this->_request = $request;
        $this->_storeManager = $storeManager;
        $this->_urlCoder = $urlCoder;
        $this->_url = $url;
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

        if (!$this->_url->isInternal($refererUrl)) {
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
                ? $this->_storeManager->getBaseUrl()
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
        if (!$this->_url->isInternal($errorUrl)) {
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
        if (!$this->_url->isInternal($successUrl)) {
            $successUrl = $this->_storeManager->getStore()->getBaseUrl();
        }
        return $successUrl;
    }
}
