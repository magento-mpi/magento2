<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\App;

/**
 * Class ContextPlugin
 */
class ContextPlugin
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
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Session\SessionManagerInterface $session
     * @param \Magento\App\Http\Context $httpContext
     * @param \Magento\App\Request\Http $httpRequest
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Session\SessionManagerInterface $session,
        \Magento\App\Http\Context $httpContext,
        \Magento\App\Request\Http $httpRequest,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->session      = $session;
        $this->httpContext  = $httpContext;
        $this->httpRequest  = $httpRequest;
        $this->storeManager = $storeManager;
    }

    /**
     * Before dispatch plugin
     *
     * @param \Magento\App\FrontController $subject
     * @return null
     */
    public function beforeDispatch(\Magento\App\FrontController $subject)
    {
        $this->httpContext->setValue(
            \Magento\Core\Helper\Data::CONTEXT_CURRENCY,
            $this->session->getCurrencyCode(),
            $this->storeManager->getWebsite()->getDefaultStore()->getDefaultCurrency()->getCode()
        );

        $this->httpContext->setValue(
            \Magento\Core\Helper\Data::CONTEXT_STORE,
            $this->httpRequest->getParam(
                '___store',
                $this->httpRequest->getCookie(\Magento\Core\Model\Store::COOKIE_NAME)
            ),
            $this->storeManager->getWebsite()->getDefaultStore()->getCode()
        );
        return;
    }
}
