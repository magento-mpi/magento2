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
 * Adminhtml dashboard recent orders grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Dashboard_Orders_Grid extends Magento_Adminhtml_Block_Dashboard_Grid
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('lastOrdersGrid');
    }

    protected function _prepareCollection()
    {
        if (!$this->_coreData->isModuleEnabled('Magento_Reports')) {
            return $this;
        }
        $collection = Mage::getResourceModel('Magento_Reports_Model_Resource_Order_Collection')
            ->addItemCountExpr()
            ->joinCustomerName('customer')
            ->orderByCreatedAt();

        if ($this->getParam('store') || $this->getParam('website') || $this->getParam('group')) {
            if ($this->getParam('store')) {
                $collection->addAttributeToFilter('store_id', $this->getParam('store'));
            } else if ($this->getParam('website')) {
                $storeIds = Mage::app()->getWebsite($this->getParam('website'))->getStoreIds();
                $collection->addAttributeToFilter('store_id', array('in' => $storeIds));
            } else if ($this->getParam('group')) {
                $storeIds = Mage::app()->getGroup($this->getParam('group'))->getStoreIds();
                $collection->addAttributeToFilter('store_id', array('in' => $storeIds));
            }

            $collection->addRevenueToSelect();
        } else {
            $collection->addRevenueToSelect(true);
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepares page sizes for dashboard grid with las 5 orders
     *
     * @return void
     */
    protected function _preparePage()
    {
        $this->getCollection()->setPageSize($this->getParam($this->getVarNameLimit(), $this->_defaultLimit));
//        Remove count of total orders
//        $this->getCollection()->setCurPage($this->getParam($this->getVarNamePage(), $this->_defaultPage));
    }

    protected function _prepareColumns()
    {
        $this->addColumn('customer', array(
            'header'    => __('Customer'),
            'sortable'  => false,
            'index'     => 'customer',
            'default'   => __('Guest'),
        ));

        $this->addColumn('items', array(
            'header'    => __('Items'),
            'align'     => 'right',
            'type'      => 'number',
            'sortable'  => false,
            'index'     => 'items_count'
        ));

        $baseCurrencyCode = Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode();

        $this->addColumn('total', array(
            'header'    => __('Grand Total'),
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

    public function getRowUrl($row)
    {
        return $this->getUrl('*/sales_order/view', array('order_id'=>$row->getId()));
    }
}
