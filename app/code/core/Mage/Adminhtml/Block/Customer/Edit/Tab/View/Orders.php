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
 * Adminhtml customer recent orders grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Customer_Edit_Tab_View_Orders extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_view_orders_grid');
        $this->setDefaultSort('created_at', 'desc');
        $this->setSortable(false);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    protected function _preparePage()
    {
        $this->getCollection()
            ->setPageSize(5)
            ->setCurPage(1);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('increment_id')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('grand_total')
            ->addAttributeToSelect('order_currency_code')
            ->addAttributeToSelect('store_id')
            ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id')
            ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id')
            ->addAttributeToFilter('customer_id', Mage::registry('current_customer')->getId())
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('increment_id', array(
            'header' => __('Order #'),
            'align' => 'center',
            'index' => 'increment_id',
            'width' => '100px',
        ));

        $this->addColumn('created_at', array(
            'header' => __('Purchased at'),
            'index' => 'created_at',
            'type' => 'datetime',
        ));

        $this->addColumn('shipping_firstname', array(
            'header' => __('Ship to First name'),
            'index' => 'shipping_firstname',
        ));

        $this->addColumn('shipping_lastname', array(
            'header' => __('Ship to Last name'),
            'index' => 'shipping_lastname',
        ));

        $this->addColumn('grand_total', array(
            'header' => __('Grand Total'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));

        $stores = Mage::getResourceModel('core/store_collection')->setWithoutDefaultFilter()->load()->toOptionHash();

        $this->addColumn('store_id', array(
            'header' => __('Bought From'),
            'index' => 'store_id',
            'type' => 'options',
            'options' => $stores,
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return Mage::getUrl('*/sales_order/view', array('order_id' => $row->getId()));
    }

    public function getHeadersVisibility()
    {
        return ($this->getCollection()->getSize() > 0);
    }

}