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

/**
 * Adminhtml sales report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Dmytro Vasylenko <dimav@varien.com>
 */
class Mage_Adminhtml_Block_Report_Sales_Grid extends Mage_Adminhtml_Block_Report_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('gridSales');
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('reports/order_collection');
    }

    protected function _prepareColumns()
    {
        $this->addColumn('orders', array(
            'header'    =>Mage::helper('reports')->__('Number of Orders'),
            'index'     =>'orders',
            'total'     =>'sum'
        ));

        $this->addColumn('items', array(
            'header'    =>Mage::helper('reports')->__('Items Ordered'),
            'index'     =>'items',
            'total'     =>'sum'
        ));

        $this->addColumn('subtotal', array(
            'header'    =>Mage::helper('reports')->__('Subtotal'),
            'type'      =>'currency',
            'currency_code' => (string) Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode(),
            'index'     =>'subtotal',
            'total'     =>'sum'
        ));

        $this->addColumn('tax', array(
            'header'    =>Mage::helper('reports')->__('Tax'),
            'type'      =>'currency',
            'currency_code' => (string) Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode(),
            'index'     =>'tax',
            'total'     =>'sum'
        ));

        $this->addColumn('shipping', array(
            'header'    =>Mage::helper('reports')->__('Shipping'),
            'type'      =>'currency',
            'currency_code' => (string) Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode(),
            'index'     =>'shipping',
            'total'     =>'sum'
        ));

        $this->addColumn('discount', array(
            'header'    =>Mage::helper('reports')->__('Discounts'),
            'type'      =>'currency',
            'currency_code' => (string) Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode(),
            'index'     =>'discount',
            'total'     =>'sum'
        ));

        $this->addColumn('total', array(
            'header'    =>Mage::helper('reports')->__('Total'),
            'type'      =>'currency',
            'currency_code' => (string) Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode(),
            'index'     =>'total',
            'total'     =>'sum'
        ));

        $this->addColumn('invoiced', array(
            'header'    =>Mage::helper('reports')->__('Invoiced'),
            'type'      =>'currency',
            'currency_code' => (string) Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode(),
            'index'     =>'invoiced',
            'total'     =>'sum'
        ));

        $this->addColumn('refunded', array(
            'header'    =>Mage::helper('reports')->__('Refunded'),
            'type'      =>'currency',
            'currency_code' => (string) Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode(),
            'index'     =>'refunded',
            'total'     =>'sum'
        ));

        $this->addExportType('*/*/exportSalesCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportSalesExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }
}