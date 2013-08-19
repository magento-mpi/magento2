<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml dashboard most recent customers grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Dashboard_Tab_Customers_Newest extends Magento_Adminhtml_Block_Dashboard_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('customersNewestGrid');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Magento_Reports_Model_Resource_Customer_Collection')
            ->addCustomerName();

        $storeFilter = 0;
        if ($this->getParam('store')) {
            $collection->addAttributeToFilter('store_id', $this->getParam('store'));
            $storeFilter = 1;
        } else if ($this->getParam('website')){
            $storeIds = Mage::app()->getWebsite($this->getParam('website'))->getStoreIds();
            $collection->addAttributeToFilter('store_id', array('in' => $storeIds));
        } else if ($this->getParam('group')){
            $storeIds = Mage::app()->getGroup($this->getParam('group'))->getStoreIds();
            $collection->addAttributeToFilter('store_id', array('in' => $storeIds));
        }

        $collection->addOrdersStatistics($storeFilter)
            ->orderByCustomerRegistration();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => __('Customer'),
            'sortable'  => false,
            'index'     => 'name'
        ));

        $this->addColumn('orders_count', array(
            'header'    => __('Orders'),
            'sortable'  => false,
            'index'     => 'orders_count',
            'type'      => 'number'
        ));

        $baseCurrencyCode = (string) Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode();

        $this->addColumn('orders_avg_amount', array(
            'header'    => __('Average'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'orders_avg_amount',
            'renderer'  =>'Magento_Adminhtml_Block_Report_Grid_Column_Renderer_Currency'
        ));

        $this->addColumn('orders_sum_amount', array(
            'header'    => __('Total'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'orders_sum_amount',
            'renderer'  =>'Magento_Adminhtml_Block_Report_Grid_Column_Renderer_Currency'
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/customer/edit', array('id'=>$row->getId()));
    }
}
