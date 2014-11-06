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
use Magento\Customer\Model\Registration;

class Create extends \Magento\Customer\Controller\Account
{
    /** @var Registration */
    protected $registration;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param Registration $registration
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Registration $registration
    ) {
        $this->registration = $registration;
        parent::__construct($context, $customerSession);
    }

    /**
     * Customer register form page
     *
     * @return void
     */
    public function execute()
    {
        if ($this->_getSession()->isLoggedIn() || !$this->registration->isAllowed()) {
            $this->_redirect('*/*');
            return;
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}
