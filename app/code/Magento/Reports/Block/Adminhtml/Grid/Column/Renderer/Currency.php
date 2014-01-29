<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml grid item renderer currency
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Reports\Block\Adminhtml\Grid\Column\Renderer;

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
        $data = $this->_locale->currency($currency_code)->toCurrency($data);
        return $data;
    }
}
