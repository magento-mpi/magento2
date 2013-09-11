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
namespace Magento\Invitation\Controller;

class Index extends \Magento\Core\Controller\Front\Action
{
    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!\Mage::getSingleton('Magento\Invitation\Model\Config')->isEnabledOnFront()) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }

        if (!\Mage::getSingleton('Magento\Customer\Model\Session')->authenticate($this)) {
            $this->getResponse()->setRedirect(\Mage::helper('Magento\Customer\Helper\Data')->getLoginUrl());
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
            $customer = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer();
            $invPerSend = \Mage::getSingleton('Magento\Invitation\Model\Config')->getMaxInvitationsPerSend();
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
                    $invitation = \Mage::getModel('Magento\Invitation\Model\Invitation')->setData(array(
                        'email'    => $email,
                        'customer' => $customer,
                        'message'  => (isset($data['message']) ? $data['message'] : ''),
                    ))->save();
                    if ($invitation->sendInvitationEmail()) {
                        \Mage::getSingleton('Magento\Customer\Model\Session')->addSuccess(__('You sent the invitation for %1.', $email));
                        $sent++;
                    }
                    else {
                        throw new \Exception(''); // not \Magento\Core\Exception intentionally
                    }

                }
                catch (\Magento\Core\Exception $e) {
                    if (\Magento\Invitation\Model\Invitation::ERROR_CUSTOMER_EXISTS === $e->getCode()) {
                        $customerExists++;
                    }
                    else {
                        \Mage::getSingleton('Magento\Customer\Model\Session')->addError($e->getMessage());
                    }
                }
                catch (\Exception $e) {
                    \Mage::getSingleton('Magento\Customer\Model\Session')->addError(__('Something went wrong sending an email to %1.', $email));
                }
            }
            if ($customerExists) {
                \Mage::getSingleton('Magento\Customer\Model\Session')->addNotice(
                    __('We did not send %1 invitation(s) addressed to current customers.', $customerExists)
                );
            }
            $this->_redirect('*/*/');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('Magento\Customer\Model\Session');
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
        $this->_initLayoutMessages('Magento\Customer\Model\Session');
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
