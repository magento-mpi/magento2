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
     * @var \Magento\Session\SessionManagerInterface
     */
    protected $session;

    /**
     * @var \Magento\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\App\Request\Http
     */
    protected $httpRequest;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Session\SessionManagerInterface $session
     * @param \Magento\App\Http\Context $httpContext
     * @param \Magento\App\Request\Http $httpRequest
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Session\SessionManagerInterface $session,
        \Magento\App\Http\Context $httpContext,
        \Magento\App\Request\Http $httpRequest,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->session      = $session;
        $this->httpContext  = $httpContext;
        $this->httpRequest  = $httpRequest;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\App\Action\Action $subject
     * @param callable $proceed
     * @param \Magento\App\RequestInterface $request
     * @return mixed
     */
    public function aroundDispatch(
        \Magento\App\Action\Action $subject,
        \Closure $proceed,
        \Magento\App\RequestInterface $request
    ) {
        $this->httpContext->setValue(
            \Magento\Core\Helper\Data::CONTEXT_CURRENCY,
            $this->session->getCurrencyCode(),
            $this->storeManager->getWebsite()->getDefaultStore()->getDefaultCurrency()->getCode()
        );

        $this->httpContext->setValue(
            \Magento\Core\Helper\Data::CONTEXT_STORE,
            $this->httpRequest->getParam(
                '___store',
                $this->httpRequest->getCookie(\Magento\Store\Model\Store::COOKIE_NAME)
            ),
            $this->storeManager->getWebsite()->getDefaultStore()->getCode()
        );
        return $proceed($request);
    }
}
