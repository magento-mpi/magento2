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
 * Invitation data helper
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
class Magento_Invitation_Helper_Data extends Magento_Core_Helper_Abstract
{
    protected $_isRegistrationAllowed = null;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData;

    /**
     * Customer data
     *
     * @var Magento_Customer_Helper_Data
     */
    protected $_customerData;

    /**
     * Invitation Status
     *
     * @var Magento_Invitation_Model_Source_Invitation_Status
     */
    protected $_invitationStatus;

    /**
     * Url builder
     *
     * @var Magento_Core_Model_Url
     */
    protected $_urlBuilder;

    /**
     * @param Magento_Customer_Helper_Data $customerData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Invitation_Model_Source_Invitation_Status $invitationStatus
     * @param Magento_Core_Model_Url $urlBuilder
     */
    public function __construct(
        Magento_Customer_Helper_Data $customerData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Helper_Context $context,
        Magento_Invitation_Model_Source_Invitation_Status $invitationStatus,
        Magento_Core_Model_Url $urlBuilder
    ) {
        parent::__construct($context);
        $this->_customerData = $customerData;
        $this->_coreData = $coreData;
        $this->_invitationStatus = $invitationStatus;
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * Return text for invitation status
     *
     * @param $invitation
     * @return Magento_Invitation_Model_Invitation
     */
    public function getInvitationStatusText($invitation)
    {
        return $this->_invitationStatus->getOptionText($invitation->getStatus());
    }

    /**
     * Return invitation url
     *
     * @param Magento_Invitation_Model_Invitation $invitation
     * @return string
     */
    public function getInvitationUrl($invitation)
    {
        return $this->_urlBuilder->setStore($invitation->getStoreId())
            ->getUrl('magento_invitation/customer_account/create', array(
                'invitation' => $this->_coreData->urlEncode($invitation->getInvitationCode()),
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
        return $this->_getUrl('magento_invitation/');
    }

    /**
     * Return invitation send form url
     *
     * @return string
     */
    public function getCustomerInvitationFormUrl()
    {
        return $this->_getUrl('magento_invitation/index/send');
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
            $result = $this->_customerData->isRegistrationAllowed();
            if ($this->_isRegistrationAllowed === null) {
                $this->_isRegistrationAllowed = $result;
            }
        } elseif ($isAllowed !== null) {
            $this->_isRegistrationAllowed = $isAllowed;
        }

        return $this->_isRegistrationAllowed;
    }
}
