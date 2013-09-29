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
 * Invitation group id options source
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
namespace Magento\Invitation\Model\Source\Invitation;

class GroupId
    implements \Magento\Core\Model\Option\ArrayInterface

{
    /**
     * @var \Magento\Customer\Model\Group
     */
    protected $_model;

    /**
     * @param \Magento\Customer\Model\Group $invitationModel
     */
    public function __construct(\Magento\Customer\Model\Group $invitationModel)
    {
        $this->_model = $invitationModel;
    }

    /**
     * Return list of invitation statuses as options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_model->getCollection()
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();
    }
}
