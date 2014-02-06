<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Helper;

use Magento\Invitation\Model\Invitation;

/**
 * Invitation data helper
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * @var bool
     */
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
     * @var \Magento\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Customer\Helper\Data $customerData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Invitation\Model\Source\Invitation\Status $invitationStatus
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Customer\Helper\Data $customerData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Invitation\Model\Source\Invitation\Status $invitationStatus
    ) {
        parent::__construct($context);
        $this->_customerData = $customerData;
        $this->_coreData = $coreData;
        $this->_invitationStatus = $invitationStatus;
    }

    /**
     * Return text for invitation status
     *
     * @param Invitation $invitation
     * @return Invitation
     */
    public function getInvitationStatusText($invitation)
    {
        return $this->_invitationStatus->getOptionText($invitation->getStatus());
    }

    /**
     * Return invitation url
     *
     * @param Invitation $invitation
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
     * @param bool $isAllowed
     * @return bool
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
