<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Controller;

use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\UrlFinderInterface;

/**
 * UrlRewrite Controller Router
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /** var \Magento\Framework\App\ActionFactory */
    protected $actionFactory;

    /** @var \Magento\Framework\UrlInterface */
    protected $url;

    /** @var \Magento\Framework\App\State */
    protected $appState;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /** @var \Magento\Framework\App\ResponseInterface */
    protected $response;

    /** @var UrlFinderInterface */
    protected $urlFinder;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param UrlFinderInterface $urlFinder
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\State $appState,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response,
        UrlFinderInterface $urlFinder
    ) {
        $this->actionFactory = $actionFactory;
        $this->url = $url;
        $this->appState = $appState;
        $this->storeManager = $storeManager;
        $this->response = $response;
        $this->urlFinder = $urlFinder;
    }

    /**
     * Match corresponding URL Rewrite and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->appState->isInstalled()) {
            $this->response->setRedirect($this->url->getUrl('install'))->sendResponse();
            return null;
        }

        $rewrite = $this->urlFinder->findOneByData([
            UrlRewrite::REQUEST_PATH => trim($request->getPathInfo(), '/'),
            UrlRewrite::STORE_ID => $this->storeManager->getStore()->getId(),
        ]);
        if ($rewrite === null) {
            return null;
        }

        $redirectType = $rewrite->getRedirectType();
        if ($redirectType) {
            $this->response->setRedirect($rewrite->getTargetPath(), (int)$redirectType);
            $request->setDispatched(true);
            return $this->actionFactory->create('Magento\Framework\App\Action\Redirect', array('request' => $request));
        }

        $request->setPathInfo('/' . $rewrite->getTargetPath());
        return $this->actionFactory->create('Magento\Framework\App\Action\Forward', array('request' => $request));
    }
}
