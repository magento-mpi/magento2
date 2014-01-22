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

class Data extends \Magento\App\Helper\AbstractHelper
{
    protected $_isRegistrationAllowed = null;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * Customer data
     *
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerData;

    /**
     * Invitation Status
     *
     * @var \Magento\Invitation\Model\Source\Invitation\Status
     */
    protected $_invitationStatus;

    /**
     * Url builder
     *
     * @var \Magento\Core\Model\Url
     */
    protected $_urlBuilder;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Customer\Helper\Data $customerData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Invitation\Model\Source\Invitation\Status $invitationStatus
     * @param \Magento\Core\Model\Url $urlBuilder
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Customer\Helper\Data $customerData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Invitation\Model\Source\Invitation\Status $invitationStatus,
        \Magento\Core\Model\Url $urlBuilder
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
     * @return \Magento\Invitation\Model\Invitation
     */
    public function getInvitationStatusText($invitation)
    {
        return $this->_invitationStatus->getOptionText($invitation->getStatus());
    }

    /**
     * Return invitation url
     *
     * @param \Magento\Invitation\Model\Invitation $invitation
     * @return string
     */
    public function getInvitationUrl($invitation)
    {
        return $this->_urlBuilder->setScope($invitation->getStoreId())
            ->getUrl('magento_invitation/customer_account/create', array(
                'invitation' => $this->_coreData->urlEncode($invitation->getInvitationCode()),
                '_scope_to_url' => true,
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
