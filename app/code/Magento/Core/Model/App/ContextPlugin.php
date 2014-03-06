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
     * @param \Magento\Session\SessionManagerInterface $session
     * @param \Magento\App\Http\Context $httpContext
     * @param \Magento\App\Request\Http $httpRequest
     */
    public function __construct(
        \Magento\Session\SessionManagerInterface $session,
        \Magento\App\Http\Context $httpContext,
        \Magento\App\Request\Http $httpRequest
    ) {
        $this->session      = $session;
        $this->httpContext  = $httpContext;
        $this->httpRequest  = $httpRequest;
    }

    /**
     * Before launch plugin
     *
     * @param \Magento\LauncherInterface $subject
     */
    public function beforeLaunch(\Magento\LauncherInterface $subject)
    {
        $this->httpContext->setValue(\Magento\Core\Helper\Data::CONTEXT_CURRENCY,
            $this->session->getCurrencyCode());
        $this->httpContext->setValue(\Magento\Core\Helper\Data::CONTEXT_STORE,
            $this->httpRequest->getParam('___store',
                $this->httpRequest->getCookie(\Magento\Core\Model\Store::COOKIE_NAME))
            );
    }
}
