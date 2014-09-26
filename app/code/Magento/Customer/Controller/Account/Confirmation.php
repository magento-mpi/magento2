<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Account;

use Magento\Framework\StoreManagerInterface;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Framework\Exception\State\InvalidTransitionException;

class Confirmation extends \Magento\Customer\Controller\Account
{
    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var CustomerAccountServiceInterface  */
    protected $customerAccountService;

    /** @var \Magento\Framework\UrlInterface */
    protected $urlModel;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param CustomerAccountServiceInterface $customerAccountService
     * @param \Magento\Framework\UrlFactory $urlFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        StoreManagerInterface $storeManager,
        CustomerAccountServiceInterface $customerAccountService,
        \Magento\Framework\UrlFactory $urlFactory
    ) {
        $this->storeManager = $storeManager;
        $this->customerAccountService = $customerAccountService;
        $this->urlModel = $urlFactory->create();
        parent::__construct($context, $customerSession);
    }

    /**
     * Send confirmation link to specified email
     *
     * @return void
     */
    public function execute()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }

        // try to confirm by email
        $email = $this->getRequest()->getPost('email');
        if ($email) {
            try {
                $this->customerAccountService->resendConfirmation(
                    $email,
                    $this->storeManager->getStore()->getWebsiteId()
                );
                $this->messageManager->addSuccess(__('Please, check your email for confirmation key.'));
            } catch (InvalidTransitionException $e) {
                $this->messageManager->addSuccess(__('This email does not require confirmation.'));
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Wrong email.'));
                $this->getResponse()->setRedirect(
                    $this->urlModel->getUrl('*/*/*', array('email' => $email, '_secure' => true))
                );
                return;
            }
            $this->_getSession()->setUsername($email);
            $this->getResponse()->setRedirect($this->urlModel->getUrl('*/*/index', array('_secure' => true)));
            return;
        }

        // output form
        $this->_view->loadLayout();

        $this->_view->getLayout()->getBlock(
            'accountConfirmation'
        )->setEmail(
            $this->getRequest()->getParam('email', $email)
        );

        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}
