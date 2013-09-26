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
class Magento_Newsletter_Controller_Subscriber extends Magento_Core_Controller_Front_Action
{
    /**
     * Session
     *
     * @var Magento_Core_Model_Session
     */
    protected $_session;

    /**
     * Customer session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Customer factory
     *
     * @var Magento_Customer_Model_CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Subscriber factory
     *
     * @var Magento_Newsletter_Model_SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Newsletter_Model_SubscriberFactory $subscriberFactory
     * @param Magento_Customer_Model_CustomerFactory $customerFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Model_Session $session
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Newsletter_Model_SubscriberFactory $subscriberFactory,
        Magento_Customer_Model_CustomerFactory $customerFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Model_Session $session
    ) {
        parent::__construct($context);
        $this->_subscriberFactory = $subscriberFactory;
        $this->_customerFactory = $customerFactory;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_session = $session;
    }

    /**
     * New subscription action
     *
     * @throws Magento_Core_Exception
     */
    public function newAction()
    {
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $email = (string) $this->getRequest()->getPost('email');

            try {
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    throw new Magento_Core_Exception(__('Please enter a valid email address.'));
                }

                if ($this->_objectManager->get('Magento_Core_Model_Store_Config')
                        ->getConfig(Magento_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1
                    && !$this->_customerSession->isLoggedIn()) {
                    throw new Magento_Core_Exception(__('Sorry, but the administrator denied subscription for guests. '
                        . 'Please <a href="%1">register</a>.',
                        $this->_objectManager->get('Magento_Customer_Helper_Data')->getRegisterUrl()));
                }

                $ownerId = $this->_customerFactory->create()
                        ->setWebsiteId($this->_storeManager->getStore()->getWebsiteId())
                        ->loadByEmail($email)
                        ->getId();
                if ($ownerId !== null && $ownerId != $this->_customerSession->getId()) {
                    throw new Magento_Core_Exception(__('This email address is already assigned to another user.'));
                }

                $status = $this->_subscriberFactory->create()->subscribe($email);
                if ($status == Magento_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                    $this->_session->addSuccess(__('The confirmation request has been sent.'));
                } else {
                    $this->_session->addSuccess(__('Thank you for your subscription.'));
                }
            }
            catch (Magento_Core_Exception $e) {
                $this->_session->addException($e, __('There was a problem with the subscription: %1',
                    $e->getMessage()));
            }
            catch (Exception $e) {
                $this->_session->addException($e, __('Something went wrong with the subscription.'));
            }
        }
        $this->_redirectReferer();
    }

    /**
     * Subscription confirm action
     */
    public function confirmAction()
    {
        $id    = (int) $this->getRequest()->getParam('id');
        $code  = (string) $this->getRequest()->getParam('code');

        if ($id && $code) {
            /** @var Magento_Newsletter_Model_Subscriber $subscriber */
            $subscriber = $this->_subscriberFactory->create()->load($id);

            if ($subscriber->getId() && $subscriber->getCode()) {
                if ($subscriber->confirm($code)) {
                    $this->_session->addSuccess(__('Your subscription has been confirmed.'));
                } else {
                    $this->_session->addError(__('This is an invalid subscription confirmation code.'));
                }
            } else {
                $this->_session->addError(__('This is an invalid subscription ID.'));
            }
        }

        $this->_redirectUrl($this->_storeManager->getStore()->getBaseUrl());
    }

    /**
     * Unsubscribe newsletter
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
                $this->_session->addSuccess(__('You have been unsubscribed.'));
            }
            catch (Magento_Core_Exception $e) {
                $this->_session->addException($e, $e->getMessage());
            }
            catch (Exception $e) {
                $this->_session->addException($e, __('Something went wrong with the un-subscription.'));
            }
        }
        $this->_redirectReferer();
    }
}
