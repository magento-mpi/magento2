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
 * @category   Mage
 * @package    Mage_Import
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Import Tool Grid
 *
 * Hide Show in central select for non-top categories
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Logging_Block_Events_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('enterpriseLoggerEventsGrid');
        $this->setDefaultSort('date');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);

        /*
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        */

        $this->setTemplate('enterprise/logging/events/grid.phtml');

        $this->setRowClickCallback('importFileRowClick');
	//$this->setRowInitCallback('importRowsInit');
        //$this->setCheckboxCheckCallback('registerFile');
    }


    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('logging/event_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function getGridUrl()
    {
        return $this->getUrl('logging/events/grid', array('_current'=>true));
    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('time', array(
            'header'    => 'time',
            'index'     => 'time',
            'type'      => 'datetime',
        ));

        $this->addColumn('ip', array(
            'header'    => 'IP',
            'index'     => 'ip',
            'type'      => 'text',
        ));

        $this->addColumn('event', array(
            'header'    => 'Event',
            'index'     => 'event_label',
            'type'      => 'text',
        ));

        $this->addColumn('user', array(
            'header'    => 'User',
            'index'     => 'username',
            'type'      => 'string',
            'sortable'  => false,
            'filter'    => false
        ));

        $this->addColumn('action', array(
            'header'    => 'Action',
            'index'     => 'action',
            'type'      => 'string',
            'sortable'  => false,
            'filter'    => false
        ));


        return $this;
    }
}
