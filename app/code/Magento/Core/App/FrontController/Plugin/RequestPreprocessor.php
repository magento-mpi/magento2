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
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * @var \Magento\App\ResponseFactory
     */
    protected $_responseFactory;

    /**
     * @var \Magento\Core\Model\Url
     */
    protected $_url;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @var \Magento\App\Dir
     */
    protected $_dir;

    /**
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\App\State $appState
     * @param \Magento\Core\Model\Url $url
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\App\ResponseFactory $responseFactory
     * @param \Magento\App\Dir $dir
     */
    public function __construct(
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\App\State $appState,
        \Magento\Core\Model\Url $url,
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\App\ResponseFactory $responseFactory,
        \Magento\App\Dir $dir
    ) {
        $this->_storeManager = $storeManager;
        $this->_appState = $appState;
        $this->_url = $url;
        $this->_storeConfig = $storeConfig;
        $this->_responseFactory = $responseFactory;
        $this->_dir = $dir;
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
        try {
            // If pre-configured, check equality of base URL and requested URL
            $this->_checkBaseUrl($request);
            $request->setDispatched(false);

            return $invocationChain->proceed($arguments);
        } catch (\Magento\Core\Model\Session\Exception $e) {
            header('Location: ' . $this->_storeManager->getStore()->getBaseUrl());
        } catch (\Magento\Core\Model\Store\Exception $e) {
            require $this->_dir->getDir(\Magento\App\Dir::PUB) . DS . 'errors' . DS . '404.php';
        }
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

            $response = $this->_responseFactory->create();
            $response->setRedirect($redirectUrl, $redirectCode);
            $response->sendResponse();
            exit;
        }
    }
}