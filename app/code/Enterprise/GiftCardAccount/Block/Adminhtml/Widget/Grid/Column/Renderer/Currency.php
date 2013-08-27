<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCardAccount_Block_Adminhtml_Widget_Grid_Column_Renderer_Currency
extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Currency
{
    protected static $_websiteBaseCurrencyCodes = array();

    protected function _getCurrencyCode($row)
    {
        $websiteId = $row->getWebsiteId();
        $code = Mage::app()->getWebsite($websiteId)->getBaseCurrencyCode();
        self::$_websiteBaseCurrencyCodes[$websiteId] = $code;

        return self::$_websiteBaseCurrencyCodes[$websiteId];
    }

    protected function _getRate($row)
    {
        return 1;
    }
}
