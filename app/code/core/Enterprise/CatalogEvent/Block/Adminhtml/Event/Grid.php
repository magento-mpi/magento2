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
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Events grid
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */
class Enterprise_CatalogEvent_Block_Adminhtml_Event_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('event_id');
        $this->setDefaultDir('ASC');
    }

    /**
     * Prepares events collection
     *
     * @return Enterprise_CatalogEvent_Block_Adminhtml_Event_Grid
     */
    protected function _prepareCollection()
    {
       	$collection = Mage::getModel('enterprise_catalogevent/event')->getCollection()
       		->addCategoryData();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare event grid columns
     *
     * @return Enterprise_CatalogEvent_Block_Adminhtml_Event_Grid
     */
    protected function _prepareColumns()
    {

        $this->addColumn('event_id', array(
            'header'=> Mage::helper('enterprise_catalogevent')->__('ID'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'event_id'
        ));

        $this->addColumn('category', array(
            'header' => Mage::helper('enterprise_catalogevent')->__('Category'),
            'index' => 'category_name',
       		'type'  => 'text'
        ));

        $this->addColumn('date_start', array(
            'header' => Mage::helper('enterprise_catalogevent')->__('Start On'),
            'index' => 'date_start',
            'type' => 'datetime',
        	'filter_time' => true,
            'width' => '150px'
        ));

        $this->addColumn('date_end', array(
            'header' => Mage::helper('enterprise_catalogevent')->__('End On'),
            'index' => 'date_end',
            'type' => 'datetime',
        	'filter_time' => true,
            'width' => '150px'
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('enterprise_catalogevent')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                Enterprise_CatalogEvent_Model_Event::STATUS_UPCOMING => Mage::helper('enterprise_catalogevent')->__('Upcoming'),
                Enterprise_CatalogEvent_Model_Event::STATUS_OPEN 	  => Mage::helper('enterprise_catalogevent')->__('Open'),
                Enterprise_CatalogEvent_Model_Event::STATUS_CLOSED   => Mage::helper('enterprise_catalogevent')->__('Closed')
            ),
            'width' => '140px'
        ));

        $this->addColumn('display_state', array(
            'header' => Mage::helper('enterprise_catalogevent')->__('Display Ticker On'),
            'index' => 'display_state',
            'type' => 'options',
            'renderer' => 'enterprise_catalogevent/adminhtml_event_grid_column_renderer_bitmask',
            'options' => array(
                Enterprise_CatalogEvent_Model_Event::DISPLAY_CATEGORY_PAGE => Mage::helper('enterprise_catalogevent')->__('Category Page'),
                Enterprise_CatalogEvent_Model_Event::DISPLAY_PRODUCT_PAGE  => Mage::helper('enterprise_catalogevent')->__('Product Page')
            )
        ));

        $this->addColumn('actions', array(
            'header'    => $this->helper('enterprise_catalogevent')->__('Action'),
            'width'     => '15px',
            'sortable'  => false,
            'filter'    => false,
            'type'      => 'action',
            'actions'   => array(
                array(
                    'url'       => $this->getUrl('*/*/edit') . 'event_id/$event_id',
                    'caption'   => $this->helper('enterprise_catalogevent')->__('Edit'),
                ),
            )
        ));

        return parent::_prepareColumns();
    }


    /**
     * Grid row event edit url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('event_id' => $row->getId()));
    }
}
