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
class Mage_Xmlconnect_Block_Adminhtml_Mobile_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('mobile_apps_grid');
        $this->setDefaultSort('application_id');
        $this->setDefaultDir('ASC');
    }

    /**
     * Initialize grid data collection
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('xmlconnect/application')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Delcare grid columns
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => Mage::helper('xmlconnect')->__('App Name'),
            'align'     => 'left',
            'index'     => 'name',
        ));

        $this->addColumn('code', array(
            'header'    => Mage::helper('xmlconnect')->__('Code'),
            'align'     => 'left',
            'index'     => 'code',
            'width'     => '200',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('xmlconnect')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'width'         => '250',
            ));
        }

        $this->addColumn('status', array(
            'header'    => Mage::helper('xmlconnect')->__('Status'),
            'index'     => 'status',
            'width'     => '50',
            'renderer'  => 'xmlconnect/adminhtml_mobile_grid_renderer_bool',
            'align'     => 'center',
        ));

        $this->addColumn('active_from', array(
            'header'    => Mage::helper('xmlconnect')->__('Active From'),
            'index'     => 'active_from',
            'type'      => 'date',
            'width'     => '150',
        ));

        $this->addColumn('active_to', array(
            'header'    => Mage::helper('xmlconnect')->__('Active To'),
            'index'     => 'active_to',
            'type'      => 'date',
            'width'     => '150',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('application_id' => $row->getId()));
    }
}
