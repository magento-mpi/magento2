<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation data helper
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Helper_Data extends Magento_Core_Helper_Abstract
{
    protected $_isRegistrationAllowed = null;

    /**
     * Return text for invetation status
     *
     * @return Enterprise_Invitation_Model_Invitation $invitation
     * @return string
     */
    public function getInvitationStatusText($invitation)
    {
        return Mage::getSingleton('Enterprise_Invitation_Model_Source_Invitation_Status')->getOptionText($invitation->getStatus());
    }

    /**
     * Return invitation url
     *
     * @param Enterprise_Invitation_Model_Invitation $invitation
     * @return string
     */
    public function getInvitationUrl($invitation)
    {
        return Mage::getModel('Magento_Core_Model_Url')->setStore($invitation->getStoreId())
            ->getUrl('enterprise_invitation/customer_account/create', array(
                'invitation' => Mage::helper('Magento_Core_Helper_Data')->urlEncode($invitation->getInvitationCode()),
                '_store_to_url' => true,
                '_nosid' => true
            ));
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
            $result = Mage::helper('Magento_Customer_Helper_Data')->isRegistrationAllowed();
            if ($this->_isRegistrationAllowed === null) {
                $this->_isRegistrationAllowed = $result;
            }
        } elseif ($isAllowed !== null) {
            $this->_isRegistrationAllowed = $isAllowed;
        }

        return $this->_isRegistrationAllowed;
    }
}
