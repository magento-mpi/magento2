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
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getSingleton('Magento_Invitation_Model_Config')->isEnabledOnFront()) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }

        if (!Mage::getSingleton('Magento_Customer_Model_Session')->authenticate($this)) {
            $this->getResponse()->setRedirect(Mage::helper('Magento_Customer_Helper_Data')->getLoginUrl());
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
            $customer = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer();
            $invPerSend = Mage::getSingleton('Magento_Invitation_Model_Config')->getMaxInvitationsPerSend();
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
                    $invitation = Mage::getModel('Magento_Invitation_Model_Invitation')->setData(array(
                        'email'    => $email,
                        'customer' => $customer,
                        'message'  => (isset($data['message']) ? $data['message'] : ''),
                    ))->save();
                    if ($invitation->sendInvitationEmail()) {
                        Mage::getSingleton('Magento_Customer_Model_Session')->addSuccess(__('You sent the invitation for %1.', $email));
                        $sent++;
                    }
                    else {
                        throw new Exception(''); // not Magento_Core_Exception intentionally
                    }

                }
                catch (Magento_Core_Exception $e) {
                    if (Magento_Invitation_Model_Invitation::ERROR_CUSTOMER_EXISTS === $e->getCode()) {
                        $customerExists++;
                    }
                    else {
                        Mage::getSingleton('Magento_Customer_Model_Session')->addError($e->getMessage());
                    }
                }
                catch (Exception $e) {
                    Mage::getSingleton('Magento_Customer_Model_Session')->addError(__('Something went wrong sending an email to %1.', $email));
                }
            }
            if ($customerExists) {
                Mage::getSingleton('Magento_Customer_Model_Session')->addNotice(
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
