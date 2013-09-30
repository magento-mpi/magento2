<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation frontend controller
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
class Magento_Invitation_Controller_Index extends Magento_Core_Controller_Front_Action
{
    /**
     * Customer Session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_session;

    /**
     * Invitation Config
     *
     * @var Magento_Invitation_Model_Config
     */
    protected $_config;

    /**
     * Invitation Factory
     *
     * @var Magento_Invitation_Model_InvitationFactory
     */
    protected $invitationFactory;

    /**
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Customer_Model_Session $session
     * @param Magento_Invitation_Model_Config $config
     * @param Magento_Invitation_Model_InvitationFactory $invitationFactory
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Customer_Model_Session $session,
        Magento_Invitation_Model_Config $config,
        Magento_Invitation_Model_InvitationFactory $invitationFactory
    ) {
        parent::__construct($context);
        $this->_session = $session;
        $this->_config = $config;
        $this->invitationFactory = $invitationFactory;
    }

    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->_config->isEnabledOnFront()) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }

        if (!$this->_session->authenticate($this)) {
            $this->getResponse()->setRedirect(
                $this->_objectManager->get('Magento_Customer_Helper_Data')->getLoginUrl()
            );
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    /**
     * Send invitations from frontend
     *
     */
    public function sendAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $customer = $this->_session->getCustomer();
            $invPerSend = $this->_config->getMaxInvitationsPerSend();
            $attempts = 0;
            $sent     = 0;
            $customerExists = 0;
            foreach ($data['email'] as $email) {
                $attempts++;
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    continue;
                }
                if ($attempts > $invPerSend) {
                    continue;
                }
                try {
                    $invitation = $this->invitationFactory->create()->setData(array(
                        'email'    => $email,
                        'customer' => $customer,
                        'message'  => (isset($data['message']) ? $data['message'] : ''),
                    ))->save();
                    if ($invitation->sendInvitationEmail()) {
                        $this->_session->addSuccess(__('You sent the invitation for %1.', $email));
                        $sent++;
                    } else {
                        throw new Exception(''); // not Magento_Core_Exception intentionally
                    }

                }
                catch (Magento_Core_Exception $e) {
                    if (Magento_Invitation_Model_Invitation::ERROR_CUSTOMER_EXISTS === $e->getCode()) {
                        $customerExists++;
                    } else {
                        $this->_session->addError($e->getMessage());
                    }
                }
                catch (Exception $e) {
                    $this->_session->addError(__('Something went wrong sending an email to %1.', $email));
                }
            }
            if ($customerExists) {
                $this->_session->addNotice(
                    __('We did not send %1 invitation(s) addressed to current customers.', $customerExists)
                );
            }
            $this->_redirect('*/*/');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        $this->loadLayoutUpdates();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Send Invitations'));
        }
        $this->renderLayout();
    }

    /**
     * View invitation list in 'My Account' section
     *
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        $this->loadLayoutUpdates();
        if ($block = $this->getLayout()->getBlock('invitations_list')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('My Invitations'));
        }
        $this->renderLayout();
    }
}
