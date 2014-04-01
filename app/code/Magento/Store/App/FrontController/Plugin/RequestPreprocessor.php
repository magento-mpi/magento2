<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Store\App\FrontController\Plugin;

class RequestPreprocessor
{
    /**
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @var \Magento\App\ResponseFactory
     */
    protected $_responseFactory;

    /**
     * @var \Magento\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\State $appState
     * @param \Magento\UrlInterface $url
     * @param \Magento\App\Config\ScopeConfigInterface $storeConfig
     * @param \Magento\App\ResponseFactory $responseFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\App\State $appState,
        \Magento\UrlInterface $url,
        \Magento\App\Config\ScopeConfigInterface $storeConfig,
        \Magento\App\ResponseFactory $responseFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->_appState = $appState;
        $this->_url = $url;
        $this->_storeConfig = $storeConfig;
        $this->_responseFactory = $responseFactory;
    }

    /**
     * Auto-redirect to base url (without SID) if the requested url doesn't match it.
     * By default this feature is enabled in configuration.
     *
     * @param \Magento\App\FrontController $subject
     * @param callable $proceed
     * @param \Magento\App\RequestInterface $request
     *
     * @return \Magento\App\ResponseInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\App\FrontController $subject,
        \Closure $proceed,
        \Magento\App\RequestInterface $request
    ) {
        if ($this->_appState->isInstalled() && !$request->isPost() && $this->_isBaseUrlCheckEnabled()) {
            $baseUrl = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\UrlInterface::URL_TYPE_WEB,
                $this->_storeManager->getStore()->isCurrentlySecure()
            );
            if ($baseUrl) {
                $uri = parse_url($baseUrl);
                if (!$this->_isBaseUrlCorrect($uri, $request)) {
                    $redirectUrl = $this->_url->getRedirectUrl(
                        $this->_url->getUrl(ltrim($request->getPathInfo(), '/'), array('_nosid' => true))
                    );
                    $redirectCode = (int)$this->_storeConfig->getValue('web/url/redirect_to_base', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) !== 301
                        ? 302
                        : 301;

                    $response = $this->_responseFactory->create();
                    $response->setRedirect($redirectUrl, $redirectCode);
                    return $response;
                }
            }
        }
        $request->setDispatched(false);

        return $proceed($request);
    }

    /**
     * Is base url check enabled
     *
     * @return bool
     */
    protected function _isBaseUrlCheckEnabled()
    {
        return (bool) $this->_storeConfig->getValue('web/url/redirect_to_base', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check if base url enabled
     *
     * @param array $uri
     * @param \Magento\App\Request\Http $request
     * @return bool
     */
    protected function _isBaseUrlCorrect($uri, $request)
    {
        $requestUri = $request->getRequestUri() ? $request->getRequestUri() : '/';
        return (!isset(
            $uri['scheme']
        ) || $uri['scheme'] === $request->getScheme()) && (!isset(
            $uri['host']
        ) || $uri['host'] === $request->getHttpHost()) && (!isset(
            $uri['path']
        ) || strpos(
            $requestUri,
            $uri['path']
        ) !== false);
    }
}
