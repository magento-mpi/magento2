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
 * Adminhtml invitation orders report grid block
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Adminhtml_Report_Invitation_Order_Grid
    extends Mage_Reports_Block_Adminhtml_Grid
{

    /**
     * Prepare report collection
     *
     * @return Enterprise_Invitation_Block_Adminhtml_Report_Invitation_Order_Grid
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('Enterprise_Invitation_Model_Resource_Report_Invitation_Order_Collection');
        return $this;
    }

    /**
     * Prepare report grid columns
     *
     * @return Enterprise_Invitation_Block_Adminhtml_Report_Invitation_Order_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('sent', array(
            'header'    =>Mage::helper('Enterprise_Invitation_Helper_Data')->__('Invitations Sent'),
            'type'      =>'number',
            'index'     => 'sent',
            'width'     =>'200'
        ));

        $this->addColumn('accepted', array(
            'header'    =>Mage::helper('Enterprise_Invitation_Helper_Data')->__('Invitations Accepted'),
            'type'      =>'number',
            'index'     => 'accepted',
            'width'     =>'200'
        ));

        $this->addColumn('purchased', array(
            'header'    =>Mage::helper('Enterprise_Invitation_Helper_Data')->__('Accepted and Purchased'),
            'type'      =>'number',
            'index'     => 'purchased',
            'width'     =>'220'
        ));

        $this->addColumn('purchased_rate', array(
            'header'    =>Mage::helper('Enterprise_Invitation_Helper_Data')->__('Conversion Rate'),
            'index'     =>'purchased_rate',
            'renderer'  => 'Enterprise_Invitation_Block_Adminhtml_Grid_Column_Renderer_Percent',
            'type'      =>'string',
            'width'     =>'100'
        ));

        $this->addExportType('*/*/exportOrderCsv', Mage::helper('Enterprise_Invitation_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportOrderExcel', Mage::helper('Enterprise_Invitation_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }


}
