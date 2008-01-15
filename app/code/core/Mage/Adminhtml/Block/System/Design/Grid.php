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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Adminhtml_Block_System_Design_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('designGrid');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');
    }

    protected function _prepareCollection()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);

        $collection = Mage::getResourceModel('core/design_collection')
            ->joinStore();

        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('store',
            array(
                'header'=> Mage::helper('catalog')->__('Store'),
                'width' => '100px',
                'filter'    => 'adminhtml/system_design_grid_filter_store',
                'index'     => 'name',
        ));
        $this->addColumn('package',
            array(
                'header'=> Mage::helper('catalog')->__('Package'),
                'width' => '100px',
                'index'     => 'package',
        ));
        $this->addColumn('theme',
            array(
                'header'=> Mage::helper('catalog')->__('Theme'),
                'width' => '100px',
                'index'     => 'theme',
        ));
        $this->addColumn('date_from', array(
            'header'    => Mage::helper('catalogrule')->__('Date From'),
            'align'     => 'left',
            'width'     => '100px',
            'type'      => 'date',
            'index'     => 'date_from',
        ));

        $this->addColumn('date_to', array(
            'header'    => Mage::helper('catalogrule')->__('Date To'),
            'align'     => 'left',
            'width'     => '100px',
            'type'      => 'date',
            'index'     => 'date_to',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array('base'=>'*/*/edit'),
                        'field'   => 'id'
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));

        return parent::_prepareColumns();
    }


    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}
