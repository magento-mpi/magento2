<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Price;

use Magento\Catalog\Model\Layer\Category;
use Magento\Catalog\Model\Resource\Layer\Filter\Price as PriceResource;
use Magento\Framework\Search\Dynamic\IntervalInterface;
use Magento\Framework\StoreManagerInterface;

class Interval implements IntervalInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Category
     */
    private $layer;

    /**
     * @param StoreManagerInterface $storeManager
     * @param Category $layer
     */
    public function __construct(StoreManagerInterface $storeManager, Category $layer)
    {
        $this->storeManager = $storeManager;
        $this->layer = $layer;
    }
    /**
     * {@inheritdoc}
     */
    public function load($limit, $offset = null, $lower = null, $upper = null)
    {
        $lower = $this->prepareComparingValue($lower);
        $upper = $this->prepareComparingValue($upper);
        if (!is_null($upper)) {
            $upper -= PriceResource::MIN_POSSIBLE_PRICE / 10;
        }
        $result = $this->layer->getProductCollection()->getPriceData($lower, $upper, $limit, $offset);
        if (!$result) {
            return $result;
        }
        foreach ($result as &$v) {
            $v = round((double)$v * $this->getCurrencyRate(), 2);
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function loadPrevious($data, $index, $lower = null)
    {
        $originLowerPrice = $lower;
        $lower = $this->prepareComparingValue($lower);
        $data = $this->prepareComparingValue($data);
        if (!is_null($data)) {
            $data -= PriceResource::MIN_POSSIBLE_PRICE / 10;
        }
        $countLess = $this->layer->getProductCollection()->getPriceData($lower, $data, null, null, true);
        if (!$countLess) {
            return false;
        }

        return $this->load($index - $countLess + 1, $countLess - 1, $originLowerPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function loadNext($data, $rightIndex, $upper = null)
    {
        $lowerPrice = $this->prepareComparingValue($data);
        $data = $this->prepareComparingValue($data, false);
        $upper = $this->prepareComparingValue($upper);
        if (!is_null($data)) {
            $data += PriceResource::MIN_POSSIBLE_PRICE / 10;
        }
        if (!is_null($upper)) {
            $upper -= PriceResource::MIN_POSSIBLE_PRICE / 10;
        }
        $countGreater = $this->layer->getProductCollection()->getPriceData($data, $upper, null, null, true);
        if (!$countGreater) {
            return false;
        }

        $result = $this->layer->getProductCollection()->getPriceData(
            $lowerPrice,
            $upper,
            $rightIndex - $countGreater + 1,
            $countGreater - 1,
            false,
            'desc'
        );
        if (!$result) {
            return $result;
        }
        foreach ($result as &$v) {
            $v = round((double)$v * $this->getCurrencyRate(), 2);
        }
        return $result;
    }

    /**
     * Retrieve active currency rate for filter
     *
     * @return float
     */
    private function getCurrencyRate()
    {
        $rate = $rate = $this->storeManager->getStore()->getCurrentCurrencyRate();
        if (!$rate) {
            $rate = 1;
        }
        return $rate;
    }

    /**
     * Get comparing value according to currency rate
     *
     * @param float|null $value
     * @param bool $decrease
     * @return float|null
     */
    private function prepareComparingValue($value, $decrease = true)
    {
        if (is_null($value)) {
            return $value;
        }

        if ($decrease) {
            $value -= PriceResource::MIN_POSSIBLE_PRICE / 2;
        } else {
            $value += PriceResource::MIN_POSSIBLE_PRICE / 2;
        }

        $value /= $this->getCurrencyRate();
        if ($value < 0) {
            $value = null;
        }

        return $value;
    }
}
