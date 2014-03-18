<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter subscribe controller
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Newsletter\Controller;

use Magento\App\Action\Context;
use Magento\Core\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Customer\Helper\Data as CustomerHelper;

class Subscriber extends \Magento\App\Action\Action
{
    /**
     * Customer session
     *
     * @var Session
     */
    protected $_customerSession;

    /**
     * Customer Service
     *
     * @var CustomerAccountServiceInterface
     */
    protected $_customerService;

    /**
     * Subscriber factory
     *
     * @var SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var CustomerHelper
     */
    protected $_customerHelper;

    /**
     * @param Context $context
     * @param SubscriberFactory $subscriberFactory
     * @param CustomerAccountServiceInterface $customerService
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param CustomerHelper $customerHelper
     */
    public function __construct(
        Context $context,
        SubscriberFactory $subscriberFactory,
        CustomerAccountServiceInterface $customerService,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        CustomerHelper $customerHelper
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_subscriberFactory = $subscriberFactory;
        $this->_customerService = $customerService;
        $this->_customerSession = $customerSession;
        $this->_customerHelper = $customerHelper;
    }

    /**
     * New subscription action
     *
     * @throws \Magento\Core\Exception
     * @return void
     */
    public function newAction()
    {
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $email = (string) $this->getRequest()->getPost('email');

            try {
                $this->validateEmailFormat($email);
                $this->validateGuestSubscription();
                $this->validateEmailAvailable($email);

                $status = $this->_subscriberFactory->create()->subscribe($email);
                if ($status == \Magento\Newsletter\Model\Subscriber::STATUS_NOT_ACTIVE) {
                    $this->messageManager->addSuccess(__('The confirmation request has been sent.'));
                } else {
                    $this->messageManager->addSuccess(__('Thank you for your subscription.'));
                }
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addException($e, __('There was a problem with the subscription: %1',
                    $e->getMessage()));
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong with the subscription.'));
            }
        }
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
    }

    /**
     * Subscription confirm action
     * @return void
     */
    public function confirmAction()
    {
        $id    = (int) $this->getRequest()->getParam('id');
        $code  = (string) $this->getRequest()->getParam('code');

        if ($id && $code) {
            /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
            $subscriber = $this->_subscriberFactory->create()->load($id);

            if ($subscriber->getId() && $subscriber->getCode()) {
                if ($subscriber->confirm($code)) {
                    $this->messageManager->addSuccess(__('Your subscription has been confirmed.'));
                } else {
                    $this->messageManager->addError(__('This is an invalid subscription confirmation code.'));
                }
            } else {
                $this->messageManager->addError(__('This is an invalid subscription ID.'));
            }
        }

        $this->getResponse()->setRedirect($this->_storeManager->getStore()->getBaseUrl());
    }

    /**
     * Unsubscribe newsletter
     * @return void
     */
    public function unsubscribeAction()
    {
        $id    = (int) $this->getRequest()->getParam('id');
        $code  = (string) $this->getRequest()->getParam('code');

        if ($id && $code) {
            try {
                $this->_subscriberFactory->create()->load($id)
                    ->setCheckCode($code)
                    ->unsubscribe();
                $this->messageManager->addSuccess(__('You have been unsubscribed.'));
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addException($e, $e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong with the un-subscription.'));
            }
        }
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
    }

    /**
     * Validates that the email address isn't being used by a different account.
     *
     * @param $email
     * @throws \Magento\Core\Exception
     */
    protected function validateEmailAvailable($email)
    {
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        if ($this->_customerSession->getCustomerDataObject()->getEmail() !== $email
            && !$this->_customerService->isEmailAvailable($email, $websiteId)
        ) {
            throw new \Magento\Core\Exception(__('This email address is already assigned to another user.'));
        }
    }

    /**
     * Validates that if the current user is a guest, that they can subscribe to a newsletter.
     *
     * @throws \Magento\Core\Exception
     */
    protected function validateGuestSubscription()
    {
        if ($this->_objectManager->get('Magento\Core\Model\Store\Config')
                ->getConfig(\Magento\Newsletter\Model\Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1
            && !$this->_customerSession->isLoggedIn()
        ) {
            throw new \Magento\Core\Exception(__('Sorry, but the administrator denied subscription for guests. '
                . 'Please <a href="%1">register</a>.',
                $this->_customerHelper->getRegisterUrl()));
        }
    }

    /**
     * Validates the format of the email address
     *
     * @param $email
     * @throws \Magento\Core\Exception
     */
    protected function validateEmailFormat($email)
    {
        if (!\Zend_Validate::is($email, 'EmailAddress')) {
            throw new \Magento\Core\Exception(__('Please enter a valid email address.'));
        }
    }
}
