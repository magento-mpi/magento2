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
 * Default Total Row Renderer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create\Totals;

class DefaultTotals extends \Magento\Sales\Block\Adminhtml\Order\Create\Totals
{
    protected $_template = 'Magento_Sales::order/create/totals/default.phtml';

    /**
     * Retrieve quote session object
     *
     * @return \Magento\Backend\Model\Session\Quote
     */
    protected function _getSession()
    {
        return $this->_sessionQuote;
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
