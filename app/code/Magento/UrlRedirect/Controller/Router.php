<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Controller;

/**
 * UrlRedirect Controller Router
 */
class Router extends \Magento\Framework\App\Router\AbstractRouter
{
    /** @var \Magento\Framework\UrlInterface */
    protected $url;

    /** @var \Magento\Framework\App\State */
    protected $appState;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /** @var \Magento\Framework\App\ResponseInterface */
    protected $response;

    /** @var \Magento\UrlRedirect\Service\V1\UrlManager */
    protected $urlManager;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Magento\UrlRedirect\Service\V1\UrlManager $urlManager
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\State $appState,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\UrlRedirect\Service\V1\UrlManager $urlManager
    ) {
        parent::__construct($actionFactory);
        $this->url = $url;
        $this->appState = $appState;
        $this->storeManager = $storeManager;
        $this->response = $response;
        $this->urlManager = $urlManager;
    }

    /**
     * Validate and Match Cms Page and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->appState->isInstalled()) {
            $this->response->setRedirect($this->url->getUrl('install'))->sendResponse();
            exit;
        }

        $identifier = trim($request->getPathInfo(), '/');
        $urlRewrite = $this->urlManager->match($identifier, $this->storeManager->getStore()->getId());
        if ($urlRewrite === null) {
            return null;
        }

        $redirectType = $urlRewrite->getRedirectType();
        if ($redirectType) {
            $redirectCode = $redirectType == \Magento\UrlRedirect\Model\OptionProvider::PERMANENT ? 301 : 302;
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
