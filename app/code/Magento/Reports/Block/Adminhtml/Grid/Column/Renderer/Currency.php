<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Block\Adminhtml\Grid\Column\Renderer;

/**
 * Adminhtml grid item renderer currency
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Currency
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Currency
{
    /**
     * Renders grid column
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $data = $row->getData($this->getColumn()->getIndex());
        $currency_code = $this->_getCurrencyCode($row);

        if (!$currency_code) {
            return $data;
        }

        $data = floatval($data) * $this->_getRate($row);
        $data = sprintf("%f", $data);
        $data = $this->_localeCurrency->getCurrency($currency_code)->toCurrency($data);
        return $data;
    }
}
