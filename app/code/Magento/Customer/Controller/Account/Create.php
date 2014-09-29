<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Helper\Data as CustomerHelper;

class Create extends \Magento\Customer\Controller\Account
{
    /** @var CustomerHelper */
    protected $customerHelper;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param CustomerHelper $customerHelper
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerHelper $customerHelper
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
