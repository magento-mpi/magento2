<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CurrencySymbol
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Currency Symbol helper
 *
 * @category   Magento
 * @package    Magento_CurrencySymbol
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CurrencySymbol\Helper;

class Data extends \Magento\Core\Helper\Data
{
    /**
     * @var \Magento\CurrencySymbol\Model\System\Currencysymbol\Factory
     */
    protected $_symbolFactory;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\Locale $locale
     * @param \Magento\App\State $appState
     * @param \Magento\CurrencySymbol\Model\System\Currencysymbol\Factory $symbolFactory
     * @param bool $dbCompatibleMode
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\Locale $locale,
        \Magento\App\State $appState,
        \Magento\CurrencySymbol\Model\System\Currencysymbol\Factory $symbolFactory,
        $dbCompatibleMode = true
    ) {
        $this->_symbolFactory = $symbolFactory;
        parent::__construct(
            $context,
            $config,
            $coreStoreConfig,
            $storeManager,
            $locale,
            $appState,
            $dbCompatibleMode
        );
    }

    /**
     * Get currency display options
     *
     * @param string $baseCode
     * @return array
     */
    public function getCurrencyOptions($baseCode)
    {
        $currencyOptions = array();
        $currencySymbol = $this->_symbolFactory->create();
        if($currencySymbol) {
            $customCurrencySymbol = $currencySymbol->getCurrencySymbol($baseCode);

            if ($customCurrencySymbol) {
                $currencyOptions['symbol']  = $customCurrencySymbol;
                $currencyOptions['display'] = \Zend_Currency::USE_SYMBOL;
            }
        }

        return $currencyOptions;
    }
}
