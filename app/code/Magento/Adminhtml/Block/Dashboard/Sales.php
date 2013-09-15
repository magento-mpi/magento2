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

namespace Magento\Adminhtml\Block\Dashboard;

class Sales extends \Magento\Adminhtml\Block\Dashboard\Bar
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

        $collection = \Mage::getResourceModel('Magento\Reports\Model\Resource\Order\Collection')
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
