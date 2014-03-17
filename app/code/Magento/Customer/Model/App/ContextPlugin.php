<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\App;

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
     * Before dispatch plugin
     *
     * @param \Magento\App\FrontController $subject
     * @return null
     */
    public function beforeDispatch(\Magento\App\FrontController $subject)
    {
        $this->httpContext->setValue(
            \Magento\Customer\Helper\Data::CONTEXT_GROUP,
            $this->customerSession->getCustomerGroupId(),
            \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID
        );
        $this->httpContext->setValue(
            \Magento\Customer\Helper\Data::CONTEXT_AUTH,
            $this->customerSession->isLoggedIn(),
            false
        );
        return;
    }
}