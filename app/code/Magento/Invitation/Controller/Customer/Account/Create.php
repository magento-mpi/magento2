<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Controller\Customer\Account;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Model\Exception;
use Magento\Customer\Controller\Account;
use Magento\Invitation\Model\InvitationProvider;

class Create extends Account
{
    /**
     * @var InvitationProvider
     */
    protected $invitationProvider;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param InvitationProvider $invitationProvider
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        InvitationProvider $invitationProvider
    ) {
        $this->invitationProvider = $invitationProvider;
        parent::__construct(
            $context,
            $customerSession
        );
    }

    /**
     * Customer register form page
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->invitationProvider->get($this->getRequest());
            $this->_view->loadLayout();
            $this->_view->getLayout()->initMessages();
            $this->_view->renderLayout();
            return;
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('customer/account/login');
    }
}
