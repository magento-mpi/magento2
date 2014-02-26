<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Block\Adminhtml\Widget\Grid\Column\Renderer;

class Currency
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Currency
{
    /**
     * @var array
     */
    protected static $_websiteBaseCurrencyCodes = array();

    /**
     * @param \Magento\Object $row
     * @return string
     */
    protected function _getCurrencyCode($row)
    {
        $websiteId = $row->getWebsiteId();
        $code = $this->_storeManager->getWebsite($websiteId)->getBaseCurrencyCode();
        self::$_websiteBaseCurrencyCodes[$websiteId] = $code;

        return self::$_websiteBaseCurrencyCodes[$websiteId];
    }

    /**
     * @param \Magento\Object $row
     * @return float|int
     */
    protected function _getRate($row)
    {
        return 1;
    }
}
