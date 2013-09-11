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

class Subscriber extends \Magento\Core\Controller\Front\Action
{
    /**
      * New subscription action
      */
    public function newAction()
    {
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $session            = \Mage::getSingleton('Magento\Core\Model\Session');
            $customerSession    = \Mage::getSingleton('Magento\Customer\Model\Session');
            $email              = (string) $this->getRequest()->getPost('email');

            try {
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    \Mage::throwException(__('Please enter a valid email address.'));
                }

                if (\Mage::getStoreConfig(\Magento\Newsletter\Model\Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 && 
                    !$customerSession->isLoggedIn()) {
                    \Mage::throwException(__('Sorry, but the administrator denied subscription for guests. Please <a href="%1">register</a>.', \Mage::helper('Magento\Customer\Helper\Data')->getRegisterUrl()));
                }

                $ownerId = \Mage::getModel('\Magento\Customer\Model\Customer')
                        ->setWebsiteId(\Mage::app()->getStore()->getWebsiteId())
                        ->loadByEmail($email)
                        ->getId();
                if ($ownerId !== null && $ownerId != $customerSession->getId()) {
                    \Mage::throwException(__('This email address is already assigned to another user.'));
                }

                $status = \Mage::getModel('\Magento\Newsletter\Model\Subscriber')->subscribe($email);
                if ($status == \Magento\Newsletter\Model\Subscriber::STATUS_NOT_ACTIVE) {
                    $session->addSuccess(__('The confirmation request has been sent.'));
                }
                else {
                    $session->addSuccess(__('Thank you for your subscription.'));
                }
            }
            catch (\Magento\Core\Exception $e) {
                $session->addException($e, __('There was a problem with the subscription: %1', $e->getMessage()));
            }
            catch (\Exception $e) {
                $session->addException($e, __('Something went wrong with the subscription.'));
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
            $subscriber = \Mage::getModel('\Magento\Newsletter\Model\Subscriber')->load($id);
            $session = \Mage::getSingleton('Magento\Core\Model\Session');

            if($subscriber->getId() && $subscriber->getCode()) {
                if($subscriber->confirm($code)) {
                    $session->addSuccess(__('Your subscription has been confirmed.'));
                } else {
                    $session->addError(__('This is an invalid subscription confirmation code.'));
                }
            } else {
                $session->addError(__('This is an invalid subscription ID.'));
            }
        }

        $this->_redirectUrl(\Mage::getBaseUrl());
    }

    /**
     * Unsubscribe newsletter
     */
    public function unsubscribeAction()
    {
        $id    = (int) $this->getRequest()->getParam('id');
        $code  = (string) $this->getRequest()->getParam('code');

        if ($id && $code) {
            $session = \Mage::getSingleton('Magento\Core\Model\Session');
            try {
                \Mage::getModel('\Magento\Newsletter\Model\Subscriber')->load($id)
                    ->setCheckCode($code)
                    ->unsubscribe();
                $session->addSuccess(__('You have been unsubscribed.'));
            }
            catch (\Magento\Core\Exception $e) {
                $session->addException($e, $e->getMessage());
            }
            catch (\Exception $e) {
                $session->addException($e, __('Something went wrong with the un-subscription.'));
            }
        }
        $this->_redirectReferer();
    }
}
