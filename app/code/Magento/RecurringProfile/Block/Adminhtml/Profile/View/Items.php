<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml recurring profile items grid
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\RecurringProfile\Block\Adminhtml\Profile\View;

class Items extends \Magento\RecurringProfile\Block\Adminhtml\Items\AbstractItems
{
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            throw new \Magento\Core\Exception(__('Invalid parent block for this block'));
        }
        parent::_beforeToHtml();
    }

    /**
     * Return current recurring profile
     *
     * @return \Magento\RecurringProfile\Model\Profile
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
     * Retrieve formatted price
     *
     * @param   decimal $value
     * @return  string
     */
    public function formatPrice($value)
    {
        $store = $this->_storeManager->getStore($this->_getRecurringProfile()->getStore());
        return $store->formatPrice($value);
    }
}

