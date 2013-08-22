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
 * Adminhtml dashboard totals bar
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Dashboard_Totals extends Magento_Adminhtml_Block_Dashboard_Bar
{
    protected $_template = 'dashboard/totalbar.phtml';

    protected function _prepareLayout()
    {
        if (!Mage::helper('Magento_Core_Helper_Data')->isModuleEnabled('Magento_Reports')) {
            return $this;
        }
        $isFilter = $this->getRequest()->getParam('store') || $this->getRequest()->getParam('website') || $this->getRequest()->getParam('group');
        $period = $this->getRequest()->getParam('period', '24h');

        /* @var $collection Magento_Reports_Model_Resource_Order_Collection */
        $collection = Mage::getResourceModel('Magento_Reports_Model_Resource_Order_Collection')
            ->addCreateAtPeriodFilter($period)
            ->calculateTotals($isFilter);

        if ($this->getRequest()->getParam('store')) {
            $collection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
        } else if ($this->getRequest()->getParam('website')){
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $collection->addFieldToFilter('store_id', array('in' => $storeIds));
        } else if ($this->getRequest()->getParam('group')){
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $collection->addFieldToFilter('store_id', array('in' => $storeIds));
        } elseif (!$collection->isLive()) {
            $collection->addFieldToFilter('store_id',
                array('eq' => Mage::app()->getStore(Magento_Core_Model_Store::ADMIN_CODE)->getId())
            );
        }

        $collection->load();

        $totals = $collection->getFirstItem();

        $this->addTotal(__('Revenue'), $totals->getRevenue());
        $this->addTotal(__('Tax'), $totals->getTax());
        $this->addTotal(__('Shipping'), $totals->getShipping());
        $this->addTotal(__('Quantity'), $totals->getQuantity()*1, true);
    }
}
