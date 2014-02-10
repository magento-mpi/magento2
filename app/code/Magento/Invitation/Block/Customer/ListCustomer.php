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

use Magento\Customer\Service\V1\CustomerServiceInterface;
use Magento\Customer\Service\V1\CustomerAddressServiceInterface;

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
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param CustomerServiceInterface $customerService
     * @param CustomerAddressServiceInterface $addressService
     * @param \Magento\Invitation\Model\InvitationFactory $invitationFactory
     * @param \Magento\Invitation\Model\Source\Invitation\Status $invitationStatus
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerServiceInterface $customerService,
        CustomerAddressServiceInterface $addressService,
        \Magento\Invitation\Model\InvitationFactory $invitationFactory,
        \Magento\Invitation\Model\Source\Invitation\Status $invitationStatus,
        array $data = array()
    ) {
        $this->_invitationFactory = $invitationFactory;
        $this->_invitationStatus = $invitationStatus;
        parent::__construct(
            $context, $customerSession, $subscriberFactory, $customerService, $addressService, $data
        );
        $this->_isScopePrivate = true;
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
