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
 * Adminhtml dashboard sales statistics bar
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Dashboard_Sales extends Magento_Adminhtml_Block_Dashboard_Bar
{
    protected $_template = 'dashboard/salebar.phtml';

    protected function _prepareLayout()
    {
        if (!$this->_coreData->isModuleEnabled('Magento_Reports')) {
            return $this;
        }
        $isFilter = $this->getRequest()->getParam('store')
            || $this->getRequest()->getParam('website')
            || $this->getRequest()->getParam('group');

        $collection = Mage::getResourceModel('Magento_Reports_Model_Resource_Order_Collection')
            ->calculateSales($isFilter);

        if ($this->getRequest()->getParam('store')) {
            $collection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
        } else if ($this->getRequest()->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $collection->addFieldToFilter('store_id', array('in' => $storeIds));
        } else if ($this->getRequest()->getParam('group')) {
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $collection->addFieldToFilter('store_id', array('in' => $storeIds));
        }

        $collection->load();
        $sales = $collection->getFirstItem();

        $this->addTotal(__('Lifetime Sales'), $sales->getLifetime());
        $this->addTotal(__('Average Orders'), $sales->getAverage());
    }
}
