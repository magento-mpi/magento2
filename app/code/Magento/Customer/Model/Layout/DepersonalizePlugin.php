<?php
/**
 * Depersonalize customer session data
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
     * @var \Magento\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var int
     */
    protected $customerGroupId;

    /**
     * @var string
     */
    protected $formKey;

    /**
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\Session\SessionManagerInterface $session
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Event\Manager $eventManager
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\View\LayoutInterface $layout,
        \Magento\Session\SessionManagerInterface $session,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Event\Manager $eventManager,
        \Magento\App\RequestInterface $request,
        \Magento\Module\Manager $moduleManager
    ) {
        $this->layout           = $layout;
        $this->session          = $session;
        $this->customerSession  = $customerSession;
        $this->customer         = $customerFactory->create();
        $this->eventManager     = $eventManager;
        $this->request          = $request;
        $this->moduleManager    = $moduleManager;

    }

    /**
     * Actions before session_write_close
     *
     * @return $this
     */
    protected function beforeSessionWriteClose()
    {
        $this->customerGroupId = $this->customerSession->getCustomerGroupId();
        $this->formKey = $this->session->getData(\Magento\Data\Form\FormKey::FORM_KEY);
        return $this;
    }

    /**
     * Actions after session_write_close
     *
     * @return $this
     */
    protected function afterSessionWriteClose()
    {
        $this->session->clearStorage();
        $this->customerSession->clearStorage();
        $this->session->setData(\Magento\Data\Form\FormKey::FORM_KEY, $this->formKey);
        $this->customerSession->setCustomerGroupId($this->customerGroupId);
        $this->customer->setGroupId($this->customerGroupId);
        $this->customerSession->setCustomer($this->customer);
        return $this;
    }

    /**
     * After layout generate
     *
     * @param mixed $arguments
     * @return mixed
     */
    public function afterGenerateXml($arguments = null)
    {
        if ($this->moduleManager->isEnabled('Magento_PageCache')
            && !$this->request->isAjax()
            && $this->layout->isCacheable()
        ) {
            $this->beforeSessionWriteClose();
            $this->eventManager->dispatch('before_session_write_close');
            session_write_close();
            $this->afterSessionWriteClose();
        }
        return $arguments;
    }
}
