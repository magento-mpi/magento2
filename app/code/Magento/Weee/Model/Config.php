<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Weee\Model;

use Magento\Store\Model\Store;
/**
 * WEEE config model
 */
class Config
{
    /**
     * Enabled config path
     */
    const XML_PATH_FPT_ENABLED = 'tax/weee/enable';

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Tax\Helper\Data $taxData
    ) {
        $this->taxHelper = $taxData;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get weee amount display type on product view page
     *
     * @param   null|string|bool|int|Store $store
     * @return  int
     */
    public function getPriceDisplayType($store = null)
    {
        return $this->scopeConfig->getValue(
            'tax/weee/display',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get weee amount display type on product list page
     *
     * @param   null|string|bool|int|Store $store
     * @return  int
     */
    public function getListPriceDisplayType($store = null)
    {
        return $this->scopeConfig->getValue(
            'tax/weee/display_list',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get weee amount display type in sales modules
     *
     * @param   null|string|bool|int|Store $store
     * @return  int
     */
    public function getSalesPriceDisplayType($store = null)
    {
        return $this->scopeConfig->getValue(
            'tax/weee/display_sales',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get weee amount display type in email templates
     *
     * @param   null|string|bool|int|Store $store
     * @return  int
     */
    public function getEmailPriceDisplayType($store = null)
    {
        return $this->scopeConfig->getValue(
            'tax/weee/display_email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check if weee amount includes tax already
     * Returns true if weee is taxable and catalog price includes tax
     *
     * @param   null|string|bool|int|Store $store
     * @return  bool
     */
    public function isTaxIncluded($store = null)
    {
        $isFPTTaxable = $this->isTaxable($store);

        $isCatalogPriceIncludeTax = $this->taxHelper->priceIncludesTax($store);

        return $isFPTTaxable && $isCatalogPriceIncludeTax;
    }

    /**
     * Check if weee tax amount should be included to subtotal
     *
     * @param   null|string|bool|int|Store $store
     * @return  bool
     */
    public function includeInSubtotal($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            'tax/weee/include_in_subtotal',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check if weee tax amount should be discounted
     *
     * @param   null|string|bool|int|Store $store
     * @return  bool
     */
    public function isDiscounted($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            'tax/weee/discount',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check if weee tax amount should be taxable
     *
     * @param   null|string|bool|int|Store $store
     * @return  bool
     */
    public function isTaxable($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            'tax/weee/apply_vat',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check if fixed taxes are used in system
     *
     * @param Store $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FPT_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

}