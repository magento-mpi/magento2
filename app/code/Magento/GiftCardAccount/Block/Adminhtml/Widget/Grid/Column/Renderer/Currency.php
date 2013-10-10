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

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Core\Model\App $app,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Directory\Model\Currency\DefaultLocator $currencyLocator,
        \Magento\Core\Model\StoreManager $storeManager,
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
