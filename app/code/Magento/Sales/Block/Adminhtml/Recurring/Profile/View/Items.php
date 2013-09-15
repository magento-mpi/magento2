<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml recurring profile items grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Recurring\Profile\View;

class Items extends \Magento\Adminhtml\Block\Sales\Items\AbstractItems
{
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            \Mage::throwException(__('Invalid parent block for this block'));
        }
        parent::_beforeToHtml();
    }

    /**
     * Return current recurring profile
     *
     * @return \Magento\Sales\Model\Recurring\Profile
     */
    public function _getRecurringProfile()
    {
        return $this->_coreRegistry->registry('current_recurring_profile');
    }

    /**
     * Retrieve recurring profile item
     *
     * @return \Magento\Sales\Model\Order\Item
     */
    public function getItem()
    {
        return $this->_getRecurringProfile()->getItem();
    }

    /**
     * Retrieve formated price
     *
     * @param   decimal $value
     * @return  string
     */
    public function formatPrice($value)
    {
        $store = \Mage::app()->getStore($this->_getRecurringProfile()->getStore());
        return $store->formatPrice($value);
    }
}

