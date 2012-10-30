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
 * Adminhtml invitation general report grid block
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Adminhtml_Report_Invitation_General_Grid extends Mage_Reports_Block_Adminhtml_Grid
{

    /**
     * Prepare report collection
     *
     * @return Enterprise_Invitation_Block_Adminhtml_Report_Invitation_General_Grid
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('Enterprise_Invitation_Model_Resource_Report_Invitation_Collection');
        return $this;
    }

    /**
     * Prepare report grid columns
     *
     * @return Enterprise_Invitation_Block_Adminhtml_Report_Invitation_General_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('sent', array(
            'header'    =>Mage::helper('Enterprise_Invitation_Helper_Data')->__('Sent'),
            'type'      =>'number',
            'index'     => 'sent'
        ));

        $this->addColumn('accepted', array(
            'header'    =>Mage::helper('Enterprise_Invitation_Helper_Data')->__('Accepted'),
            'type'      =>'number',
            'index'     => 'accepted',
            'width'     => ''
        ));

        $this->addColumn('canceled', array(
            'header'    => Mage::helper('Enterprise_Invitation_Helper_Data')->__('Discarded'),
            'type'      =>'number',
            'index'     => 'canceled',
            'width'     => ''
        ));

        $this->addColumn('accepted_rate', array(
            'header'    =>Mage::helper('Enterprise_Invitation_Helper_Data')->__('Acceptance Rate'),
            'index'     =>'accepted_rate',
            'renderer'  => 'Enterprise_Invitation_Block_Adminhtml_Grid_Column_Renderer_Percent',
            'type'      =>'string',
            'width'     => '170'

        ));

        $this->addColumn('canceled_rate', array(
            'header'    =>Mage::helper('Enterprise_Invitation_Helper_Data')->__('Discard Rate'),
            'index'     =>'canceled_rate',
            'type'      =>'number',
            'renderer'  => 'Enterprise_Invitation_Block_Adminhtml_Grid_Column_Renderer_Percent',
            'width'     => '170'
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('Enterprise_Invitation_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('Enterprise_Invitation_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
