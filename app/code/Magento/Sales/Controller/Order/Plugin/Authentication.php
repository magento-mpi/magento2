<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Order\Plugin;

use Magento\Framework\App\RequestInterface;

class Authentication
{
    /**
     * @var \Magento\Customer\Helper\Data
     */
    protected $customerHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Magento\Customer\Helper\Data $customerHelper
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Customer\Helper\Data $customerHelper,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerHelper = $customerHelper;
        $this->customerSession = $customerSession;
    }

    /**
     * Authenticate user
     *
     * @param \Magento\Framework\App\ActionInterface $subject
     * @param RequestInterface $request
     * @return void
     */
    public function beforeDispatch(\Magento\Framework\App\ActionInterface $subject, RequestInterface $request)
    {
        $loginUrl = $this->customerHelper->getLoginUrl();

        if (!$this->customerSession->authenticate($subject, $loginUrl)) {
            $subject->getActionFlag()->set('', $subject::FLAG_NO_DISPATCH, true);
        }
    }
}
