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
 * @package    Mage_Oscommerce
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * osCommerce convert grid block
 *
 * @author     Kyaw Soe Lynn Maung <vincent@varien.com>
 */

class Mage_Oscommerce_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('oscommerceOrderGrid');
        $this->setDefaultSort('id');

    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('oscommerce/oscommerce_order')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('order_id', array(
            'header'    =>Mage::helper('adminhtml')->__('Order #'),
            'width'     =>'50px',
            'index'     =>'osc_magento_id',
        ));

        $this->addColumn('billing_name', array(
            'header'    =>Mage::helper('adminhtml')->__('Billing to Name'),
            'index'     =>'billing_name',
        ));
        
        $this->addColumn('delivery_name', array(
            'header'    =>Mage::helper('adminhtml')->__('Ship to Name'),
            'index'     =>'delivery_name',
        ));
        
        $this->addColumn('orders_total', array(
            'header' =>Mage::helper('adminhtml')->__('Order Total'),
            'width' =>'50px',
            'index' =>'orders_total',
            'type' => 'currency',
            'currency'=>'USD'
        ));     
                        
        $this->addColumn('date_purchased', array(
            'header'    =>Mage::helper('adminhtml')->__('Purchased On'),
            'index'     =>'date_purchased',
            'width'     =>'200px',
            'type' => 'datetime'
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('order_id'=>$row->getId()));
    }

}