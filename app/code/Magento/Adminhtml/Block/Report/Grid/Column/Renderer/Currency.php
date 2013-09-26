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
 * Adminhtml grid item renderer currency
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Report_Grid_Column_Renderer_Currency
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Currency
{
    /**
     * Locale
     *
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Core_Model_App $app
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Directory_Model_Currency_DefaultLocator $currencyLocator
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Backend_Block_Context $context,
        Magento_Core_Model_App $app,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Directory_Model_Currency_DefaultLocator $currencyLocator,
        array $data = array()
    ) {
        $this->_locale = $locale;
        parent::__construct($context, $app, $locale, $currencyLocator, $data);
    }


    /**
     * Renders grid column
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
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
