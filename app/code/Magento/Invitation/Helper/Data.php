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
namespace Magento\Invitation\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    protected $_isRegistrationAllowed = null;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * Customer data
     *
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerData = null;

    /**
     * @param \Magento\Customer\Helper\Data $customerData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Helper\Context $context
     */
    public function __construct(
        \Magento\Customer\Helper\Data $customerData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Helper\Context $context
    ) {
        $this->_customerData = $customerData;
        $this->_coreData = $coreData;
        parent::__construct($context);
    }

    /**
     * Return text for invitation status
     *
     * @param $invitation
     * @return \Magento\Invitation\Model\Invitation
     */
    public function getInvitationStatusText($invitation)
    {
        return \Mage::getSingleton('Magento_Invitation_Model_Source_Invitation_Status')
            ->getOptionText($invitation->getStatus());
    }

    /**
     * Return invitation url
     *
     * @param \Magento\Invitation\Model\Invitation $invitation
     * @return string
     */
    public function getInvitationUrl($invitation)
    {
        return \Mage::getModel('Magento\Core\Model\Url')->setStore($invitation->getStoreId())
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
