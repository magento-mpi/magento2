<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Layer\Filter;

/**
 * Layer decimal filter
 */
class Decimal extends \Magento\Catalog\Model\Layer\Filter\Decimal
{
    /**
     * @param ItemFactory $filterItemFactory
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Resource\Layer\Filter\DecimalFactory $filterDecimalFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Search\Request\Builder $requestBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Resource\Layer\Filter\DecimalFactory $filterDecimalFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Search\Request\Builder $requestBuilder,
        array $data = array()
    ) {
        parent::__construct($filterItemFactory, $storeManager, $layer, $filterDecimalFactory, $priceCurrency, $data);
    }
}
