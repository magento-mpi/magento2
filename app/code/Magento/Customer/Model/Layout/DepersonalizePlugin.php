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
     * @var \Magento\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Log\Model\Visitor
     */
    protected $visitor;

    /**
     * @var int
     */
    protected $customerGroupId;

    /**
     * @var string
     */
    protected $formKey;

    /**
     * @param \Magento\Session\SessionManagerInterface $session
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Module\Manager $moduleManager
     * @param \Magento\Log\Model\Visitor $visitor
     */
    public function __construct(
        \Magento\Session\SessionManagerInterface $session,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\App\RequestInterface $request,
        \Magento\Module\Manager $moduleManager,
        \Magento\Log\Model\Visitor $visitor
    ) {
        $this->session          = $session;
        $this->customerSession  = $customerSession;
        $this->customer         = $customerFactory->create();
        $this->request          = $request;
        $this->moduleManager    = $moduleManager;
        $this->visitor          = $visitor;
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
     * Before generate Xml
     *
     * @param \Magento\Core\Model\Layout $subject
     * @return array
     */
    public function beforeGenerateXml(\Magento\Core\Model\Layout $subject)
    {
        if ($this->moduleManager->isEnabled('Magento_PageCache')
            && !$this->request->isAjax()
            && $subject->isCacheable()
        ) {
            $this->beforeSessionWriteClose();
        }
        return array();
    }
    
    /**
     * Actions after session_write_close
     *
     * @return $this
     */
    protected function afterSessionWriteClose()
    {
        $this->visitor->setSkipRequestLogging(true);
        $this->visitor->unsetData();
        $this->session->clearStorage();
        $this->customerSession->clearStorage();
        $this->session->setData(\Magento\Data\Form\FormKey::FORM_KEY, $this->formKey);
        $this->customerSession->setCustomerGroupId($this->customerGroupId);
        $this->customer->setGroupId($this->customerGroupId);
        $this->customerSession->setCustomer($this->customer);
        return $this;
    }

    /**
     * After generate Xml
     *
     * @param \Magento\Core\Model\Layout $subject
     * @param mixed result
     * @return mixed
     */
    public function afterGenerateXml(\Magento\Core\Model\Layout $subject, $result)
    {
        if ($this->moduleManager->isEnabled('Magento_PageCache')
            && !$this->request->isAjax()
            && $subject->isCacheable()
        ) {
            $this->afterSessionWriteClose();
        }
        return $result;
    }
}
