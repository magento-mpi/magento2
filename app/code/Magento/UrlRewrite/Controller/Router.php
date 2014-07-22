<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Controller;

/**
 * UrlRewrite Controller Router
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /** @var \Magento\Framework\UrlInterface */
    protected $url;

    /** @var \Magento\Framework\App\State */
    protected $appState;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /** @var \Magento\Framework\App\ResponseInterface */
    protected $response;

    /** @var \Magento\UrlRewrite\Service\V1\UrlMatcherInterface */
    protected $urlMatcher;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Magento\UrlRewrite\Service\V1\UrlMatcherInterface $urlMatcher
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\State $appState,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\UrlRewrite\Service\V1\UrlMatcherInterface $urlMatcher
    ) {
        parent::__construct($actionFactory);
        $this->url = $url;
        $this->appState = $appState;
        $this->storeManager = $storeManager;
        $this->response = $response;
        $this->urlMatcher = $urlMatcher;
    }

    /**
     * Validate and Match Cms Page and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return mixed
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->appState->isInstalled()) {
            $this->response->setRedirect($this->url->getUrl('install'))->sendResponse();
            return null;
        }

        $identifier = trim($request->getPathInfo(), '/');
        $urlRewrite = $this->urlMatcher->match($identifier, $this->storeManager->getStore()->getId());
        if ($urlRewrite === null) {
            return null;
        }

        $redirectType = $urlRewrite->getRedirectType();
        if ($redirectType) {
            //@TODO: UrlRewrite Change constant values to redirect code
            $redirectCode = $redirectType == \Magento\UrlRewrite\Model\OptionProvider::PERMANENT ? 301 : 302;
            $this->response->setRedirect($urlRewrite->getTargetPath(), $redirectCode);
            $request->setDispatched(true);
            return $this->_actionFactory->createController(
                'Magento\Framework\App\Action\Redirect',
                array('request' => $request)
            );
        }

        $request->setPathInfo('/' . $urlRewrite->getTargetPath());
        return $this->_actionFactory->createController(
            'Magento\Framework\App\Action\Forward',
            array('request' => $request)
        );
    }
}
