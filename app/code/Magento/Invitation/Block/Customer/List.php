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
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
class Magento_Invitation_Block_Customer_List extends Magento_Customer_Block_Account_Dashboard
{
    /**
     * Invitation Factory
     *
     * @var Magento_Invitation_Model_InvitationFactory
     */
    protected $_invitationFactory;

    /**
     * Customer Session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * Invitation Status
     *
     * @var Magento_Invitation_Model_Source_Invitation_Status
     */
    protected $_invitationStatus;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Invitation_Model_InvitationFactory $invitationFactory
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Invitation_Model_Source_Invitation_Status $invitationStatus
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Invitation_Model_InvitationFactory $invitationFactory,
        Magento_Customer_Model_Session $customerSession,
        Magento_Invitation_Model_Source_Invitation_Status $invitationStatus,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_invitationFactory = $invitationFactory;
        $this->_customerSession = $customerSession;
        $this->_invitationStatus = $invitationStatus;
    }

    /**
     * Return list of invitations
     *
     * @return Magento_Invitation_Model_Resource_Invitation_Collection
     */
    public function getInvitationCollection()
    {
        if (!$this->hasInvitationCollection()) {
            $this->setData('invitation_collection', $this->_invitationFactory->create()->getCollection()
                ->addOrder('invitation_id', Magento_Data_Collection::SORT_ORDER_DESC)
                ->loadByCustomerId($this->_customerSession->getCustomerId())
            );
        }
        return $this->_getData('invitation_collection');
    }

    /**
     * Return status text for invitation
     *
     * @param Magento_Invitation_Model_Invitation $invitation
     * @return string
     */
    public function getStatusText($invitation)
    {
        return $this->_invitationStatus->getOptionText($invitation->getStatus());
    }
}
