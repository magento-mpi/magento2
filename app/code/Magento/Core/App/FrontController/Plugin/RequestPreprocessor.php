<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\FrontController\Plugin;

class RequestPreprocessor
{
    /**
     * @var \Magento\Core\App\Request\RewriteService RewriteService
     */
    protected $_rewriteService;

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * @param \Magento\Core\App\Request\RewriteService $rewriteService
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\App\State $appState
     * @param \Magento\Core\Model\Url $url
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Core\Model\Store\Config $storeConfig
     */
    public function __construct(
        \Magento\Core\App\Request\RewriteService $rewriteService,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\App\State $appState,
        \Magento\Core\Model\Url $url,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Core\Model\Store\Config $storeConfig
    ) {
        $this->_backendData = $backendData;
        $this->_rewriteService = $rewriteService;
        $this->_storeManager = $storeManager;
        $this->_appState = $appState;
        $this->_url = $url;
        $this->_storeConfig = $storeConfig;
    }

    /**
     * Preprocess request
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return mixed
     */
    public function aroundDispatch(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $request = $arguments[0];
        // If pre-configured, check equality of base URL and requested URL
        $this->_checkBaseUrl($request);
        $request->setDispatched(false);
        $this->_rewriteService->applyRewrites($request);

        return $invocationChain->proceed($arguments);
    }

    /**
     * Check if requested path starts with one of the admin front names
     *
     * @param \Magento\App\RequestInterface $request
     * @return boolean
     */
    protected function _isAdminFrontNameMatched($request)
    {
        $pathPrefix = $this->_extractPathPrefixFromUrl($request);
        return $pathPrefix == $this->_backendData->getAreaFrontName();
    }

    /**
     * Extract first path part from url (in most cases this is area code)
     *
     * @param \Magento\App\RequestInterface $request
     * @return string
     */
    protected function _extractPathPrefixFromUrl($request)
    {
        $pathPrefix = ltrim($request->getPathInfo(), '/');
        $urlDelimiterPos = strpos($pathPrefix, '/');
        if ($urlDelimiterPos) {
            $pathPrefix = substr($pathPrefix, 0, $urlDelimiterPos);
        }

        return $pathPrefix;
    }

    /**
     * Auto-redirect to base url (without SID) if the requested url doesn't match it.
     * By default this feature is enabled in configuration.
     *
     * @param \Magento\App\RequestInterface $request
     */
    protected function _checkBaseUrl($request)
    {
        if (!$this->_appState->isInstalled() || $request->getPost() || strtolower($request->getMethod()) == 'post') {
            return;
        }

        $redirectCode = (int)$this->_storeConfig->getConfig('web/url/redirect_to_base');
        if (!$redirectCode) {
            return;
        } elseif ($redirectCode != 301) {
            $redirectCode = 302;
        }

        if ($this->_isAdminFrontNameMatched($request)) {
            return;
        }

        $baseUrl = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Core\Model\Store::URL_TYPE_WEB,
            $this->_storeManager->getStore()->isCurrentlySecure()
        );
        if (!$baseUrl) {
            return;
        }

        $uri = parse_url($baseUrl);
        $requestUri = $request->getRequestUri() ? $request->getRequestUri() : '/';
        if (isset($uri['scheme']) && $uri['scheme'] != $request->getScheme()
            || isset($uri['host']) && $uri['host'] != $request->getHttpHost()
            || isset($uri['path']) && strpos($requestUri, $uri['path']) === false
        ) {
            $redirectUrl = $this->_url->getRedirectUrl(
                $this->_url->getUrl(ltrim($request->getPathInfo(), '/'), array('_nosid' => true))
            );

            $this->_response
                ->setRedirect($redirectUrl, $redirectCode)
                ->sendResponse();
            exit;
        }
    }
}