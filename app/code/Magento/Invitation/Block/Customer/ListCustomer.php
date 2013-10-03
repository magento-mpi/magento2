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
 * Customer invitation list block
 */
namespace Magento\Invitation\Block\Customer;

class ListCustomer extends \Magento\Customer\Block\Account\Dashboard
{
    /**
     * Invitation Factory
     *
     * @var \Magento\Invitation\Model\InvitationFactory
     */
    protected $_invitationFactory;

    /**
     * Invitation Status
     *
     * @var \Magento\Invitation\Model\Source\Invitation\Status
     */
    protected $_invitationStatus;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Invitation\Model\InvitationFactory $invitationFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Magento\Invitation\Model\Source\Invitation\Status $invitationStatus
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Invitation\Model\InvitationFactory $invitationFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Invitation\Model\Source\Invitation\Status $invitationStatus,
        array $data = array()
    ) {
        $this->_invitationFactory = $invitationFactory;
        $this->_invitationStatus = $invitationStatus;
        parent::__construct($coreData, $context, $customerSession, $subscriberFactory, $data);
    }

    /**
     * Return list of invitations
     *
     * @return \Magento\Invitation\Model\Resource\Invitation\Collection
     */
    public function getInvitationCollection()
    {
        if (!$this->hasInvitationCollection()) {
            $this->setData('invitation_collection', $this->_invitationFactory->create()->getCollection()
                ->addOrder('invitation_id', \Magento\Data\Collection::SORT_ORDER_DESC)
                ->loadByCustomerId($this->_customerSession->getCustomerId())
            );
        }
        return $this->_getData('invitation_collection');
    }

    /**
     * Return status text for invitation
     *
     * @param \Magento\Invitation\Model\Invitation $invitation
     * @return string
     */
    public function getStatusText($invitation)
    {
        return $this->_invitationStatus->getOptionText($invitation->getStatus());
    }
}
