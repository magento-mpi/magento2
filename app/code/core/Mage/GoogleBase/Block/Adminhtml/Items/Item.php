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
 * @category   Mage
 * @package    Mage_GoogleBase
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml GoogleBase Item Types grid
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleBase_Block_Adminhtml_Items_Item extends Mage_Adminhtml_Block_Widget_Grid
{


    public function __construct()
    {
        parent::__construct();
        $this->setId('items');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('googlebase/item_collection');
        $store = $this->_getStore();
        $collection->addStoreFilterId($store->getId());
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('googlebase')->__('Product Name'),
                'width' => '30%',
                'index'     => 'name',
        ));

        $this->addColumn('gbase_item_id',
            array(
                'header'=> Mage::helper('googlebase')->__('Base Item ID'),
                'width' => '150px',
                'index'     => 'gbase_item_id',
        ));

        $this->addColumn('gbase_itemtype',
            array(
                'header'=> Mage::helper('googlebase')->__('Base Item Type'),
                'width' => '150px',
                'index'     => 'gbase_itemtype',
        ));

//        $this->addColumn('published',
//            array(
//                'header'=> Mage::helper('googlebase')->__('Published'),
//                'type' => 'datetime',
//                'width' => '100px',
//                'index'     => 'published',
//        ));

        $this->addColumn('expires',
            array(
                'header'=> Mage::helper('googlebase')->__('Expires'),
                'type' => 'datetime',
                'width' => '100px',
                'index'     => 'expires',
        ));

        $this->addColumn('impr',
            array(
                'header'=> Mage::helper('googlebase')->__('Impr.'),
                'width' => '150px',
                'index'     => 'impr',
        ));

        $this->addColumn('clicks',
            array(
                'header'=> Mage::helper('googlebase')->__('Clicks'),
                'width' => '150px',
                'index'     => 'clicks',
        ));

        $this->addColumn('views',
            array(
                'header'=> Mage::helper('googlebase')->__('Page views'),
                'width' => '150px',
                'index'     => 'views',
        ));

        $this->addColumn('active',
            array(
                'header'    => Mage::helper('googlebase')->__('Active'),
                'width'     => '150px',
                'type'      => 'options',
                'width'     => '70px',
                'options'   => Mage::getSingleton('googlebase/source_statuses')->getStatuses(),
                'index'     => 'is_hidden',
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('item_id');
        $this->getMassactionBlock()->setFormFieldName('item');
//        $this->setMassactionIdFieldOnlyIndexValue(true);
//
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('googlebase')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete', array('_current'=>true)),
             'confirm'  => Mage::helper('googlebase')->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('publish', array(
             'label'    => Mage::helper('googlebase')->__('Publish'),
             'url'      => $this->getUrl('*/*/massPublish', array('_current'=>true))
        ));

        $this->getMassactionBlock()->addItem('unpublish', array(
             'label'    => Mage::helper('googlebase')->__('Hide'),
             'url'      => $this->getUrl('*/*/massHide', array('_current'=>true))
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
}