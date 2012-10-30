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
 * Adminhtml invitation customer report grid block
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Adminhtml_Report_Invitation_Customer_Grid
    extends Mage_Reports_Block_Adminhtml_Grid
{


    /**
     * Prepare report collection
     *
     * @return Enterprise_Invitation_Block_Adminhtml_Report_Invitation_Customer_Grid
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()
            ->initReport('Enterprise_Invitation_Model_Resource_Report_Invitation_Customer_Collection');
        return $this;
    }

    /**
     * Prepare report grid columns
     *
     * @return Enterprise_Invitation_Block_Adminhtml_Report_Invitation_Customer_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    =>Mage::helper('Enterprise_Invitation_Helper_Data')->__('ID'),
            'index'     => 'entity_id'
        ));

        $this->addColumn('name', array(
            'header'    =>Mage::helper('Enterprise_Invitation_Helper_Data')->__('Name'),
            'index'     => 'name'
        ));

        $this->addColumn('email', array(
            'header'    =>Mage::helper('Enterprise_Invitation_Helper_Data')->__('Email'),
            'index'     => 'email'
        ));

        $this->addColumn('group', array(
            'header'    =>Mage::helper('Enterprise_Invitation_Helper_Data')->__('Group'),
            'index'     => 'group_name'
        ));

        $this->addColumn('sent', array(
            'header'    =>Mage::helper('Enterprise_Invitation_Helper_Data')->__('Invitations Sent'),
            'type'      =>'number',
            'index'     => 'sent'
        ));


        $this->addColumn('accepted', array(
            'header'    =>Mage::helper('Enterprise_Invitation_Helper_Data')->__('Invitations Accepted'),
            'type'      =>'number',
            'index'     => 'accepted'
        ));

        $this->addExportType('*/*/exportCustomerCsv', Mage::helper('Enterprise_Invitation_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportCustomerExcel', Mage::helper('Enterprise_Invitation_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }


}
