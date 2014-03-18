<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Block\Adminhtml\Payment\View;

/**
 * Adminhtml recurring payment items grid
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Items extends \Magento\Sales\Block\Adminhtml\Items\AbstractItems
{
    /**
     * Retrieve required options from parent
     *
     * @return void
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            throw new \Magento\Model\Exception(__('Invalid parent block for this block'));
        }
        parent::_beforeToHtml();
    }

    /**
     * Return current recurring payment
     *
     * @return \Magento\RecurringPayment\Model\Payment
     */
    public function _getRecurringPayment()
    {
        return $this->_coreRegistry->registry('current_recurring_payment');
    }

    /**
     * Retrieve recurring payment item
     *
     * @return \Magento\Sales\Model\Order\Item
     */
    public function getItem()
    {
        return $this->_getRecurringPayment()->getItem();
    }

    /**
     * Retrieve formatted price
     *
     * @param   float $value
     * @return  string
     */
    public function formatPrice($value)
    {
        $store = $this->_storeManager->getStore($this->_getRecurringPayment()->getStore());
        return $store->formatPrice($value);
    }
}

