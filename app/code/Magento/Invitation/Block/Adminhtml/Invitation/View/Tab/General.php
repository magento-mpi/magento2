<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Block\Adminhtml\Invitation\View\Tab;

/**
 * Invitation view general tab block
 *
 */
class General extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var string
     */
    protected $_template = 'view/tab/general.phtml';

    /**
     * Invitation data
     *
     * @var \Magento\Invitation\Helper\Data
     */
    protected $_invitationData;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Customer Factory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $_customerGroupService;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Invitation\Helper\Data $invitationData
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $customerGroupService
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Invitation\Helper\Data $invitationData,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $customerGroupService,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
        $this->_invitationData = $invitationData;
        $this->_customerFactory = $customerFactory;
        $this->_customerGroupService = $customerGroupService;
    }

    /**
     * Tab label getter
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Tab Title getter
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Check whether tab can be showed
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check whether tab is hidden
     *
     * @return false
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Return Invitation for view
     *
     * @return \Magento\Invitation\Model\Invitation
     */
    public function getInvitation()
    {
        return $this->_coreRegistry->registry('current_invitation');
    }

    /**
     * Check whether it is possible to edit invitation message
     *
     * @return bool
     */
    public function canEditMessage()
    {
        return $this->getInvitation()->canMessageBeUpdated();
    }

    /**
     * Return save message button html
     *
     * @return string
     */
    public function getSaveMessageButtonHtml()
    {
        return $this->getChildHtml('save_message_button');
    }

    /**
     * Retrieve formatting date
     *
     * @param   string $date
     * @param   string $format
     * @param   bool $showTime
     * @return  string
     */
    public function formatDate($date = null, $format = 'short', $showTime = false)
    {
        if (is_string($date)) {
            $date = $this->_localeDate->date($date, \Magento\Framework\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT);
        }

        return parent::formatDate($date, $format, $showTime);
    }

    /**
     * Return invitation customer model
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getReferral()
    {
        if (!$this->hasData('referral')) {
            if ($this->getInvitation()->getReferralId()) {
                $referral = $this->_customerFactory->create()->load($this->getInvitation()->getReferralId());
            } else {
                $referral = false;
            }

            $this->setData('referral', $referral);
        }

        return $this->getData('referral');
    }

    /**
     * Return invitation customer model
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        if (!$this->hasData('customer')) {
            if ($this->getInvitation()->getCustomerId()) {
                $customer = $this->_customerFactory->create()->load($this->getInvitation()->getCustomerId());
            } else {
                $customer = false;
            }

            $this->setData('customer', $customer);
        }

        return $this->getData('customer');
    }

    /**
     * Return customer group code by group id
     * If $configUsed passed as true then result will be default string
     * instead of N/A sign
     *
     * @param int $groupId
     * @param bool $configUsed
     * @return string
     */
    public function getCustomerGroupCode($groupId, $configUsed = false)
    {
        $group = $this->_customerGroupService->getGroup($groupId);
        if ($group) {
            return $group->getCode();
        } else {
            if ($configUsed) {
                return __('Default from System Configuration');
            } else {
                return __('N/A');
            }
        }
    }

    /**
     * Invitation website name getter
     *
     * @return string
     */
    public function getWebsiteName()
    {
        return $this->_storeManager->getStore($this->getInvitation()->getStoreId())->getWebsite()->getName();
    }

    /**
     * Invitation store name getter
     *
     * @return string
     */
    public function getStoreName()
    {
        return $this->_storeManager->getStore($this->getInvitation()->getStoreId())->getName();
    }

    /**
     * Get invitation URL in case if it can be accepted
     *
     * @return string|false
     */
    public function getInvitationUrl()
    {
        if (!$this->getInvitation()->canBeAccepted(
            $this->_storeManager->getStore($this->getInvitation()->getStoreId())->getWebsiteId()
        )
        ) {
            return false;
        }
        return $this->_invitationData->getInvitationUrl($this->getInvitation());
    }

    /**
     * Checks if this invitation was sent by admin
     *
     * @return boolean - true if this invitation was sent by admin, false otherwise
     */
    public function isInvitedByAdmin()
    {
        $invitedByAdmin = $this->getInvitation()->getCustomerId() == null;
        return $invitedByAdmin;
    }

    /**
     * Check whether can show referral link
     *
     * @return bool
     */
    public function canShowReferralLink()
    {
        return $this->_authorization->isAllowed('Magento_Customer::manage');
    }
}
