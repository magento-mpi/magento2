<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule1\Controller;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;

/**
 * Controller for testing the CookieManager.
 *
 */
class CookieTester extends \Magento\Framework\App\Action\Action
{
    /** @var \Magento\Framework\App\State */
    protected $appState;

    /** @var PhpCookieManager */
    protected $cookieManager;

    /** @var  CookieMetadataFactory */
    protected $cookieMetadataFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\State $appState
     * @param PhpCookieManager $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\State $appState,
        PhpCookieManager $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->appState = $appState;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFacory = $cookieMetadataFactory;
        parent::__construct($context);
    }

    /**
     * Retrieve cookie metadata factory
     */
    protected function getCookieMetadataFactory()
    {
        return $this->cookieMetadataFacory;
    }

    /**
     * Retrieve cookie metadata factory
     */
    protected function getCookieManager()
    {
        return $this->cookieManager;
    }

    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->appState->isInstalled()) {
            parent::dispatch($request);
        }

        if (!$this->getRequest()->isDispatched()) {
            parent::dispatch($request);
        }

        $result = parent::dispatch($request);
        return $result;
    }
}
