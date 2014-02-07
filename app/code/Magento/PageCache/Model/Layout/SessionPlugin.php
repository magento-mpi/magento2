<?php
/**
 * Layout session plugin
 *
 * Disable session if page is public
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Model\Layout;

/**
 * Class SessionPlugin
 */
class SessionPlugin
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
     * @param $arguments
     * @return mixed
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
