<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Model\App;

/**
 * Class ContextPlugin
 */
class ContextPlugin
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\App\Http\Context $httpContext
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\App\Http\Context $httpContext
    ) {
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
    }

    /**
     * Before launch plugin
     *
     * @param \Magento\LauncherInterface $subject
     */
    public function beforeLaunch(\Magento\LauncherInterface $subject)
    {
        $this->httpContext->setValue(\Magento\Customer\Helper\Data::CONTEXT_GROUP,
            $this->customerSession->getCustomerGroupId());
        $this->httpContext->setValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH,
            $this->customerSession->isLoggedIn());
    }
}
