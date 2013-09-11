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
 * Default Total Row Renderer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Order\Create\Totals;

class DefaultTotals extends \Magento\Adminhtml\Block\Sales\Order\Create\Totals
{
    protected $_template = 'Magento_Adminhtml::sales/order/create/totals/default.phtml';

    /**
     * Retrieve quote session object
     *
     * @return \Magento\Adminhtml\Model\Session\Quote
     */
    protected function _getSession()
    {
        return \Mage::getSingleton('Magento\Adminhtml\Model\Session\Quote');
    }

    /**
     * Retrieve store model object
     *
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        return $this->_getSession()->getStore();
    }

    public function formatPrice($value)
    {
        return $this->getStore()->formatPrice($value);
    }
}
