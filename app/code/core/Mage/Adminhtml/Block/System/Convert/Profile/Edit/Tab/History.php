<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Convert profile edit tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tab_History extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('history_grid');
        $this->setDefaultSort('performed_at', 'desc');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Mage_Dataflow_Model_Resource_Profile_History_Collection')
            ->joinAdminUser()
            ->addFieldToFilter('profile_id', Mage::registry('current_convert_profile')->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('action_code', array(
            'header'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Profile Action'),
            'index'     => 'action_code',
            'filter'    => 'Mage_Adminhtml_Block_System_Convert_Profile_Edit_Filter_Action',
            'renderer'  => 'Mage_Adminhtml_Block_System_Convert_Profile_Edit_Renderer_Action',
        ));

        $this->addColumn('performed_at', array(
            'header'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Performed At'),
            'type'      => 'datetime',
            'index'     => 'performed_at',
            'width'     => '150px',
        ));

        $this->addColumn('firstname', array(
            'header'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('First Name'),
            'index'     => 'firstname',
        ));

        $this->addColumn('lastname', array(
            'header'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Last Name'),
            'index'     => 'lastname',
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/history', array('_current' => true));
    }
}
