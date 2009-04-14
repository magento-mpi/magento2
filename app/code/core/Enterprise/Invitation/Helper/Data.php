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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Invitation data helper
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'enterprise_invitation/general/enabled';

    const XML_PATH_USE_INVITATION_MESSAGE = 'enterprise_invitation/general/allow_customer_message';
    const XML_PATH_MAX_INVITATION_AMOUNT_PER_SEND = 'enterprise_invitation/general/max_invitation_amount_per_send';

    const XML_PATH_REGISTRATION_REQUIRED_INVITATION = 'enterprise_invitation/general/registration_required_invitation';
    const XML_PATH_REGISTRATION_USE_INVITER_GROUP = 'enterprise_invitation/general/registration_use_inviter_group';

    protected $_isRegistrationAllowed = null;

    /**
     * Return max inventation amount per send by config
     *
     * @return int
     */
    public function getMaxInvitationAmountPerSend()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_MAX_INVITATION_AMOUNT_PER_SEND);
    }

    /**
     * Return config value for required cutomer registration by invitation
     *
     * @return boolean
     */
    public function getInvitationRequired()
    {
        return Mage::getStoreConfig(self::XML_PATH_REGISTRATION_REQUIRED_INVITATION);
    }


    /**
     * Return config value for use same group as inviter
     *
     * @return boolean
     */
    public function getUseInviterGroup()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_REGISTRATION_USE_INVITER_GROUP);
    }

    /**
     * Return config value for use same group as inviter
     *
     * @return boolean
     */
    public function getUseInvitationMessage()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_USE_INVITATION_MESSAGE);
    }

    /**
     * Get customer session
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Return text for invetation status
     *
     * @return Enterprise_Invitation_Model_Invitation $invitation
     * @return string
     */
    public function getInvitationStatusText($invitation)
    {
        return Mage::getSingleton('enterprise_invitation/source_invitation_status')->getOptionText($invitation->getStatus());
    }

    /**
     * Return invitation url
     *
     * @param Enterprise_Invitation_Model_Invitation $invitation
     * @return string
     */
    public function getInvitationUrl($invitation)
    {
        $params = array(
            'invitation' => $invitation->getInvitationCode()
        );

        $urlModel = Mage::getModel('core/url');
        $urlModel->setStore($invitation->getStoreId());

        return $urlModel->getUrl('enterprise_invitation/customer_account/create', $params);
    }

    /**
     * Return account dashboard invitation url
     *
     * @return string
     */
    public function getCustomerInvitationUrl()
    {
        return $this->_getUrl('enterprise_invitation/');
    }


    /**
     * Return invitation send form url
     *
     * @return string
     */
    public function getCustomerInvitationFormUrl()
    {
        return $this->_getUrl('enterprise_invitation/index/send');
    }

    /**
     * Checks is allowed registration in invitation controller
     *
     * @param boolean $isAllowed
     * @return boolean
     */
    public function isRegistrationAllowed($isAllowed = null)
    {
        if ($isAllowed === null && $this->_isRegistrationAllowed === null) {
            $result = Mage::helper('customer')->isRegistrationAllowed();
            if ($this->_isRegistrationAllowed === null) {
                $this->_isRegistrationAllowed = $result;
            }
        } elseif ($isAllowed !== null) {
            $this->_isRegistrationAllowed = $isAllowed;
        }

        return $this->_isRegistrationAllowed;
    }

    /**
     * Retrieve configuration for availability of invitations
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED);
    }

}