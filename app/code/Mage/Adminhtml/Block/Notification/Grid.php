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
 * Adminhtml AdminNotification inbox grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Notification_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        parent::_construct();
        $this->setSaveParametersInSession(true);
        $this->setId('notificationGrid');
        $this->setIdFieldName('notification_id');
        $this->setDefaultSort('date_added', 'desc');
        $this->setFilterVisibility(false);
    }

    /**
     * Init backups collection
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Mage_AdminNotification_Model_Inbox')
            ->getCollection()
            ->addRemoveFilter();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('severity', array(
            'header'    => Mage::helper('Mage_AdminNotification_Helper_Data')->__('Severity'),
            'width'     => '60px',
            'index'     => 'severity',
            'renderer'  => 'Mage_Adminhtml_Block_Notification_Grid_Renderer_Severity',
        ));

        $this->addColumn('date_added', array(
            'header'    => Mage::helper('Mage_AdminNotification_Helper_Data')->__('Date Added'),
            'index'     => 'date_added',
            'width'     => '150px',
            'type'      => 'datetime'
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('Mage_AdminNotification_Helper_Data')->__('Message'),
            'index'     => 'title',
            'renderer'  => 'Mage_Adminhtml_Block_Notification_Grid_Renderer_Notice',
        ));

        $this->addColumn('actions', array(
            'header'    => Mage::helper('Mage_AdminNotification_Helper_Data')->__('Actions'),
            'width'     => '250px',
            'sortable'  => false,
            'renderer'  => 'Mage_Adminhtml_Block_Notification_Grid_Renderer_Actions',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare mass action
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('notification_id');
        $this->getMassactionBlock()->setFormFieldName('notification');

        $this->getMassactionBlock()->addItem('mark_as_read', array(
             'label'    => Mage::helper('Mage_AdminNotification_Helper_Data')->__('Mark as Read'),
             'url'      => $this->getUrl('*/*/massMarkAsRead', array('_current'=>true)),
        ));

        $this->getMassactionBlock()->addItem('remove', array(
             'label'    => Mage::helper('Mage_AdminNotification_Helper_Data')->__('Remove'),
             'url'      => $this->getUrl('*/*/massRemove'),
             'confirm'  => Mage::helper('Mage_AdminNotification_Helper_Data')->__('Are you sure?')
        ));

//        $this->getColumn('massaction')->setWidth('30px');

        return $this;
    }

    public function getRowClass(Varien_Object $row) {
        return $row->getIsRead() ? 'read' : 'unread';
    }

    public function getRowClickCallback()
    {
        return false;
    }
}
