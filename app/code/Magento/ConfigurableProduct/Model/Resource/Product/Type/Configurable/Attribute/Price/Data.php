<?php
/**
 * Catalog Configurable Product Attribute Collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute\Price;

/**
 * Class Data
 * Caching price for performance improvements of Configurable product loading
 * (Avoiding using static properties of Attribute Collection resource)
 * @todo Configurable Product models/resouces should be refactored with introduction of new entity(es),
 * such as ConfigurableOption (or OptionPrice, OptionPriceCollection)
 */
class Data {
    /**
     * @var array
     */
    protected $prices;

    /**
     * @param int $productId
     * @param array $priceData
     */
    public function setProductPrice($productId, array $priceData)
    {
        $this->prices[$productId] = $priceData;
    }

    /**
     * @param int $productId
     * @return array|bool
     */
    public function getProductPrice($productId)
    {
        return isset($this->prices[$productId]) ? $this->prices[$productId] : false;
    }
} 