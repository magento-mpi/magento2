<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Enterprise
 * @package    Enterprise_Logging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class Enterprise_Logging_Block_Events_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('enterpriseLoggerEventsGrid');
        $this->setDefaultSort('time');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

        $this->setTemplate('enterprise/logging/events/grid.phtml');

        $this->setRowClickCallback('importFileRowClick');
        $this->setColumnRenderers(
            array(
                'eventlabel' => 'enterprise_logging/events_grid_renderer_eventlabel'
            ));
    }

    /**
     * PrepareCollection method.
     */

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('enterprise_logging/event_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Return grids url
     */
    public function getGridUrl()
    {
         return $this->getUrl('adminhtml/events/grid', array('_current'=>true));
    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('time', array(
            'header'    => 'Time',
            'index'     => 'time',
            'type'      => 'datetime',
        ));

        $this->addColumn('ip', array(
            'header'    => 'IP',
            'index'     => 'ip',
            'type'      => 'text', 
            'filter'    => 'enterprise_logging/events_grid_filter_ip',
            'sortable'  => false
        ));

        $this->addColumn('user', array(
            'header'    => 'User',
            'index'     => 'user',
            'type'      => 'text',
            'sortable'  => false,
            'filter'    => 'enterprise_logging/events_grid_filter_user',
        ));

        $this->addColumn('event', array(
            'header'    => 'Event',
            'index'     => 'event_code',
            'type'      => 'eventlabel',
            'sortable'  => false,
            'filter'    => 'enterprise_logging/events_grid_filter_event',
        ));

        $this->addColumn('action', array(
            'header'    => 'Action',
            'index'     => 'action',
            'type'      => 'text',
            'filter'    => 'enterprise_logging/events_grid_filter_action',
            'sortable'  => false,
        ));

        $this->addColumn('status', array(
            'header'    => 'Status',
            'index'     => 'status',
            'sortable'  => false,
            'type'      => 'text',
            'filter'    => 'enterprise_logging/events_grid_filter_status',
                         ));

        $this->addColumn('fullaction', array(
            'header' => 'Action path',
            'index'  => 'fullaction',
            'sortable' => false,
            'type'  => 'text'
        ));

        $this->addColumn('info', array(
            'header'    => 'Item_id',
            'index'     => 'info',
            'type'      => 'text',
            'sortable'  => false,
            'filter'    => false
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('enterprise_logging')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('enterprise_logging')->__('XML'));
        return $this;
    }
}
