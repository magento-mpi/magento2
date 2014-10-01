<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Price;

use Magento\Framework\Search\Price\IntervalInterface;

class Interval implements IntervalInterface
{
    /**
     * @var \Magento\Catalog\Model\Resource\Layer\Filter\Price
     */
    private $resource;

    /**
     * @param \Magento\Catalog\Model\Resource\Layer\Filter\Price $resource
     */
    public function __construct(\Magento\Catalog\Model\Resource\Layer\Filter\Price $resource)
    {
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function load($limit, $offset = null, $lowerPrice = null, $upperPrice = null)
    {
        $prices = $this->resource->loadPrices($limit, $offset, $lowerPrice, $upperPrice);
        return $this->arrayValuesToFloat($prices);
    }

    /**
     * {@inheritdoc}
     */
    public function loadPrevious($price, $index, $lowerPrice = null)
    {
        $prices = $this->resource->loadPreviousPrices($price, $index, $lowerPrice);
        return $this->arrayValuesToFloat($prices);
    }

    /**
     * {@inheritdoc}
     */
    public function loadNext($price, $rightIndex, $upperPrice = null)
    {
        $prices = $this->resource->loadNextPrices($price, $rightIndex, $upperPrice);
        return $this->arrayValuesToFloat($prices);
    }

    /**
     * @param array $prices
     * @return array
     */
    private function arrayValuesToFloat($prices)
    {
        $returnPrices = [];
        if (is_array($prices) && !empty($prices)) {
            $returnPrices = array_map('floatval', $prices);
        }
        return $returnPrices;
    }
}
