<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect AirMail message queue grid
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Queue_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Constructor
     *
     * Setting grid_id, sort order and sort direction
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('app_queue_grid');
        $this->setDefaultSort('exec_time');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Setting collection to show
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('xmlconnect/queue')->getCollection();

        $collection->addFieldToFilter('main_table.status', array('neq' => Mage_XmlConnect_Model_Queue::STATUS_DELETED));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('xmlconnect')->__('ID'),
            'align'     => 'center',
            'index'     => 'main_table.id',
            'width'     => '40px',
            'renderer'  => 'xmlconnect/adminhtml_queue_grid_renderer_id'
        ));

        $this->addColumn('exec_time', array(
            'header'    => Mage::helper('xmlconnect')->__('Queue Date'),
            'index'     => 'exec_time',
            'type'      => 'datetime',
            'gmtoffset' => false,
            'default'   => ' ---- '
        ));

        $this->addColumn('app_code', array(
            'header'    => Mage::helper('xmlconnect')->__('Application Name'),
            'align'     => 'left',
            'index'     => 'main_table.app_code',
            'type'      => 'options',
            'options'   => Mage::helper('xmlconnect')->getApplications(),
            'renderer'  => 'xmlconnect/adminhtml_queue_grid_renderer_application'
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('xmlconnect')->__('Template Name'),
            'align'     => 'left',
            'index'     => 't.name',
            'type'      => 'text',
            'default'   => '--- Parent template has been deleted ---',
            'renderer'  => 'xmlconnect/adminhtml_queue_grid_renderer_template'
        ));

        $this->addColumn('push_title', array(
            'header'    => Mage::helper('xmlconnect')->__('Push Title'),
            'align'     => 'left',
            'index'     => 'main_table.push_title',
            'type'      => 'text',
            'renderer'  => 'xmlconnect/adminhtml_queue_grid_renderer_pushtitle'
        ));

        $this->addColumn('message_title', array(
            'header'    => Mage::helper('xmlconnect')->__('Message Title'),
            'align'     => 'left',
            'index'     => 'main_table.message_title',
            'type'      => 'text',
            'renderer'  => 'xmlconnect/adminhtml_queue_grid_renderer_msgtitle'
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('xmlconnect')->__('Status'),
            'align'     => 'left',
            'index'     => 'main_table.status',
            'type'      => 'options',
            'width'     => '50px',
            'options'   => array(
                Mage_XmlConnect_Model_Queue::STATUS_CANCELED => Mage::helper('xmlconnect')->__('Canceled'),
                Mage_XmlConnect_Model_Queue::STATUS_IN_QUEUE => Mage::helper('xmlconnect')->__('In Queue'),
                Mage_XmlConnect_Model_Queue::STATUS_COMPLETED => Mage::helper('xmlconnect')->__('Completed'),
            ),
            'renderer'  => 'xmlconnect/adminhtml_queue_grid_renderer_status',
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('xmlconnect')->__('Action'),
            'type'      => 'action',
            'getter'    => 'getId',
            'renderer'  => 'xmlconnect/adminhtml_queue_grid_renderer_action',

            'filter'    => false,
            'sortable'  => false,
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare mass actions
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Queue_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('queue');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('xmlconnect')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDeleteQueue'),
             'confirm'  => Mage::helper('xmlconnect')->__('Are you sure you what to delete selected records?')
        ));

        $this->getMassactionBlock()->addItem('cancel', array(
             'label'    => Mage::helper('xmlconnect')->__('Cancel'),
             'url'      => $this->getUrl('*/*/massCancelQueue'),
             'confirm'  => Mage::helper('xmlconnect')->__('Are you sure you what to cancel selected records?')
        ));
        return $this;
    }

    /**
     * Configure row click url
     *
     * @param Mage_Catalog_Model_Queue|Varien_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/editQueue', array('id' => $row->getId()));
    }
}
