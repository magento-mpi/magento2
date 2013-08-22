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
     * Return list of invitations
     *
     * @return Magento_Invitation_Model_Resource_Invitation_Collection
     */
    public function getInvitationCollection()
    {
        if (!$this->hasInvitationCollection()) {
            $this->setData('invitation_collection', Mage::getModel('Magento_Invitation_Model_Invitation')->getCollection()
                ->addOrder('invitation_id', Magento_Data_Collection::SORT_ORDER_DESC)
                ->loadByCustomerId(Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId())
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
        return Mage::getSingleton('Magento_Invitation_Model_Source_Invitation_Status')
            ->getOptionText($invitation->getStatus());
    }
}
