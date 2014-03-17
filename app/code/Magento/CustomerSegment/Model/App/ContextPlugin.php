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
     * @var \Magento\CustomerSegment\Model\Customer
     */
    protected $customerSegment;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\App\Http\Context $httpContext
     * @param \Magento\CustomerSegment\Model\Customer $customerSegment
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\App\Http\Context $httpContext,
        \Magento\CustomerSegment\Model\Customer $customerSegment,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
        $this->customerSegment = $customerSegment;
        $this->storeManager = $storeManager;
    }

    /**
     * Before launch plugin
     *
     * @param \Magento\LauncherInterface $subject
     * @return void
     */
    public function beforeLaunch(\Magento\LauncherInterface $subject)
    {
        if ($this->customerSession->getCustomerId()) {
            $customerSegmentIds = $this->customerSegment->getCustomerSegmentIdsForWebsite(
                $this->customerSession->getCustomerId(),
                $this->storeManager->getWebsite()->getId()
            );
            $this->httpContext->setValue(
                \Magento\CustomerSegment\Helper\Data::CONTEXT_SEGMENT,
                $customerSegmentIds,
                array()
            );
        } else {
            $this->httpContext->setValue(
                \Magento\CustomerSegment\Helper\Data::CONTEXT_SEGMENT,
                array(),
                array()
            );
        }
    }
}
