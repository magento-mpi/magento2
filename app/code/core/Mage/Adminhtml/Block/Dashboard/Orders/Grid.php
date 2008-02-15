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

class Mage_Adminhtml_Block_Dashboard_Orders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('lastOrdersGrid');
        $this->setTemplate('dashboard/grid.phtml');
        $this->setDefaultLimit(5);
    }

    protected function _prepareCollection()
    {

        $collection = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToSelect('*')
            ->addItemCountExpr()
            ->addExpressionAttributeToSelect('customer', 'CONCAT({{customer_firstname}}," ",{{customer_lastname}})', array(
                'customer_firstname',
                'customer_lastname'
            ))
            ->setOrder('created_at');

        if($this->getParam('store')) {
            $collection->addAttributeToFilter('store_id', $this->getParam('store'));
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('customer', array(
            'header'    => $this->__('Customer'),
            'width'     => '150px',
            'sortable'  => false,
            'index'     => 'customer'
        ));

        $this->addColumn('items', array(
            'header'    => $this->__('Items'),
            'sortable'  => false,
            'index'     => 'items_count'
        ));

        $this->addColumn('total', array(
            'header'    => $this->__('Grand Total'),
            'width'     => '50px',
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            //'corrency_code'  => 'USD',
            'currency'  => 'order_currency_code',
            'index'     => 'grand_total'
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }
}