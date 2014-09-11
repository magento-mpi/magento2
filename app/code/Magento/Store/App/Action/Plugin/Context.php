<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\App\Action\Plugin;

/**
 * Class ContextPlugin
 */
class Context
{
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $session;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $httpRequest;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\Session\SessionManagerInterface $session
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Framework\App\Request\Http $httpRequest
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\App\Request\Http $httpRequest,
        \Magento\Framework\StoreManagerInterface $storeManager
    ) {
        $this->session      = $session;
        $this->httpContext  = $httpContext;
        $this->httpRequest  = $httpRequest;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\App\Action\Action $subject
     * @param callable $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     * @return mixed
     */
    public function aroundDispatch(
        \Magento\Framework\App\Action\Action $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $defaultStore = $this->storeManager->getWebsite()->getDefaultStore();
        $this->httpContext->setValue(
            \Magento\Core\Helper\Data::CONTEXT_CURRENCY,
            $this->session->getCurrencyCode(),
            $defaultStore->getDefaultCurrency()->getCode()
        );

        $this->httpContext->setValue(
            \Magento\Core\Helper\Data::CONTEXT_STORE,
            $this->httpRequest->getParam(
                '___store',
                $defaultStore->getStoreCodeFromCookie()
            ),
            $this->storeManager->getWebsite()->getDefaultStore()->getCode()
        );
        return $proceed($request);
    }
}
