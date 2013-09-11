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
 * Subtotal Total Row Renderer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Sales\Order\Create\Totals;

class Grandtotal extends \Magento\Adminhtml\Block\Sales\Order\Create\Totals\DefaultTotals
{
    protected $_template = 'sales/order/create/totals/grandtotal.phtml';

    public function includeTax()
    {
        return \Mage::getSingleton('Magento\Tax\Model\Config')->displayCartTaxWithGrandTotal();
    }

    public function getTotalExclTax()
    {
        $excl = $this->getTotal()->getAddress()->getGrandTotal()-$this->getTotal()->getAddress()->getTaxAmount();
        $excl = max($excl, 0);
        return $excl;
    }
}
