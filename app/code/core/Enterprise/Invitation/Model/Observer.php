<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Invitation data model
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Model_Observer
{
    /**
     * Flag that indicates customer registration page
     *
     * @var boolean
     */
    protected $_flagInCustomerRegistration = false;

    /**
     * Observe customer registration for invitations
     *
     * @return void
     */
    public function observeCustomerRegistration(Varien_Event_Observer $observer)
    {
        $action = $observer->getEvent()->getControllerAction();

        $invitationCode = $action->getRequest()->getParam('enterprise_invitation', false);
        if (Mage::helper('enterprise_invitation')->getInvitationRequired() && !$invitationCode) {
            $action->setFlag('', 'no-dispatch', true);
            $action->getResponse()->setRedirect(Mage::getUrl('customer/account/login'));
            return;
        }
/*
        if ($invitationCode &&
            ! Mage::helper('enterprise_invitation')->getInvitationRequired()) {
            Mage::getSingleton('customer/session')
                ->addNotice(Mage::helper('enterprise_invitation')->__(
                    'You are creating account with invitation. You can create account <a href="%s">without it</a>.',
                    Mage::getUrl('customer/account/create')
                ));

        }
*/
    }

    /**
     * Update invitation status after customer account was created with invitation
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function observeCustomerSaveAfter(Varien_Event_Observer $observer)
    {
        $invitationCode = Mage::getSingleton('customer/session')->getInvitationCode();
        $invitation = Mage::getModel('enterprise_invitation/invitation')->loadByInvitationCode($invitationCode);
        $referralId = $observer->getEvent()->getCustomer()->getId();

        if ($invitation->getId() && $this->_flagInCustomerRegistration) {
            $this->_flagInCustomerRegistration = false;
            $now = Mage::app()->getLocale()->date()
                    ->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE)
                    ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

            $invitation->setReferralId($referralId)
                ->setSignupDate($now)
                ->setStatus(Enterprise_Invitation_Model_Invitation::STATUS_ACCEPTED)
                ->save();

            Mage::getSingleton('customer/session')->unsInvitationCode();
        } else {
            Mage::getSingleton('customer/session')->unsInvitationCode();
        }
    }

    /**
     * Check invitation behaviors before customer account was created with invitation
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function observeCustomerSaveBefore(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        /* @var $customer Mage_Customer_Model_Customer */
        if (!$customer->getId()) {
            $this->_flagInCustomerRegistration = true;
            $invitationCode = Mage::getSingleton('customer/session')->getInvitationCode();
            if (empty($invitationCode) && Mage::helper('enterprise_invitation')->getInvitationRequired()) {
                Mage::throwException(Mage::helper('enterprise_invitation')->__('Registration only by invitation'));
            }
            $invitation = Mage::getModel('enterprise_invitation/invitation')->loadByInvitationCode($invitationCode);
            if (!$invitation->getId() &&
                Mage::helper('enterprise_invitation')->getInvitationRequired()) {
                Mage::throwException(Mage::helper('enterprise_invitation')->__('Invalid invitation link'));
            }

            if ($invitation->getId() && $invitation->getGroupId()) {
                $customer->setGroupId($invitation->getGroupId());
            }
        }
    }
}