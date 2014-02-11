<?php
/**
 * Customer session storage
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Layout;

/**
 * Class DepersonalizePlugin
 */
class DepersonalizePlugin
{
    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Magento\Session\SessionManagerInterface
     */
    protected $session;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var \Magento\Event\Manager
     */
    protected $eventManager;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $request;

    /**
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\Session\SessionManagerInterface $session
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Event\Manager $eventManager
     * @param \Magento\App\RequestInterface $request
     */
    public function __construct(
        \Magento\View\LayoutInterface $layout,
        \Magento\Session\SessionManagerInterface $session,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Event\Manager $eventManager,
        \Magento\App\RequestInterface $request
    ){
        $this->layout = $layout;
        $this->session = $session;
        $this->customerSession = $customerSession;
        $this->customer = $customerFactory->create();
        $this->eventManager = $eventManager;
        $this->request = $request;

    }

    /**
     * Before layout generate
     *
     * @param null $arguments
     * @return null
     */
    public function beforeGenerateXml($arguments)
    {
        if (!$this->request->isAjax() && $this->layout->isCacheable()) {
            $customerSegmentIds  = $this->customerSession->getCustomerSegmentIds();
            $customerGroupId    = $this->customerSession->getCustomerGroupId();
            $this->eventManager->dispatch('before_session_write_close');
            session_write_close();
            $this->session->clearStorage();
            $this->customerSession->clearStorage();
            $this->customerSession->setCustomerSegmentIds($customerSegmentIds);
            $this->customerSession->setCustomerGroupId($customerGroupId);
            $this->customer->setGroupId($customerGroupId);
            $this->customerSession->setCustomer($this->customer);
        }
        return $arguments;
    }
}
