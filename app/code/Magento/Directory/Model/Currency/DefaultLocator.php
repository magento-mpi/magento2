<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Directory\Model\Currency;

class DefaultLocator
{
    /**
     * Config object
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_configuration;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\App\Config\ScopeConfigInterface $configuration
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\App\Config\ScopeConfigInterface $configuration,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_configuration = $configuration;
        $this->_storeManager = $storeManager;
    }

    /**
     * Retrieve default currency for selected store, website or website group
     * @todo: Refactor to ScopeDefiner
     * @param \Magento\App\RequestInterface $request
     * @return string
     */
    public function getDefaultCurrency(\Magento\App\RequestInterface $request)
    {
        if ($request->getParam('store')) {
            $store = $request->getParam('store');
            $currencyCode = $this->_storeManager->getStore($store)->getBaseCurrencyCode();
        } else {
            if ($request->getParam('website')) {
                $website = $request->getParam('website');
                $currencyCode = $this->_storeManager->getWebsite($website)->getBaseCurrencyCode();
            } else {
                if ($request->getParam('group')) {
                    $group = $request->getParam('group');
                    $currencyCode = $this->_storeManager->getGroup($group)->getWebsite()->getBaseCurrencyCode();
                } else {
                    $currencyCode = $this->_configuration->getValue(
                        \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                        'default'
                    );
                }
            }
        }

        return $currencyCode;
    }
}
