<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Account;

class Create extends \Magento\Customer\Controller\Account
{
    /** @var \Magento\Customer\Helper\Data */
    protected $customerHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Helper\Data $customerHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Helper\Data $customerHelper
    ) {
        $this->customerHelper = $customerHelper;
        parent::__construct($context, $customerSession);
    }

    /**
     * Customer register form page
     *
     * @return void
     */
    public function execute()
    {
        if ($this->_getSession()->isLoggedIn() || !$this->customerHelper->isRegistrationAllowed()) {
            $this->_redirect('*/*');
            return;
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}
