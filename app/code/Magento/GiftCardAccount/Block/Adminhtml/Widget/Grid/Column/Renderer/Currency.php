<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCardAccount_Block_Adminhtml_Widget_Grid_Column_Renderer_Currency
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Currency
{
    protected static $_websiteBaseCurrencyCodes = array();

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    public function __construct(
        Magento_Backend_Block_Context $context,
        Magento_Core_Model_App $app,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Directory_Model_Currency_DefaultLocator $currencyLocator,
        Magento_Core_Model_StoreManager $storeManager,
        array $data = array()
    ) {
        parent::__construct($context, $app, $locale, $currencyLocator, $data);
        $this->_storeManager = $storeManager;
    }


    protected function _getCurrencyCode($row)
    {
        $websiteId = $row->getWebsiteId();
        $code = $this->_storeManager->getWebsite($websiteId)->getBaseCurrencyCode();
        self::$_websiteBaseCurrencyCodes[$websiteId] = $code;

        return self::$_websiteBaseCurrencyCodes[$websiteId];
    }

    protected function _getRate($row)
    {
        return 1;
    }
}
