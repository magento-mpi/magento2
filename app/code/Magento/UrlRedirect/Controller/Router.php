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
class Router implements \Magento\Framework\App\RouterInterface
{
    /** @var \Magento\Framework\StoreManagerInterface */
    protected $storeManager;

    /** @var \Magento\Framework\App\ResponseInterface */
    protected $response;

    /** @var \Magento\UrlRedirect\Service\V1\UrlMatcherInterface */
    protected $urlMatcher;

    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Magento\UrlRedirect\Service\V1\UrlMatcherInterface $urlMatcher
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\UrlRedirect\Service\V1\UrlMatcherInterface $urlMatcher
    ) {
        $this->actionFactory = $actionFactory;
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
        $identifier = trim($request->getPathInfo(), '/');
        $urlRewrite = $this->urlMatcher->match($identifier, $this->storeManager->getStore()->getId());
        if ($urlRewrite === null) {
            return null;
        }

        $redirectType = $urlRewrite->getRedirectType();
        if ($redirectType) {
            $redirectCode = $redirectType == \Magento\UrlRedirect\Model\OptionProvider::PERMANENT ? 301 : 302;
            $this->response->setRedirect($urlRewrite->getTargetPath(), $redirectCode);
            $request->setDispatched(true);
            return $this->actionFactory->createController(
                'Magento\Framework\App\Action\Redirect',
                array('request' => $request)
            );
        }

        $request->setPathInfo('/' . $urlRewrite->getTargetPath());
        return $this->actionFactory->createController(
            'Magento\Framework\App\Action\Forward',
            array('request' => $request)
        );
    }
}
