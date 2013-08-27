<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation group id options source
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Model_Source_Invitation_GroupId
    implements Magento_Core_Model_Option_ArrayInterface

{
    /**
     * @var Magento_Customer_Model_Group
     */
    protected $_model;

    /**
     * @param Magento_Customer_Model_Group $invitationModel
     */
    public function __construct(Magento_Customer_Model_Group $invitationModel)
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
