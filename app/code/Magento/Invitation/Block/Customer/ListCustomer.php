<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Block\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\AccountManagementInterface;

/**
 * Customer invitation list block
 */
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
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $customerAccount
     * @param \Magento\Invitation\Model\InvitationFactory $invitationFactory
     * @param \Magento\Invitation\Model\Source\Invitation\Status $invitationStatus
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccount,
        \Magento\Invitation\Model\InvitationFactory $invitationFactory,
        \Magento\Invitation\Model\Source\Invitation\Status $invitationStatus,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        array $data = array()
    ) {
        $this->_invitationFactory = $invitationFactory;
        $this->_invitationStatus = $invitationStatus;
        parent::__construct(
            $context,
            $customerSession,
            $subscriberFactory,
            $customerRepository,
            $customerAccount,
            $data
        );
        $this->_isScopePrivate = true;
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * Return list of invitations
     *
     * @return \Magento\Invitation\Model\Resource\Invitation\Collection
     */
    public function getInvitationCollection()
    {
        if (!$this->hasInvitationCollection()) {
            $this->setData(
                'invitation_collection',
                $this->_invitationFactory->create()->getCollection()
                ->addOrder('invitation_id', \Magento\Framework\Data\Collection::SORT_ORDER_DESC)
                ->loadByCustomerId($this->currentCustomer->getCustomerId())
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
