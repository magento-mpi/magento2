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
namespace Magento\Invitation\Block\Customer;

class ListCustomer extends \Magento\Customer\Block\Account\Dashboard
{
    /**
     * Return list of invitations
     *
     * @return \Magento\Invitation\Model\Resource\Invitation\Collection
     */
    public function getInvitationCollection()
    {
        if (!$this->hasInvitationCollection()) {
            $this->setData('invitation_collection', \Mage::getModel('\Magento\Invitation\Model\Invitation')->getCollection()
                ->addOrder('invitation_id', \Magento\Data\Collection::SORT_ORDER_DESC)
                ->loadByCustomerId(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId())
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
        return \Mage::getSingleton('Magento\Invitation\Model\Source\Invitation\Status')
            ->getOptionText($invitation->getStatus());
    }
}
