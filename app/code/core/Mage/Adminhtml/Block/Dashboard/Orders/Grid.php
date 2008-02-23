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
 * Adminhtml dashboard recent orders grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author	   Dmytro Vasylenko <dmitriy.vasilenko@varien.com>
 */

class Mage_Adminhtml_Block_Dashboard_Orders_Grid extends Mage_Adminhtml_Block_Dashboard_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('lastOrdersGrid');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('reports/order_collection')
            ->addAttributeToSelect('*')
            ->addItemCountExpr()
            ->addExpressionAttributeToSelect('customer',
                'CONCAT({{customer_firstname}}," ",{{customer_lastname}})',
                array(
                    'customer_firstname',
                    'customer_lastname'
                ))
            ->setOrder('created_at');

        if($this->getParam('store') || $this->getParam('website')) {
            if ($this->getParam('store')) {
                $collection->addAttributeToFilter('store_id', $this->getParam('store'));
            } else if ($this->getParam('website')){
                $storeIds = Mage::app()->getWebsite($this->getParam('website'))->getStoreIds();
                $collection->addAttributeToFilter('store_id', array('in' => implode(',', $storeIds)));
            } else if ($this->getParam('group')){
                $storeIds = Mage::app()->getGroup($this->getParam('group'))->getStoreIds();
                $collection->addAttributeToFilter('store_id', array('in' => implode(',', $storeIds)));
            }

            $collection->addExpressionAttributeToSelect('revenue',
                '({{grand_total}}/{{store_to_order_rate}})',
                array('grand_total', 'store_to_order_rate'));
        } else {
            $collection->addExpressionAttributeToSelect('revenue',
                '({{grand_total}}*{{store_to_base_rate}}/{{store_to_order_rate}})',
                array('grand_total', 'store_to_base_rate', 'store_to_order_rate'));
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

        $baseCurrencyCode = Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode();

        $this->addColumn('total', array(
            'header'    => $this->__('Grand Total'),
            'width'     => '50px',
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'revenue'
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }
}