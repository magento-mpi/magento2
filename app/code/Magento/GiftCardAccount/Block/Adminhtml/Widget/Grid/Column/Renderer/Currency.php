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
    protected static $_websiteBaseCurrencyCodes = array();

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Directory\Model\Currency\DefaultLocator $currencyLocator
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Directory\Model\Currency\DefaultLocator $currencyLocator,
        array $data = array()
    ) {
        parent::__construct($context, $storeManager, $locale, $currencyLocator, $data);
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
